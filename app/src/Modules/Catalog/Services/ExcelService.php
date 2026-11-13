<?php

namespace Modules\Catalog\Services;

use Modules\Catalog\Entities\Product;
use Modules\Catalog\Entities\Section;
use Modules\Catalog\Entities\Feature;
use Modules\Catalog\Entities\GroupFeature;
use Modules\Catalog\Entities\Group;
use Modules\Catalog\Entities\Brand;
use Shuchkin\SimpleXLSXGen;
use DB;
use Modules\Settings\Entities\Settings;

class ExcelService
{
    private const MSK_WAREHOUSE_ID = 1; // Склад Москва

    // Возвращает основные данные о всех товарах выбранной категории (включая все характеристики)
    public function productsAllData(int $sectionId)
    {
        // Домен на котором располагаются фотографии товаров
        $shopDomain = \Helper::storefrontUrl();

        // Категория
        $section = Section::find($sectionId);

        // Все бренды
        $brands = Brand::all();

        // Сохраним название бренда по id
        $brandNameById = [];
        foreach ($brands as $brand) {
            $brandNameById[$brand->id] = json_decode($brand->name)->ru;
        }

        // Получаем все товары, которые находятся в категории
        $sql = "SELECT product_id FROM product_section WHERE section_id=?";
        
        // Добавляем фильтр по наличию через подзапрос к таблице products
        if (request()->get('in_stock') == 1) {
            $sql .= " AND product_id IN (SELECT id FROM products WHERE stock_status = 'in_stock')";
        }
        
        $products = DB::select($sql, [$sectionId]);

        // Активные склады для дополнительных колонок остатков
        $activeWarehouses = DB::table('warehouses')
            ->select('id', 'name')
            ->where('is_active', 'Y')
            ->whereNull('deleted_at')
            ->orderBy('priority')
            ->orderBy('id')
            ->get();

        $warehouseIds = [];
        $warehouseHeaders = [];
        foreach ($activeWarehouses as $warehouse) {
            $warehouseIds[] = (int) $warehouse->id;
            $warehouseHeaders[] = '<b>' . $this->featureValueAsRu($warehouse->name) . '</b>';
        }

        // Индексируем остатки по product_id -> warehouse_id, чтобы не делать запросы в цикле товаров
        $stocksByProductWarehouse = [];
        $productIds = array_map(static fn($product) => (int) $product->product_id, $products);
        if (!empty($productIds) && !empty($warehouseIds)) {
            $stocks = DB::table('stocks')
                ->select('product_id', 'warehouse_id', 'qty')
                ->whereIn('product_id', $productIds)
                ->whereIn('warehouse_id', $warehouseIds)
                ->whereNull('deleted_at')
                ->get();

            foreach ($stocks as $stock) {
                $stocksByProductWarehouse[(int) $stock->product_id][(int) $stock->warehouse_id] = $stock->qty;
            }
        }

        // Получаем все параметры, которые вводятся на товары, размещенные в требуемой категории
        $data = Group::with(['features' => function ($query) {
            $query->select(GroupFeature::SELECT_ATTRIBUTES);
        }])
        ->with(['features.featureData' => function ($query) {
            $query->select(Feature::SELECT_ATTRIBUTES);
        }])
        ->find($section['product_group_id'], Group::SELECT_ATTRIBUTES)
        ->toArray();

        // Название категории товаров
        $sectionName = json_decode($data['plural_name'])->ru;
        $featuresIds = []; // хранит id параметров
        $features = []; // хранит названия параметров

        foreach ($data['features'] as $feature) {
            foreach ($feature['feature_data'] as $featureData) {
                $featureName = json_decode($featureData['name'])->ru;

                // Пропускаем ненужные параметры
                if (in_array($featureName, ['z','y','x'])) {
                    continue;
                }

                $featuresIds[] = $featureData['id'];
                $features[] = "<b>" . $featureName . "</b>";
            }
        }

        // Колонки в Excel
        $headers = [
            '<b>Артикул</b>',
            '<b>Название</b>',
            '<b>Цена розничная</b>',
        ];

        if (request()->get('in_stock') == 1) {
            $headers[] = '<b>Главный склад, шт.</b>';
        }

        if (request()->get('similar_products') == 1) {
            $headers[] = '<b>Аналоги</b>';
        }

        $headers = array_merge($headers, [
            '<b>Описание</b>',
            '<b>Главное фото</b>',
            '<b>Фото 2</b>',
            '<b>Фото 3</b>',
            '<b>Фото 4</b>',
            '<b>Бренд</b>',
        ], $features, $warehouseHeaders);

        $excelData = [$headers];

        // Формируем данные по каждому продукту
        foreach ($products as $product) {
            $productMskStock = $stocksByProductWarehouse[(int) $product->product_id][self::MSK_WAREHOUSE_ID] ?? 0;

            // Получем все заведенные на товар характеристики
            $featuresDb = DB::table('feature_product')
                ->where('product_id', $product->product_id)
                ->get();

            // Сохраняем все значения характеристик товара по ID характеристики
            $allFeatures = [];
            foreach ($featuresDb as $feature) {
                $allFeatures[$feature->feature_id] = $feature->value;
            }

            // Сохраняем все значения характеристик в нужном порядке (согласно столбцам в Excel)
            $featureValues = [];
            foreach ($featuresIds as $fid) {
                $raw = $allFeatures[$fid] ?? null;
                $featureValues[] = $raw === null ? '—' : $this->featureValueAsRu($raw);
            }

            // Получаем данные о товаре, включая цены и фотографии
            $productData = Product::with('prices')
                ->with('photos')
                ->where('id', $product->product_id)
                ->first();

            // Получаем тип матрицы для текущего товара
            $matrixType = DB::table('product_matrix_type')
                ->where('product_id', $product->product_id)
                ->value('matrix_type_id');

            // Получаем список аналогов
            $similarProducts = '';
            if ($matrixType) {
                // Получаем все товары с таким же типом матрицы, исключая текущий товар
                $similarProductsData = DB::table('product_matrix_type as pmt')
                    ->join('products as p', 'p.id', '=', 'pmt.product_id')
                    ->where('pmt.matrix_type_id', $matrixType)
                    ->where('pmt.product_id', '!=', $product->product_id)
                    ->pluck('p.name');

                // Декодируем JSON имена и формируем строку
                $similarProducts = $similarProductsData->map(function($name) {
                    return json_decode($name)->ru;
                })->implode(', ');
            }

            $mainPhoto = ''; // хранит главную фото товара
            $productPhotos = []; // хранит все остальные фотографии товара

            // Сохраняем фотографии товара
            $photoIndexNumber = 2;
            foreach ($productData->photos as $photo) {
                if ($photo->is_main == 'Y') {
                    $mainPhoto = '<a href="' . rtrim($shopDomain, '/') . $photo->file_path . '">Main photo</a>';
                } else {
                    $productPhotos[] = '<a href="' . rtrim($shopDomain, '/') . $photo->file_path . '">Photo ' . ($photoIndexNumber++) . '</a>';
                }
            }

            // Определяем розничную цену на товар
            $prices = $productData->prices->toArray();
            $retailPrice = '';
            foreach ($prices as $price) {
                if ($price['price_type_id'] == 1) {
                    $retailPrice = $price['value'];
                    break;
                }
            }

            $desc = (json_decode($productData['overview_text']) !== null)
                ? json_decode($productData['overview_text'])->ru : '';

            $productName = str_replace("&quot;", '"', json_decode($productData['name'])->ru);
            $desc = str_replace("&quot;", '"', $desc);

            // Сохраним значения столбцов для товаров в Excel
            $rowData = [
                $productData['vendor_code'],
                $productName,
                $retailPrice,
            ];

            if (request()->get('in_stock') == 1) {
                $rowData[] = $productMskStock;
            }

            if (request()->get('similar_products') == 1) {
                $rowData[] = $similarProducts;
            }

            $warehouseStocks = [];
            foreach ($warehouseIds as $warehouseId) {
                $warehouseStocks[] = $stocksByProductWarehouse[(int) $product->product_id][$warehouseId] ?? 0;
            }

            $rowData = array_merge($rowData, [
                $desc,
                $mainPhoto,
                $productPhotos[0] ?? '',
                $productPhotos[1] ?? '',
                $productPhotos[2] ?? '',
                $brandNameById[$productData['brand_id']] ?? '',
            ], $featureValues, $warehouseStocks);

            array_push($excelData, $rowData);
        }

        // https://github.com/shuchkin/simplexlsxgen
        // https://support.microsoft.com/en-gb/office/excel-functions-alphabetical-b3944572-255d-4efb-bb96-c6d90033e188
        // Создаем объект для работы с Excel
        $xlsx = new SimpleXLSXGen();
        // Создаем лист с данными
        $xlsx->addSheet($excelData, $sectionName);
        // Закрепим строку
        $xlsx->freezePanes('A2');

        // Ширина колонок
        $cntr = 1;
        $xlsx->setColWidth($cntr++, 7 + 3);
        $xlsx->setColWidth($cntr++, 50); // описание товара
        $xlsx->setColWidth($cntr++, 14 + 3);
        
        if (request()->get('in_stock') == 1) {
            $xlsx->setColWidth($cntr++, 17 + 3);
        }
        if (request()->get('similar_products') == 1) {
            $xlsx->setColWidth($cntr++, 17 + 3);
        }
        $xlsx->setColWidth($cntr++, 17 + 3);
        $xlsx->setColWidth($cntr++, 12 + 3);
        $xlsx->setColWidth($cntr++, 12 + 3);
        $xlsx->setColWidth($cntr++, 6 + 3);
        $xlsx->setColWidth($cntr++, 6 + 3);
        $xlsx->setColWidth($cntr++, 12 + 3);

        // Задаем ширину колонкам (название параметров)
        $col = $cntr;
        foreach ($features as $feature) {
            $featureNameCharsLength = mb_strlen(strip_tags($feature));
            $xlsx->setColWidth($col++, $featureNameCharsLength + 3);
        }

        // Задаем ширину колонкам складов
        foreach ($warehouseHeaders as $warehouseHeader) {
            $warehouseNameCharsLength = mb_strlen(strip_tags($warehouseHeader));
            $xlsx->setColWidth($col++, $warehouseNameCharsLength + 3);
        }

        // Отдаем на скачивание сгенерированный файл
        $xlsx->downloadAs('products_section_' . $sectionId . '_' . date("d_m_Y") . '.xlsx');
    }

    public function productsAllData1C(int $sectionId)
    {
        $sectionNames = ['Матрицы в сборе', 'Матрицы для ноутбуков', 'Матрицы для планшетов'];
        
        // Курс юаня к рублю
        $cnyExchRate = str_replace(',', '.', Settings::where('code', 'CNY_EXCH_RATE')->first()->toArray()['value']);
        // Стоимость доставки за кг
        $cargoPricePerKg = str_replace(',', '.', Settings::where('code', 'CARGO_PRICE_PER_KG')->first()->toArray()['value']);

        // Категория товаров
        $section = Section::find($sectionId);
        $sectionName = json_decode($section['name'])->ru;

        // Все товары в категории
        $sql = "SELECT product_id FROM product_section WHERE section_id=?";
        
        // Добавляем фильтр по наличию через подзапрос к таблице products
        if (request()->get('in_stock') == 1) {
            $sql .= " AND product_id IN (SELECT id FROM products WHERE stock_status = 'in_stock')";
        }
        
        $products = DB::select($sql, [$sectionId]);

        $productsExcelData = [];
        $line = 3;

        if (in_array($sectionName, $sectionNames)) {
            // Формируем данные по каждому продукту
            foreach ($products as $product) {
                // Получаем тип матрицы для текущего товара
                $matrixType = DB::table('product_matrix_type as pmt')
                    ->join('matrix_types as mt', 'mt.id', '=', 'pmt.matrix_type_id')
                    ->where('pmt.product_id', $product->product_id)
                    ->select(
                        'mt.name',
                        'mt.margin_up_to_BP',
                        'mt.EXW_price',
                        'mt.MRP_markup',
                        'mt.RRP_markup',
                        'mt.margin_up_to_P2',
                        'mt.margin_up_to_P3',
                        'mt.margin_up_to_P4',
                        'mt.mark_up_contract',
                        'weight_in_kg'
                    )
                    ->first();

                if (!$matrixType) {
                    continue;
                }

                // Получаем данные о товаре
                $productData = Product::where('id', $product->product_id)->first();

                // Получем все заведенные на товар характеристики
                $featuresDb = DB::table('feature_product')
                    ->where('product_id', $product->product_id)
                    ->whereIn('feature_id', [171, 191, 189, 158, 187, 106, 188])
                    ->get();

                $productName = json_decode($productData['name'])->ru;

                // Сохраняем все значения характеристик товара по ID характеристики
                $allFeatures = [];
                foreach ($featuresDb as $feature) {
                    $allFeatures[$feature->feature_id] = $this->featureValueAsRu($feature->value);
                }

                $desc = [
                    $allFeatures[171] ?? '', // Производитель
                    $productName,
                    $allFeatures[191] ?? '', // Диагональ
                    $allFeatures[189] ?? '', // Разрешение
                    $allFeatures[158] ?? '', // Тип матрицы
                    $allFeatures[187] ?? '', // Разъем
                    $allFeatures[106] ?? '', // Крепления
                    $allFeatures[188] ?? '', // Покрытие
                ];

                $desc = array_filter($desc, fn($value) => $value !== '');

                $line++;

                $productsExcelData[] = [
                    $sectionName,
                    $matrixType->name,
                    $productName,
                    implode(', ', $desc),
                    $productData['vendor_code'],
                    $matrixType->EXW_price,
                    '',
                    '<f>ROUNDUP((F' . $line . ' + G' . $line . ') * 0.02 + ' . $matrixType->weight_in_kg . ' * ' . $cargoPricePerKg . ' * 7.3, 0)</f>',
                    '<f>ROUNDUP((F' . $line . ' + G' . $line . ') * ' . $matrixType->mark_up_contract . ', 0)</f>',
                    '<f>F' . $line . '+G' . $line . '+H' . $line . '</f>',
                    '<f>F' . $line . '+G' . $line . '+I' . $line . '</f>',
                    '<f>MAX((J' . $line . ':K' . $line . ')) + 5</f>',
                    '<f>ROUND((L' . $line . ' * F1), 0)</f>',
                    '<f>L' . $line . ' * Y' . $line . '</f>',
                    '<f>ROUND((N' . $line . ' * F1), 0)</f>',
                    '<f>ROUND((O' . $line . ' * 1.01), 0)</f>',
                    '<f>ROUND((P' . $line . ' * 1.01), -1)</f>',
                    '<f>ROUND((Q' . $line . ' * '. $matrixType->margin_up_to_P2 . '), -1)</f>',
                    '<f>ROUND((R' . $line . ' * '. $matrixType->margin_up_to_P3 . '), -1)</f>',
                    '<f>ROUND((S' . $line . ' * '. $matrixType->margin_up_to_P4 . '), -1)</f>',
                    '<f>ROUND((S' . $line . ' * Z' . $line . '), -1)</f>',
                    '<f>ROUND((U' . $line . ' * AA' . $line . '), -1)</f>',
                    '<f>ROUND((V' . $line . ' * 1.15), -1)</f>',
                    '',
                    $matrixType->margin_up_to_BP,
                    $matrixType->MRP_markup,
                    $matrixType->RRP_markup,
                ];
            }
        }

        // Данные для первой строки в Excel
        $line1 = ['','','','','Курс CNY','<style bgcolor="#FFD800">'.$cnyExchRate.'</style>','','','','','','','WS0000002','','WS0000020','','WS0000004','WS0000001','000000003','000000004','WS0000016','WS0000006','WS0000005'];

        // Данные для второй строки в Excel
        $line2 = [
            '<style bgcolor="#99CCFF">Товарная группа</style>',
            '<style bgcolor="#99CCFF">Марка</style>',
            '<style bgcolor="#99CCFF">Модель</style>',
            '<style bgcolor="#99CCFF">Описание</style>',
            '<style bgcolor="#99CCFF">Артикул</style>',
            '<style bgcolor="#99CCFF">ExW</style>',
            '<style bgcolor="#99CCFF">SSD</style>',
            '<style bgcolor="#99CCFF">Cargo</style>',
            '<style bgcolor="#99CCFF">Contract</style>',
            '<style bgcolor="#99CCFF">LC, Cargo</style>',
            '<style bgcolor="#99CCFF">LC, Contract</style>',
            '<style bgcolor="#99CCFF">TC, CNY</style>',
            '<style bgcolor="#99CCFF">TC, RUB</style>',
            '<style bgcolor="#99CCFF">BP, CNY</style>',
            '<style bgcolor="#99CCFF">BP, RUB</style>',
            '<style bgcolor="#99CCFF">Spec</style>',
            '<style bgcolor="#99CCFF">P1 специальная</style>',
            '<style bgcolor="#99CCFF">P2 партнерская</style>',
            '<style bgcolor="#99CCFF">P3 мелкооптовая</style>',
            '<style bgcolor="#99CCFF">P4 розница на сайте</style>',
            '<style bgcolor="#99CCFF">MRP акции</style>',
            '<style bgcolor="#99CCFF">RRP маркетплейс</style>',
            '<style bgcolor="#99CCFF">Титул Зачеркнутая</style>',
            '<style bgcolor="#99CCFF">Курс у.е.</style>',
            '<style bgcolor="#99CCFF">BP mark-up</style>',
            '<style bgcolor="#99CCFF">MRP mark-up</style>',
            '<style bgcolor="#99CCFF">RRP mark-up</style>',
        ];

        $line3 = [$sectionName];

        $excelData = [$line1, $line2, $line3, ...$productsExcelData];

        // Создаем объект для работы с Excel
        $xlsx = new SimpleXLSXGen();
        // Создаем лист с данными
        $xlsx->addSheet($excelData, $sectionName);
        // Закрепим строку
        $xlsx->freezePanes('A3');

        // Отдаем на скачивание сгенерированный файл
        $xlsx->downloadAs('products_1c_' . $sectionId . '_' . date("d_m_Y") . '.xlsx');
    }

    /**
     * Значения параметров в БД могут храниться как JSON {"ru":"…","en":"…"} — для Excel берём ru.
     */
    private function featureValueAsRu(mixed $value): mixed
    {
        if (!is_string($value) || $value === '') {
            return $value;
        }

        $trimmed = ltrim($value);
        if ($trimmed === '' || ($trimmed[0] !== '{' && $trimmed[0] !== '[')) {
            return $value;
        }

        $decoded = json_decode($value, false);
        if (json_last_error() !== JSON_ERROR_NONE || !is_object($decoded)) {
            return $value;
        }

        return property_exists($decoded, 'ru') ? (string) $decoded->ru : $value;
    }
}
 