<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Modules\Catalog\Entities\AdGenerator;
use Modules\Catalog\Entities\AdYandexMarket;
use Modules\Catalog\Entities\Feature;
use Modules\Catalog\Entities\Product;
use Modules\Catalog\Entities\ProductPhoto;
use Modules\Catalog\Entities\ProductPrice;
use Modules\Catalog\Entities\ProductWhereSn;
use Modules\Catalog\Entities\RelatedProduct;
use Modules\Catalog\Entities\Section;
use Modules\Catalog\Entities\SimilarProduct;
use Modules\Catalog\Entities\ProductAdsRotator;
use Modules\Catalog\Entities\ProductMaterialsUrls;
use Modules\Catalog\Repositories\ProductRepository;
use Modules\Material\Entities\Material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Request;
use App\Helpers\Helper;
use DB;
use Modules\TransportCompany\Entities\CourierCity;
use Modules\TransportCompany\Entities\Warehouse;
use Illuminate\Support\Facades\Cookie;

/**
 * Class EloquentProductRepository
 * @package Modules\Catalog\Repositories\Eloquent
 */
class EloquentProductRepository implements ProductRepository
{
    /** @var Model An instance of the Eloquent Model. */
    protected Model $model;

    /**
     * Repository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function setModel(int $id): self
    {
        try {
            $this->model = $this->model->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            if ($_SERVER['REQUEST_URI'] == '/basket/') {
                Cookie::queue(Cookie::forget('basket'));
            }

            throw new ModelNotFoundException('Product not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        $data['overview_text'] = preg_replace('/\n/', '\n', $data['overview_text']);

        Helper::havePhraseOnDefaultLang($data['name'], 'name');
        Helper::havePhraseOnDefaultLang($data['overview_text'], 'overview_text');

        if (!isset($data['uuid'])) {
            $data['uuid'] = Str::uuid()->toString();
        }

        $data['has_photos'] = $this->hasPhotos($data['uuid']);

        if (!$this->hasPrices($data)) {
            $data['stock_status'] = 'out_of_stock';
        }

        $this->model = $this->model->create($data);

        // Add matrix type relation if specified
        if (isset($data['matrix_type_id'])) {
            DB::table('product_matrix_type')->insert([
                'product_id' => $this->model->id,
                'matrix_type_id' => $data['matrix_type_id']
            ]);
        }

        $this->relatedProducts($data);

        //if (isset($data['similar_products'])) {
        //    foreach ($data['similar_products'] as $similar_product_id) {
        //            $this->addSimilarProduct($this->model->id, $similar_product_id);
        //    }
        //}

        $this->adGenerator($data);
        $this->adYandexMarket($data);
        $this->prices($data);
        $this->model->sections()->attach($data['catalog_section_id']);
        $this->features($data);

        if (isset($data['random_id'])) {
            SimilarProduct::where('product_id', $data['random_id'])
                ->update(['product_id' => $this->model->id]);
            SimilarProduct::where('similar_product_id', $data['random_id'])
                ->update(['similar_product_id' => $this->model->id]);
        }

        // Ads Rotator
        if (isset($data['ads_rotator']) && $data['ads_rotator'] == 'Y') {
            $adsRotator = new ProductAdsRotator();
            $adsRotator->product_id = $this->model->id;
            $adsRotator->save();
        }

        if (isset($data['materials_urls']['drivers'])) {
            $this->materialsUrls('drivers', $this->model->id, $data['materials_urls']['drivers']);
        }

        if (isset($data['materials_urls']['manuals'])) {
            $this->materialsUrls('manuals', $this->model->id, $data['materials_urls']['manuals']);
        }

        if (isset($data['copy_data_to_similar_products'])) {
            $this->copyDataSimilarProducts($data['copy_data_to_similar_products'], $this->model->id, $data);
        }

        // Handle where_sn field
        $this->whereSn($data);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        // Сохраняем существующие значения других языков для полей name и overview_text
        if (isset($attributes['name'])) {
            $attributes['name'] = $this->mergeMultilingualField('name', $attributes['name']);
        }
        
        if (isset($attributes['overview_text'])) {
            $attributes['overview_text'] = preg_replace('/\n/', '\n', $attributes['overview_text']);
            $attributes['overview_text'] = $this->mergeMultilingualField('overview_text', $attributes['overview_text']);
        }

        if (isset($attributes['name'])) {
            Helper::havePhraseOnDefaultLang($attributes['name'], 'name');
        }
        
        if (isset($attributes['overview_text'])) {
            Helper::havePhraseOnDefaultLang($attributes['overview_text'], 'overview_text');
        }

        if (!$this->hasPrices($attributes)) {
            $attributes['stock_status'] = 'out_of_stock';
        }

        // Extract created_at if provided, as Laravel doesn't update it via update() method
        $createdAt = null;
        if (isset($attributes['created_at'])) {
            $createdAt = $attributes['created_at'];
            unset($attributes['created_at']);
        }

        $this->model->update($attributes);

        // Update created_at separately if provided
        if ($createdAt !== null) {
            $this->model->created_at = $createdAt;
            $this->model->save();
        }

        // Update matrix type relation if specified
        if (isset($attributes['matrix_type_id'])) {
            DB::table('product_matrix_type')
                ->where('product_id', $this->model->id)
                ->delete();
            
            DB::table('product_matrix_type')->insert([
                'product_id' => $this->model->id,
                'matrix_type_id' => $attributes['matrix_type_id']
            ]);
        }

        $this->model->relatedProducts()->delete();
        $this->relatedProducts($attributes);

        //$this->model->similarProducts()->delete();
        //$this->similarProducts($attributes); TODO

        $this->model->adGenerator()->delete();
        $this->adGenerator($attributes);

        $this->model->adYandexMarket()->delete();
        $this->adYandexMarket($attributes);

        $this->model->prices()->delete();
        $this->prices($attributes);

        $this->model->sections()->detach();
        $this->model->sections()->attach($attributes['catalog_section_id']);

        // Получаем существующие значения features перед удалением
        $existingFeatures = [];
        if (isset($attributes['features'])) {
            $existingFeaturesData = $this->model->features()->get();
            foreach ($existingFeaturesData as $existingFeature) {
                $featureId = $existingFeature->id;
                if (!isset($existingFeatures[$featureId])) {
                    $existingFeatures[$featureId] = [];
                }
                // Декодируем JSON значение
                $valueJson = $existingFeature->pivot->value;
                $decoded = json_decode($valueJson, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $existingFeatures[$featureId][] = $decoded;
                } else {
                    // Если значение не JSON, преобразуем в JSON формат
                    $existingFeatures[$featureId][] = ['ru' => $valueJson, 'en' => $valueJson];
                }
            }
        }

        $this->model->features()->detach();
        $this->features($attributes, $existingFeatures);

        // Ads Rotator
        ProductAdsRotator::where('product_id', $this->model->id)->delete();
        if (isset($attributes['ads_rotator']) && $attributes['ads_rotator'] == 'Y') {
            $adsRotator = new ProductAdsRotator();
            $adsRotator->product_id = $this->model->id;
            $adsRotator->save();
        }

        if (isset($attributes['materials_urls']['drivers'])) {
            $this->materialsUrls('drivers', $this->model->id, $attributes['materials_urls']['drivers']);
        }

        if (isset($attributes['materials_urls']['manuals'])) {
            $this->materialsUrls('manuals', $this->model->id, $attributes['materials_urls']['manuals']);
        }

        if (isset($attributes['copy_data_to_similar_products'])) {
            $this->copyDataSimilarProducts($attributes['copy_data_to_similar_products'], $this->model->id, $attributes);
        }

        // Handle where_sn field
        $this->whereSn($attributes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        $this->model->relatedProducts()->delete();
        $this->model->similarProducts()->delete();
        $this->model->adGenerator()->delete();
        $this->model->adYandexMarket()->delete();
        $this->model->prices()->delete();
        $this->model->sections()->detach();
        $this->model->features()->detach();
        $this->model->whereSn()->delete();

        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toggleVisibilityOfProduct(int $pid): bool
    {
        $this->setModel($pid);

        $this->model->is_hidden = ($this->model->is_hidden == 'N') ? 'Y' : 'N';
        $this->model->save();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function all(
        ?string $search,
        int $page,
        int $sectionId,
        string $sorting,
        string $direction,
        array $filters,
        int $onPage,
        ?array $productIDs = []
    ): array|int {
        if (is_null($productIDs)) {
            return [];
        }

        if ($onPage >= 5000) {
            ini_set('memory_limit', '2048M');
        }

        //\DB::enableQueryLog(); // Enable query log

        $clientTypeId = (auth()->guard('client')->check()) ?
            auth()->guard('client')->user()->load(['clientType'])->clientType[0]->id : 1;

        $filterHidden       = $filters['hidden'] ?? null;
        $filterStockStatus  = $filters['stock_status'] ?? null;
        $filterProductGroup = $filters['product_group'] ?? null;
        $filterBrand        = $filters['brand'] ?? null;

        $query = Product::with(['sections' => function ($query) {
            $query->select(Section::SELECT_ATTRIBUTES);
        }])
            ->with(['prices' => function ($query) {
                $query->select(ProductPrice::SELECT_ATTRIBUTES);
            }])
            ->with(['photos' => function ($query) {
                $query->select(ProductPhoto::SELECT_ATTRIBUTES)
                    ->where('is_main', 'Y')
                    ->whereNull('product_photos.deleted_at');
            }])
            // Start Filters
            ->when(isset($filters['hidden']) !== false, function ($query) use ($filterHidden, $clientTypeId) {
                if (!in_array($clientTypeId, config('storefront.not_for_everyone_client_types', []))) {
                    // Не показывать юзерам товары НЕ ДЛЯ ВСЕХ
                    return $query->where('is_hidden', $filterHidden)->where('not_for_everyone', '!=', 'Y');
                } else {
                    return $query->where('is_hidden', $filterHidden);
                }
            })
            ->when(isset($filters['stock_status']) !== false, function ($query) use ($filterStockStatus) {
                return $query->where('stock_status', $filterStockStatus);
            })
            ->when(isset($filters['product_group']) !== false, function ($query) use ($filterProductGroup) {
                return $query->where('product_group_id', $filterProductGroup);
            })
            ->when(isset($filters['brand']) !== false, function ($query) use ($filterBrand) {
                return $query->where('brand_id', $filterBrand);
            })
            ->when(isset($filters['without_prices']) !== false, function ($query) {
                return $query->withCount('prices')->having('prices_count', '=', 0);
            })
            ->when(isset($filters['without_photos']) !== false, function ($query) {
                return $query->where('has_photos', 'N');
            })
            ->when(isset($filters['without_related']) !== false, function ($query) {
                return $query->withCount('relatedProducts')->having('related_products_count', '=', 0);
            })
            ->when(isset($filters['without_similar']) !== false, function ($query) {
                return $query->withCount('similarProducts')->having('similar_products_count', '=', 0);
            })
            ->when(isset($filters['without_features']) !== false, function ($query) {
                return $query->withCount('features')->having('features_count', '=', 0);
            })
            // End Filters

            // Start Sorting
            ->when($sorting == 'by_novelty', function ($query) use ($direction) {
                return $query->orderBy('products.id', $direction);
            })
            ->when($sorting == 'by_created_at', function ($query) use ($direction) {
                return $query->orderBy('products.created_at', $direction)
                    ->orderBy('products.id', $direction);
            })
            ->when($sorting == 'by_search', function ($query) use ($productIDs) {
                return $query->orderByRaw("FIELD(products.id, '" . implode("','", $productIDs) . "')");
            })
            ->when($sorting == 'by_availability', function ($query) use ($direction) {
                if (strtolower($direction) == 'asc') {
                    return $query->orderByRaw("FIELD(stock_status, 'in_stock','pre_order','out_of_stock')");
                } else {
                    return $query->orderByRaw("FIELD(stock_status, 'out_of_stock','pre_order','in_stock')");
                }
            })
            ->when($sorting == 'by_price', function ($query) use ($direction, $filters) {
                $priceTypeId = $filters['price_type_id'] ?? Request::input('price_type_id', 1);
                $priceCondition = isset($filters['price_type_id'])
                    ? ' AND price_type_id=' . (int) $priceTypeId . ' AND deleted_at IS NULL'
                    : '';
                $wh = Warehouse::where('is_active', 'Y')->get()->keyBy('id')->toArray();
                $whCity = implode(",", $this->getCityWarehouses($wh));

                return $query
                    ->select(DB::raw(implode(",", Product::SELECT_ATTRIBUTES) . ", product_prices.value as price,
                    EXISTS (SELECT * FROM stocks WHERE stocks.product_id=products.id AND stocks.warehouse_id IN (" . $whCity . ") AND qty>0 AND stocks.deleted_at IS NULL) as has_stock,
                    EXISTS (SELECT * FROM product_prices WHERE product_prices.product_id=products.id AND value>0" . $priceCondition . ") as has_price"))
                    ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                    ->where('product_prices.price_type_id', '=', $priceTypeId)
                    ->whereNull('product_prices.deleted_at')
                    ->orderBy('has_stock', 'desc')
                    ->orderBy('has_price', 'desc')
                    ->orderBy('product_prices.value', 'asc');
            })
            ->when($sorting == 'by_views', function ($query) use ($direction) {
                return $query->orderBy('views_counter', $direction);
            })
            ->when($sorting == 'by_name', function ($query) use ($direction) {
                return $query->orderBy('name', $direction);
            })
            // End Sorting

            ->when(!is_null($productIDs) && count($productIDs) > 0, function ($query) use ($productIDs) {
                return $query->whereIn('products.id', $productIDs);
            })
            ->when(is_string($search) && strlen($search) >= 3, function ($query) use ($search) {
                return $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('vendor_code', 'LIKE', '%' . $search . '%');
            })
            ->when($sectionId > 0, function ($query) use ($sectionId) {
                return $query->whereHas('sections', function ($query) use ($sectionId) {
                    $query->where('catalog_sections.id', '=', $sectionId);
                });
            });

        if ($onPage == 0) {
            return $query->count();
        } else {
            $all = $query->skip(($page - 1) * $onPage)
                ->take($onPage)
                ->get(Product::SELECT_ATTRIBUTES)
                ->toArray();

            //if ($sectionId == 60) {
            //    Log::debug(print_r(\DB::getQueryLog(), true));
            //}

            foreach ($all as &$product) {
                $product['url'] = \Helper::productUrl($product['name'], (int) $product['id']);

                foreach ($product['sections'] as &$section) {
                    $section['breadcrumbs'] = $this->getBreadcrumbs($section['id']);
                }
            }

            return Arr::onlyActiveLang(['name', 'overview_text', 'ext_name'], App::getLocale(), $all);
        }
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $data = Product::with(['features' => function ($query) {
            $query->select(Feature::SELECT_ATTRIBUTES);
        }])
            ->with(['sections' => function ($query) {
                $query->select(Section::SELECT_ATTRIBUTES);
            }])
            ->with(['materialsUrls' => function ($query) {
                $query->select(ProductMaterialsUrls::SELECT_ATTRIBUTES);
            }])
            ->with(['prices' => function ($query) {
                $query->select(ProductPrice::SELECT_ATTRIBUTES);
            }])
            ->with(['adYandexMarket' => function ($query) {
                $query->select(AdYandexMarket::SELECT_ATTRIBUTES);
            }])
            ->with(['adGenerator' => function ($query) {
                $query->select(AdGenerator::SELECT_ATTRIBUTES);
            }])
            ->with(['photos' => function ($query) {
                $query->select(ProductPhoto::SELECT_ATTRIBUTES);
            }])
            ->with(['similarProducts' => function ($query) {
                $query->select(SimilarProduct::SELECT_ATTRIBUTES);
            }])
            ->with(['relatedProducts' => function ($query) {
                $query->select(RelatedProduct::SELECT_ATTRIBUTES);
            }])
            ->with(['whereSn' => function ($query) {
                $query->select(ProductWhereSn::SELECT_ATTRIBUTES);
            }])
            ->find($this->model->id, Product::SELECT_ATTRIBUTES)
            ->toArray();

        $data['features'] = Arr::onlyActiveLang(
            ['name', 'unit'],
            App::getLocale(),
            $data['features']
        );

        // Локализуем pivot.value для features
        foreach ($data['features'] as &$feature) {
            if (isset($feature['pivot']['value'])) {
                $valueJson = $feature['pivot']['value'];
                $decoded = json_decode($valueJson, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $locale = App::getLocale();
                    $feature['pivot']['value'] = $decoded[$locale] ?? $decoded['ru'] ?? $valueJson;
                }
            }
        }
        unset($feature);

        $data['sections'] = Arr::onlyActiveLang(
            ['name'],
            App::getLocale(),
            $data['sections']
        );

        foreach ($data['sections'] as &$section) {
            $section['breadcrumbs'] = $this->getBreadcrumbs($section['id']);
        }

        $data['ads_rotator'] = (ProductAdsRotator::where('product_id', $this->model->id)->count()) ? 'Y' : 'N';

        $similarProducts = Arr::pluck($data['similar_products'], 'similar_product_id');
        $data['similar_products'] = Product::whereIn('id', $similarProducts)->get(['vendor_code', 'name', 'id as similar_product_id'])->toArray();

        foreach ($data['similar_products'] as &$similarProducts) {
            $similarProducts['name'] = json_decode($similarProducts['name'], true)[App::getLocale()];
        }

        foreach ($data['similar_products'] as &$similarProduct) {
            $similarProduct['url'] = \Helper::productUrl($similarProduct['name'], (int) $similarProduct['similar_product_id']);
        }

        $relatedProducts = Arr::pluck($data['related_products'], 'related_product_id');
        $data['related_products'] = Product::whereIn('id', $relatedProducts)->get(['vendor_code', 'name', 'id as related_product_id'])->toArray();

        foreach ($data['related_products'] as &$relatedProduct) {
            $relatedProduct['name'] = json_decode($relatedProduct['name'], true)[App::getLocale()];
            $relatedProduct['url'] = \Helper::productUrl($relatedProduct['name'], (int) $relatedProduct['related_product_id']);
        }

        // Add where_sn data
        if (isset($data['where_sn']) && !empty($data['where_sn'])) {
            $data['where_sn'] = $data['where_sn'][0]['where_desc'] ?? '';
        } else {
            $data['where_sn'] = '';
        }

        // Обработка materials_urls: получаем значения title и url в активном языке пользователя
        if (isset($data['materials_urls']) && is_array($data['materials_urls'])) {
            $activeLocale = App::getLocale();
            
            foreach ($data['materials_urls'] as &$materialUrl) {
                // Обрабатываем title: получаем значение для активного языка с fallback на ru/en
                if (isset($materialUrl['title'])) {
                    $titleJson = json_decode($materialUrl['title'], true);
                    if (is_array($titleJson)) {
                        $materialUrl['title'] = $this->getMaterialValueWithFallback($titleJson, $activeLocale);
                    }
                    // Если title не JSON, оставляем как есть (для обратной совместимости)
                }
                
                // Обрабатываем url: получаем значение для активного языка с fallback на ru/en
                if (isset($materialUrl['url'])) {
                    $urlJson = json_decode($materialUrl['url'], true);
                    if (is_array($urlJson)) {
                        $materialUrl['url'] = $this->getMaterialValueWithFallback($urlJson, $activeLocale);
                    }
                    // Если url не JSON, оставляем как есть (для обратной совместимости)
                }
            }
            unset($materialUrl);
        }

        return Arr::onlyActiveLang(['name', 'overview_text', 'ext_name'], App::getLocale(), [$data])[0];
    }

    /**
     * Получает массив переводов из базы данных.
     * Читает запись materials с code = 'translate_drivers_manuals_code',
     * извлекает content для языка "ru", парсит HTML по тегам <div>СТРОКА</div>
     * и формирует массив замен формата "russian text = english text".
     *
     * @return array Массив переводов [russian => english]
     */
    private function getTranslationsFromDatabase(): array
    {
        $translations = [];

        try {
            // Получаем запись из таблицы materials
            $material = Material::where('code', 'translate_drivers_manuals_code')->first();

            if (!$material || !$material->content) {
                return $translations;
            }

            // Декодируем JSON content
            $contentJson = json_decode($material->content, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($contentJson)) {
                return $translations;
            }

            // Извлекаем значение для языка "ru"
            $ruContent = $contentJson['ru'] ?? '';
            if (empty($ruContent)) {
                return $translations;
            }

            // Извлекаем все содержимое из тегов <div>...</div>
            // Паттерн извлекает содержимое между открывающим и закрывающим тегами div
            preg_match_all('/<div>(.*?)<\/div>/s', $ruContent, $matches);

            // Обрабатываем каждое содержимое div'а
            foreach ($matches[1] as $divContent) {
                // Убираем все HTML теги (например, <br>, <br/> и т.д.)
                $cleanText = strip_tags($divContent);
                
                // Убираем лишние пробелы, переводы строк и другие whitespace символы
                $cleanText = trim($cleanText);
                $cleanText = preg_replace('/\s+/', ' ', $cleanText);

                // Пропускаем пустые строки
                if (empty($cleanText)) {
                    continue;
                }

                // Парсим строку по формату "russian text == english text"
                // Разделитель - двойное равно с пробелами вокруг: " == "
                if (preg_match('/^(.+?)\s*==\s*(.+)$/', $cleanText, $parseMatches)) {
                    $russian = trim($parseMatches[1]);
                    $english = trim($parseMatches[2]);

                    // Пропускаем, если одна из частей пустая
                    if (!empty($russian) && !empty($english)) {
                        $translations[$russian] = $english;
                    }
                }
            }
        } catch (\Exception $e) {
            // В случае ошибки возвращаем пустой массив
            return $translations;
        }

        return $translations;
    }

    /**
     * @inheritDoc
     */
    public function getQtyProductsInCat(int $sectionId, string $sorting = 'by_price'): int
    {
        $onPage    = 0; // if 0, then only the quantity will be returned
        $page      = 1;
        $search    = '';
        $direction = 'asc';
        $filters   = ['hidden' => 'N'];

        return $this->all($search, $page, $sectionId, $sorting, $direction, $filters, $onPage);
    }


    public function getSimilarProducts(array $products): array
    {
        $data = SimilarProduct::whereIn('product_id', $products)->get(['product_id', 'similar_product_id']);
        $data = $data->groupBy('product_id');

        $ids = [];
        foreach ($data as &$product) {
            foreach ($product as &$similar) {
                $ids[] = $similar['similar_product_id'];
                unset($similar['product_id']);
            }
        }

        $products = Product::whereIn('id', $ids)->get(['name', 'id']);
        $productsById = $products->keyBy('id');

        foreach ($data as &$product) {
            foreach ($product as &$similar) {
                if (isset($productsById[$similar['similar_product_id']])) {
                    $similar['name'] = json_decode($productsById[$similar['similar_product_id']]['name'], true)[App::getLocale()];
                }
            }
        }

        return $data->toArray();
    }


    /**
     * Adds related products.
     */
    private function relatedProducts(array $data): void
    {
        $relatedProducts = [];

        if (isset($data['related_products'])) {
            foreach ($data['related_products'] as $related_product_id) {
                if ($this->model->id != $related_product_id) {
                    $relatedProducts[] = [
                        'product_id' => $this->model->id,
                        'related_product_id' => $related_product_id,
                    ];
                }
            }

            $this->model->relatedProducts()->createMany($relatedProducts);
        }
    }

    /**
     * Adds similar products.
     */
    /*
    private function similarProducts(array $data): void
    {
        $similarProducts = [];

        if (isset($data['similar_products'])) {
            foreach ($data['similar_products'] as $similar_product_id) {
                $similarProducts[] = [
                    'product_id' => $this->model->id,
                    'similar_product_id' => $similar_product_id,
                ];
            }

            $this->model->similarProducts()->createMany($similarProducts);
        }
    }
    */

    private function adGenerator(array $data): void
    {
        if (isset($data['ad_generator'])) {
            $this->model->adGenerator()->create([
                'alt_names' => ($data['ad_generator']['alt_names']) ? $data['ad_generator']['alt_names'] : '',
                'pn'        => ($data['ad_generator']['pn']) ? $data['ad_generator']['pn'] : '',
            ]);
        }
    }

    private function adYandexMarket(array $data): void
    {
        if (isset($data['ad_yandex_market'])) {
            $this->model->adYandexMarket()->create([
                'name'          => $data['ad_yandex_market']['name'] ?? '',
                'overview_text' => $data['ad_yandex_market']['overview_text'] ?? '',
                'picture_url'   => $data['ad_yandex_market']['picture_url'] ?? '',
                'is_active'     => $data['ad_yandex_market']['is_active'] ?? 'N',
            ]);
        }
    }

    private function hasPrices(array $data): bool
    {
        if (!isset($data['prices'])) {
            return false;
        }

        foreach ($data['prices'] as $price) {
            if ($price['value'] > 0) {
                return true;
            }
        }

        return false;
    }

    private function prices(array $data): void
    {
        $prices = [];

        if (isset($data['prices'])) {
            foreach ($data['prices'] as $price) {
                if ($price['value'] > 0) {
                    $prices[] = [
                        'product_id'    => $this->model->id,
                        'price_type_id' => $price['price_type_id'],
                        'value'         => $price['value'],
                    ];
                }
            }

            $this->model->prices()->createMany($prices);
        }
    }

    /**
     * Does the product have pictures?
     * @param $uuid
     * @return string
     */
    private function hasPhotos($uuid): string
    {
        return (ProductPhoto::where('uuid', $uuid)->count() == 0) ? 'N' : 'Y';
    }

    private function materialsUrls(string $type, int $productId, $data): bool
    {
        if (is_null($data)) {
            // Если данных нет, удаляем все записи этого типа
            ProductMaterialsUrls::where('type', $type)
                ->where('product_id', $productId)
                ->delete();
            return false;
        }

        // Получаем существующие записи для этого типа и продукта, отсортированные по ID
        $existingUrls = ProductMaterialsUrls::where('type', $type)
            ->where('product_id', $productId)
            ->orderBy('id')
            ->get();

        $processedIds = [];

        // Проверяем формат данных: новый (массив) или старый (строка)
        if (is_array($data)) {
            // Новый формат: массив объектов с lang и value
            // Обрабатываем каждый элемент массива
            foreach ($data as $index => $item) {
                if (!isset($item['lang']) || !isset($item['value'])) {
                    continue;
                }

                $itemLocale = $item['lang'];
                $value = trim($item['value']);
                
                // Разбираем value на title и url (формат: "title;url")
                $linkData = explode(";", $value);
                if (count($linkData) < 2) {
                    continue;
                }

                $newTitle = trim($linkData[0]);
                $newUrl = trim($linkData[1]);

                if (filter_var($newUrl, FILTER_VALIDATE_URL) === false) {
                    continue;
                }

                // Ищем существующую запись несколькими способами:
                // 1. По URL (проверяем все языки в URL)
                // 2. По позиции в массиве (индекс)
                // 3. По title для других языков (если title совпадает, это та же запись)
                $existingUrl = null;
                
                // Способ 1: Поиск по URL (проверяем все языки)
                foreach ($existingUrls as $existingItem) {
                    if (in_array($existingItem->id, $processedIds)) {
                        continue;
                    }
                    
                    $urlJson = json_decode($existingItem->url, true);
                    if (is_array($urlJson)) {
                        // Проверяем, совпадает ли новый URL с URL в любом языке существующей записи
                        foreach ($urlJson as $lang => $langUrl) {
                            if ($langUrl === $newUrl) {
                                $existingUrl = $existingItem;
                                break 2; // Выходим из обоих циклов
                            }
                        }
                    } else {
                        // Если URL не JSON, сравниваем напрямую
                        if ($existingItem->url === $newUrl) {
                            $existingUrl = $existingItem;
                            break;
                        }
                    }
                }
                
                // Способ 2: Если не нашли по URL, ищем по позиции (индексу)
                if (!$existingUrl && isset($existingUrls[$index]) && !in_array($existingUrls[$index]->id, $processedIds)) {
                    $existingUrl = $existingUrls[$index];
                }
                
                // Способ 3: Если не нашли по позиции, ищем по title для других языков
                if (!$existingUrl) {
                    foreach ($existingUrls as $existingItem) {
                        if (in_array($existingItem->id, $processedIds)) {
                            continue;
                        }
                        
                        $titleJson = json_decode($existingItem->title, true);
                        if (is_array($titleJson)) {
                            // Проверяем, совпадает ли новый title с title в других языках (не текущем)
                            foreach ($titleJson as $lang => $langTitle) {
                                if ($lang !== $itemLocale && $langTitle === $newTitle) {
                                    $existingUrl = $existingItem;
                                    break 2; // Выходим из обоих циклов
                                }
                            }
                        }
                    }
                }

                if ($existingUrl) {
                    // Обновляем существующую запись, сохраняя значения других языков
                    // Метод mergeMultilingualFieldForMaterialsUrls уже сохраняет данные других языков
                    $mergedTitle = $this->mergeMultilingualFieldForMaterialsUrls($existingUrl->title, $newTitle, $itemLocale);
                    $mergedUrl = $this->mergeMultilingualFieldForMaterialsUrls($existingUrl->url, $newUrl, $itemLocale);
                    
                    $existingUrl->title = $mergedTitle;
                    $existingUrl->url = $mergedUrl;
                    $existingUrl->save();
                    
                    // Обновляем запись в коллекции, чтобы последующие элементы могли её найти
                    // Обновляем атрибуты в коллекции напрямую
                    $existingUrl->setAttribute('title', $mergedTitle);
                    $existingUrl->setAttribute('url', $mergedUrl);
                    
                    $processedIds[] = $existingUrl->id;
                } else {
                    // Создаем новую запись только если не найдена существующая
                    $pmUrl = new ProductMaterialsUrls();
                    $pmUrl->product_id = $this->model->id;
                    $pmUrl->title = json_encode([$itemLocale => $newTitle], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $pmUrl->url = json_encode([$itemLocale => $newUrl], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $pmUrl->type = $type;
                    $pmUrl->save();
                    
                    // Добавляем новую запись в коллекцию, чтобы последующие элементы могли её найти
                    $existingUrls->push($pmUrl);
                    
                    // Добавляем ID новой записи в processedIds
                    $processedIds[] = $pmUrl->id;
                }
            }
        } else {
            // Старый формат: строка "title;url\n" (для обратной совместимости)
            $activeLocale = App::getLocale();
            $links = explode("\n", str_replace("\r", "", $data));

            foreach ($links as $index => $link) {
                $linkData = explode(";", $link);

                if (count($linkData) == 2) {
                    $newTitle = trim($linkData[0]);
                    $newUrl = trim($linkData[1]);

                    if (filter_var($newUrl, FILTER_VALIDATE_URL) === false) {
                        continue;
                    }

                    // Пытаемся найти существующую запись по индексу или по URL
                    $existingUrl = null;
                    if (isset($existingUrls[$index])) {
                        // Проверяем, соответствует ли URL существующей записи
                        $existingUrlItem = $existingUrls[$index];
                        $urlJson = json_decode($existingUrlItem->url, true);
                        if (is_array($urlJson)) {
                            // Проверяем, совпадает ли новый URL с одним из языков существующего URL
                            $urlMatches = ($urlJson['ru'] ?? '') === $newUrl || ($urlJson['en'] ?? '') === $newUrl;
                            if ($urlMatches) {
                                $existingUrl = $existingUrlItem;
                            }
                        } else {
                            // Если URL не JSON, сравниваем напрямую
                            if ($existingUrlItem->url === $newUrl) {
                                $existingUrl = $existingUrlItem;
                            }
                        }
                    }

                    // Если не нашли по индексу, ищем по URL среди всех существующих
                    if (!$existingUrl) {
                        $existingUrl = $existingUrls->first(function ($item) use ($newUrl, $processedIds) {
                            if (in_array($item->id, $processedIds)) {
                                return false;
                            }
                            $urlJson = json_decode($item->url, true);
                            if (is_array($urlJson)) {
                                return ($urlJson['ru'] ?? '') === $newUrl || ($urlJson['en'] ?? '') === $newUrl;
                            }
                            return $item->url === $newUrl;
                        });
                    }

                    if ($existingUrl) {
                        // Обновляем существующую запись, сохраняя значения других языков
                        $mergedTitle = $this->mergeMultilingualFieldForMaterialsUrls($existingUrl->title, $newTitle, $activeLocale);
                        $mergedUrl = $this->mergeMultilingualFieldForMaterialsUrls($existingUrl->url, $newUrl, $activeLocale);
                        
                        $existingUrl->title = $mergedTitle;
                        $existingUrl->url = $mergedUrl;
                        $existingUrl->save();
                        
                        $processedIds[] = $existingUrl->id;
                    } else {
                        // Создаем новую запись
                        $pmUrl = new ProductMaterialsUrls();
                        $pmUrl->product_id = $this->model->id;
                        $pmUrl->title = json_encode([$activeLocale => $newTitle], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        $pmUrl->url = json_encode([$activeLocale => $newUrl], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        $pmUrl->type = $type;
                        $pmUrl->save();
                    }
                }
            }
        }

        // Удаляем только те записи, которые не были обработаны
        // НО: не удаляем записи, которые имеют данные для языков, которых нет в новом массиве
        // Это позволяет сохранять данные других языков, даже если они не пришли в запросе
        if (!empty($processedIds)) {
            // Получаем все языки из нового массива данных
            $newDataLocales = [];
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (isset($item['lang'])) {
                        $newDataLocales[] = $item['lang'];
                    }
                }
            }
            
            // Удаляем только те записи, которые:
            // 1. Не были обработаны (не в processedIds)
            // 2. И не имеют данных для языков, которых нет в новом массиве
            // Если в новом массиве есть только один язык (например, ru), 
            // то записи с данными для других языков (например, en) не должны удаляться
            if (!empty($newDataLocales)) {
                // Удаляем только записи, которые не были обработаны
                // Данные других языков сохраняются автоматически через mergeMultilingualFieldForMaterialsUrls
                ProductMaterialsUrls::where('type', $type)
                    ->where('product_id', $productId)
                    ->whereNotIn('id', $processedIds)
                    ->delete();
            } else {
                // Если новый формат, но нет языков (не должно быть), удаляем необработанные
                ProductMaterialsUrls::where('type', $type)
                    ->where('product_id', $productId)
                    ->whereNotIn('id', $processedIds)
                    ->delete();
            }
        }

        return true;
    }

    private function features(array $data, array $existingFeatures = []): void
    {
        if (isset($data['features'])) {
            foreach ($data['features'] as $feature) {
                $featureId = $feature['feature_id'];
                
                // Поддержка нового формата с многоязычными значениями
                if (isset($feature['values']) && is_array($feature['values'])) {
                    // Новый формат: values - массив с lang и value
                    // Поддержка разделения по ";;;" - несколько значений для одного параметра
                    
                    // Сначала собираем все значения по языкам, разделяя по ";;;"
                    $valuesByLang = [];
                    foreach ($feature['values'] as $valueData) {
                        if (!isset($valueData['lang']) || !isset($valueData['value'])) {
                            continue;
                        }
                        
                        $lang = $valueData['lang'];
                        $value = trim($valueData['value']);
                        
                        if ($value === '' || $value === 'null') {
                            continue;
                        }
                        
                        // Разделяем по ";;;" если есть
                        $parts = explode(";;;", $value);
                        $valuesByLang[$lang] = array_map('trim', $parts);
                    }
                    
                    // Если есть значения, разделенные по ";;;", обрабатываем их отдельно
                    $hasMultipleValues = false;
                    foreach ($valuesByLang as $langValues) {
                        if (count($langValues) > 1) {
                            $hasMultipleValues = true;
                            break;
                        }
                    }
                    
                    if ($hasMultipleValues) {
                        // Определяем максимальное количество значений
                        $maxCount = 0;
                        foreach ($valuesByLang as $langValues) {
                            $maxCount = max($maxCount, count($langValues));
                        }
                        
                        // Создаем отдельные записи для каждого значения
                        for ($i = 0; $i < $maxCount; $i++) {
                            $jsonValue = [];
                            
                            // Собираем значение для каждого языка по индексу
                            foreach ($valuesByLang as $lang => $langValues) {
                                if (isset($langValues[$i]) && trim($langValues[$i]) !== '' && trim($langValues[$i]) !== 'null') {
                                    $partValue = trim($langValues[$i]);
                                    
                                    // Проверяем, не является ли значение уже JSON
                                    $decoded = json_decode($partValue, true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        // Если это JSON, объединяем с существующими значениями
                                        $jsonValue = array_merge($jsonValue, $decoded);
                                    } else {
                                        $jsonValue[$lang] = $partValue;
                                    }
                                }
                            }
                            
                            // Пропускаем пустые значения
                            if (empty($jsonValue)) {
                                continue;
                            }
                            
                            // Если приходит значение только на одном языке и есть существующие значения,
                            // объединяем новое значение с существующим, сохраняя значения других языков
                            $wasMerged = false;
                            if (!empty($existingFeatures) && isset($existingFeatures[$featureId])) {
                                $hasMultipleLangs = count($jsonValue) > 1;
                                
                                if (!$hasMultipleLangs) {
                                    // Приходит значение только на одном языке
                                    $newLang = array_key_first($jsonValue);
                                    $newValue = $jsonValue[$newLang];
                                    
                                    // Ищем соответствующее существующее значение по индексу
                                    // Важно: используем только значение с того же индекса, чтобы не смешивать разные значения
                                    if (isset($existingFeatures[$featureId][$i])) {
                                        $existingValue = $existingFeatures[$featureId][$i];
                                        // Объединяем: новое значение перезаписывает существующее для этого языка,
                                        // остальные языки сохраняются
                                        $jsonValue = array_merge($existingValue, $jsonValue);
                                        $wasMerged = true;
                                    }
                                    // Если нет значения по индексу - это новое значение, не объединяем с существующими
                                } else {
                                    // Приходят значения на нескольких языках, но все равно проверяем,
                                    // нет ли существующих значений, которые нужно сохранить
                                    if (isset($existingFeatures[$featureId][$i])) {
                                        $existingValue = $existingFeatures[$featureId][$i];
                                        // Объединяем: новые значения перезаписывают существующие,
                                        // но сохраняем языки, которых нет в новых
                                        $jsonValue = array_merge($existingValue, $jsonValue);
                                        $wasMerged = true;
                                    }
                                }
                            }
                            
                            // Убеждаемся, что есть значения для всех языков
                            if (!isset($jsonValue['ru']) && !isset($jsonValue['en'])) {
                                continue;
                            }
                            
                            // Если мы объединили с существующими значениями и пришло значение только на одном языке,
                            // проверяем, есть ли значение для другого языка. Если нет - устанавливаем равным отправленному
                            if ($wasMerged && count($jsonValue) === 1) {
                                $newLang = array_key_first($jsonValue);
                                $newValue = $jsonValue[$newLang];
                                $otherLang = ($newLang === 'ru') ? 'en' : 'ru';
                                
                                // Если значение для другого языка отсутствует, устанавливаем его равным отправленному
                                if (!isset($jsonValue[$otherLang])) {
                                    $jsonValue[$otherLang] = $newValue;
                                }
                            } elseif (!$wasMerged) {
                                // Дублируем значения только если мы НЕ объединили с существующими значениями
                                // (т.е. если это новое значение, а не обновление существующего)
                                if (!isset($jsonValue['ru']) && isset($jsonValue['en'])) {
                                    $jsonValue['ru'] = $jsonValue['en'];
                                }
                                if (!isset($jsonValue['en']) && isset($jsonValue['ru'])) {
                                    $jsonValue['en'] = $jsonValue['ru'];
                                }
                            }
                            
                            $finalJsonValue = json_encode($jsonValue, JSON_UNESCAPED_UNICODE);
                            $this->model->features()->attach($featureId, ['value' => $finalJsonValue]);
                        }
                    } else {
                        // Стандартная обработка без разделения по ";;;"
                        // Группируем значения по уникальным комбинациям (для объединения значений разных языков)
                        $groupedValues = [];
                        
                        foreach ($feature['values'] as $valueData) {
                            if (!isset($valueData['lang']) || !isset($valueData['value'])) {
                                continue;
                            }
                            
                            $lang = $valueData['lang'];
                            $value = trim($valueData['value']);
                            
                            if ($value === '' || $value === 'null') {
                                continue;
                            }
                            
                            // Используем значение как ключ для группировки (для объединения значений разных языков)
                            // Если значение уже в JSON, извлекаем его
                            $decoded = json_decode($value, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                // Уже JSON, используем значение из ru как ключ
                                $key = $decoded['ru'] ?? $value;
                                if (!isset($groupedValues[$key])) {
                                    $groupedValues[$key] = $decoded;
                                } else {
                                    // Объединяем языки
                                    $groupedValues[$key] = array_merge($groupedValues[$key], $decoded);
                                }
                            } else {
                                // Используем значение как ключ для группировки
                                $key = $value;
                                if (!isset($groupedValues[$key])) {
                                    $groupedValues[$key] = [];
                                }
                                $groupedValues[$key][$lang] = $value;
                            }
                        }
                        
                        // Создаем записи для каждой уникальной комбинации значений
                        foreach ($groupedValues as $jsonValue) {
                            // Если приходит значение только на одном языке и есть существующие значения,
                            // объединяем новое значение с существующим, сохраняя значения других языков
                            $wasMerged = false;
                            if (!empty($existingFeatures) && isset($existingFeatures[$featureId])) {
                                $hasMultipleLangs = count($jsonValue) > 1;
                                
                                if (!$hasMultipleLangs) {
                                    // Приходит значение только на одном языке
                                    $newLang = array_key_first($jsonValue);
                                    $newValue = $jsonValue[$newLang];
                                    
                                    // Ищем соответствующее существующее значение
                                    // Сопоставляем по значению на том же языке
                                    $foundExisting = false;
                                    foreach ($existingFeatures[$featureId] as $existingValue) {
                                        // Если новое значение совпадает с существующим на том же языке
                                        if (isset($existingValue[$newLang]) && $existingValue[$newLang] === $newValue) {
                                            // Объединяем: новое значение перезаписывает существующее для этого языка,
                                            // остальные языки сохраняются
                                            $jsonValue = array_merge($existingValue, $jsonValue);
                                            $foundExisting = true;
                                            $wasMerged = true;
                                            break;
                                        }
                                    }
                                    
                                    // Если не нашли по тому же языку, пробуем сопоставить по другому языку
                                    // (на случай, если значение изменилось, но это то же самое значение на другом языке)
                                    if (!$foundExisting) {
                                        $otherLang = ($newLang === 'ru') ? 'en' : 'ru';
                                        foreach ($existingFeatures[$featureId] as $existingValue) {
                                            // Если новое значение совпадает с существующим на другом языке
                                            if (isset($existingValue[$otherLang]) && $existingValue[$otherLang] === $newValue) {
                                                // Объединяем: новое значение перезаписывает существующее для нового языка,
                                                // остальные языки сохраняются
                                                $jsonValue = array_merge($existingValue, $jsonValue);
                                                $foundExisting = true;
                                                $wasMerged = true;
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // Если не нашли соответствующее существующее значение, но есть существующие значения,
                                    // используем первое существующее значение и обновляем только нужный язык
                                    // Это нужно для случая, когда значение изменилось, но мы все равно хотим сохранить другие языки
                                    if (!$foundExisting && !empty($existingFeatures[$featureId])) {
                                        $firstExisting = $existingFeatures[$featureId][0];
                                        // Объединяем: новое значение перезаписывает существующее для этого языка,
                                        // остальные языки сохраняются
                                        $jsonValue = array_merge($firstExisting, $jsonValue);
                                        $wasMerged = true;
                                    }
                                } else {
                                    // Приходят значения на нескольких языках, но все равно проверяем,
                                    // нет ли существующих значений, которые нужно сохранить
                                    // (на случай, если в существующих есть языки, которых нет в новых)
                                    foreach ($existingFeatures[$featureId] as $existingValue) {
                                        // Проверяем, совпадает ли новое значение с существующим
                                        $matches = false;
                                        foreach ($jsonValue as $lang => $val) {
                                            if (isset($existingValue[$lang]) && $existingValue[$lang] === $val) {
                                                $matches = true;
                                                break;
                                            }
                                        }
                                        if ($matches) {
                                            // Объединяем: новые значения перезаписывают существующие,
                                            // но сохраняем языки, которых нет в новых
                                            $jsonValue = array_merge($existingValue, $jsonValue);
                                            $wasMerged = true;
                                            break;
                                        }
                                    }
                                }
                            }
                            
                            // Убеждаемся, что есть значения для всех языков
                            if (!isset($jsonValue['ru']) && !isset($jsonValue['en'])) {
                                continue;
                            }
                            
                            // Если мы объединили с существующими значениями и пришло значение только на одном языке,
                            // проверяем, есть ли значение для другого языка. Если нет - устанавливаем равным отправленному
                            if ($wasMerged && count($jsonValue) === 1) {
                                $newLang = array_key_first($jsonValue);
                                $newValue = $jsonValue[$newLang];
                                $otherLang = ($newLang === 'ru') ? 'en' : 'ru';
                                
                                // Если значение для другого языка отсутствует, устанавливаем его равным отправленному
                                if (!isset($jsonValue[$otherLang])) {
                                    $jsonValue[$otherLang] = $newValue;
                                }
                            } elseif (!$wasMerged) {
                                // Дублируем значения только если мы НЕ объединили с существующими значениями
                                // (т.е. если это новое значение, а не обновление существующего)
                                if (!isset($jsonValue['ru']) && isset($jsonValue['en'])) {
                                    $jsonValue['ru'] = $jsonValue['en'];
                                }
                                if (!isset($jsonValue['en']) && isset($jsonValue['ru'])) {
                                    $jsonValue['en'] = $jsonValue['ru'];
                                }
                            }
                            
                            $finalJsonValue = json_encode($jsonValue, JSON_UNESCAPED_UNICODE);
                            $this->model->features()->attach($featureId, ['value' => $finalJsonValue]);
                        }
                    }
                } 
                // Обратная совместимость: старый формат со строкой value, разделенной ;;;
                elseif (isset($feature['value']) && is_string($feature['value'])) {
                    $values = explode(";;;", $feature['value']);

                    foreach ($values as $index => $value) {
                        $value = trim($value);
                        if ($value === '' || $value === 'null') {
                            continue;
                        }

                        // Преобразуем в JSON формат для обратной совместимости
                        // Сначала проверяем существующие значения, чтобы сохранить значения других языков
                        $wasMerged = false;
                        $jsonValue = null;
                        
                        // Если есть существующие значения, пытаемся сохранить значения других языков
                        if (!empty($existingFeatures) && isset($existingFeatures[$featureId])) {
                            // Ищем соответствующее существующее значение
                            // Сопоставляем по значению на русском языке
                            $foundExisting = false;
                            foreach ($existingFeatures[$featureId] as $existingValue) {
                                if (isset($existingValue['ru']) && $existingValue['ru'] === $value) {
                                    // Нашли соответствующее значение, объединяем
                                    // Новое значение на русском перезаписывает существующее,
                                    // но сохраняем значения других языков
                                    $jsonValue = array_merge($existingValue, ['ru' => $value]);
                                    $foundExisting = true;
                                    $wasMerged = true;
                                    break;
                                }
                            }
                            
                            // Если не нашли по русскому, но есть значение по индексу, используем его
                            if (!$foundExisting && isset($existingFeatures[$featureId][$index])) {
                                $existingValue = $existingFeatures[$featureId][$index];
                                // Объединяем: новое значение на русском перезаписывает существующее,
                                // но сохраняем значения других языков
                                $jsonValue = array_merge($existingValue, ['ru' => $value]);
                                $wasMerged = true;
                            }
                        }
                        
                        // Если не нашли существующее значение, создаем новое с обоими языками одинаковыми
                        if ($jsonValue === null) {
                            $jsonValue = [
                                'ru' => $value,
                                'en' => $value
                            ];
                        } elseif ($wasMerged) {
                            // Если мы объединили с существующим значением, проверяем, есть ли значение для другого языка
                            // Если нет - устанавливаем его равным отправленному
                            if (!isset($jsonValue['en'])) {
                                $jsonValue['en'] = $value;
                            }
                        }

                        $finalJsonValue = json_encode($jsonValue, JSON_UNESCAPED_UNICODE);
                        $this->model->features()->attach($featureId, ['value' => $finalJsonValue]);
                    }
                }
            }
        }
    }

    /**
     * Returns the breadcrumbs of the product section.
     */
    private function getBreadcrumbs($sectionId): string
    {
        $parts = [];

        while (true) {
            $section = Section::find($sectionId);

            if (!$section) {
                break;
            }

            $section = $section->toArray();

            $section = Arr::onlyActiveLang(
                ['name'],
                App::getLocale(),
                [$section]
            )[0];

            $parts[] = $section['name'];

            if ($section['parent_section_id'] === null) {
                break;
            }

            $sectionId = $section['parent_section_id'];
        }

        return implode(' / ', array_reverse($parts));
    }

    // SIMILAR PRODUCTS SUPPORT
    public function addSimilarProduct(int $productId, int $similarId): bool
    {
        $pid = $productId;
        $aid = $similarId;

        if ($pid == $aid) {
            return false;
        }

        if ($this->checkRecord($pid, $aid) === 0) {
            $this->saveRecord($pid, $aid);
        }

        if ($this->checkRecord($aid, $pid) === 0) {
            $this->saveRecord($aid, $pid);
        }

        $productsWithAnalog = SimilarProduct::where('similar_product_id', $pid)->get(['product_id']);

        foreach ($productsWithAnalog as $product) {
            if ($this->checkRecord($product['product_id'], $aid) === 0) {
                $this->saveRecord($product['product_id'], $aid);
            }
        }

        $productsWithAnalog = SimilarProduct::where('similar_product_id', $pid)->get(['product_id']);

        foreach ($productsWithAnalog as $product) {
            if ($this->checkRecord($aid, $product['product_id']) === 0) {
                $this->saveRecord($aid, $product['product_id']);
            }
        }

        $productsWithAnalog = SimilarProduct::where('similar_product_id', $aid)->get(['product_id']);

        foreach ($productsWithAnalog as $product) {
            if ($this->checkRecord($product['product_id'], $pid) === 0) {
                $this->saveRecord($product['product_id'], $pid);
            }
        }

        $productsWithAnalog = SimilarProduct::where('similar_product_id', $aid)->get(['product_id']);

        foreach ($productsWithAnalog as $product) {
            if ($this->checkRecord($pid, $product['product_id']) === 0) {
                $this->saveRecord($pid, $product['product_id']);
            }
        }

        $items = SimilarProduct::where('product_id', $aid)->get();

        $chainItems = [];
        foreach ($items as $item) {
            $chainItems[] = $item['similar_product_id'];
        }

        foreach ($chainItems as $item1) {
            foreach ($chainItems as $item2) {
                if ($this->checkRecord($item1, $item2) === 0) {
                    $this->saveRecord($item1, $item2);
                }
            }
        }

        return true;
    }

    public function deleteSimilarProduct(int $productId, int $similarId): void
    {
        $pid = $productId;
        $aid = $similarId;

        $this->deleteRecord($pid, $aid);
        $this->deleteRecord($aid, $pid);

        $items = SimilarProduct::where('similar_product_id', $aid)->get();

        foreach ($items as $item) {
            $this->deleteRecord($item['product_id'], $aid);
            $this->deleteRecord($aid, $item['product_id']);
        }
    }

    private function saveRecord($pid, $aid)
    {
        $chainRecord = new SimilarProduct();
        $chainRecord->product_id = $pid;
        $chainRecord->similar_product_id  = $aid;
        $chainRecord->save();
    }

    private function deleteRecord($pid, $aid)
    {
        if ((int)$pid === (int)$aid) {
            return false;
        }

        SimilarProduct::where('similar_product_id', $aid)->where('product_id', $pid)->delete();
    }

    private function checkRecord($pid, $aid)
    {
        if ((int)$pid === (int)$aid) {
            return 1;
        }

        return SimilarProduct::where('similar_product_id', $aid)
            ->where('product_id', $pid)
            ->whereNull('deleted_at')
            ->count();
    }

    private function copyDataSimilarProducts($whatCopy, $productId, $data)
    {
        $products = SimilarProduct::where('product_id', $productId)->get(['similar_product_id']);

        foreach ($products as $product) {
            $similarProduct = Product::find($product['similar_product_id']);

            if (!$similarProduct) {
                continue;
            }

            // Update product data
            if ($whatCopy['brand'] == 'Y') {
                $similarProduct->brand_id = $data['brand_id'];
            }

            if ($whatCopy['desc'] == 'Y') {
                $similarProduct->overview_text = $data['overview_text'];
            }

            if ($whatCopy['desc'] == 'Y' || $whatCopy['brand'] == 'Y') {
                $similarProduct->save();
            }

            // Add related products
            if ($whatCopy['related_products'] == 'Y') {
                $similarProduct->relatedProducts()->delete();

                $relatedProducts = [];

                if (isset($data['related_products'])) {
                    foreach ($data['related_products'] as $related_product_id) {
                        if ($similarProduct->id != $related_product_id) {
                            $relatedProducts[] = [
                                'product_id' => $similarProduct->id,
                                'related_product_id' => $related_product_id,
                            ];
                        }
                    }

                    $similarProduct->relatedProducts()->createMany($relatedProducts);
                }
            }

            // Add features
            if ($whatCopy['features'] == 'Y') {
                $similarProduct->features()->detach();

                if (isset($data['features'])) {
                    // Используем ту же логику, что и в методе features()
                    foreach ($data['features'] as $feature) {
                        $featureId = $feature['feature_id'];
                        
                        // Поддержка нового формата с многоязычными значениями
                        if (isset($feature['values']) && is_array($feature['values'])) {
                            // Новый формат: values - массив с lang и value
                            // Поддержка разделения по ";;;" - несколько значений для одного параметра
                            
                            // Сначала собираем все значения по языкам, разделяя по ";;;"
                            $valuesByLang = [];
                            foreach ($feature['values'] as $valueData) {
                                if (!isset($valueData['lang']) || !isset($valueData['value'])) {
                                    continue;
                                }
                                
                                $lang = $valueData['lang'];
                                $value = trim($valueData['value']);
                                
                                if ($value === '' || $value === 'null') {
                                    continue;
                                }
                                
                                // Разделяем по ";;;" если есть
                                $parts = explode(";;;", $value);
                                $valuesByLang[$lang] = array_map('trim', $parts);
                            }
                            
                            // Если есть значения, разделенные по ";;;", обрабатываем их отдельно
                            $hasMultipleValues = false;
                            foreach ($valuesByLang as $langValues) {
                                if (count($langValues) > 1) {
                                    $hasMultipleValues = true;
                                    break;
                                }
                            }
                            
                            if ($hasMultipleValues) {
                                // Определяем максимальное количество значений
                                $maxCount = 0;
                                foreach ($valuesByLang as $langValues) {
                                    $maxCount = max($maxCount, count($langValues));
                                }
                                
                                // Создаем отдельные записи для каждого значения
                                for ($i = 0; $i < $maxCount; $i++) {
                                    $jsonValue = [];
                                    
                                    // Собираем значение для каждого языка по индексу
                                    foreach ($valuesByLang as $lang => $langValues) {
                                        if (isset($langValues[$i]) && trim($langValues[$i]) !== '' && trim($langValues[$i]) !== 'null') {
                                            $partValue = trim($langValues[$i]);
                                            
                                            // Проверяем, не является ли значение уже JSON
                                            $decoded = json_decode($partValue, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                // Если это JSON, объединяем с существующими значениями
                                                $jsonValue = array_merge($jsonValue, $decoded);
                                            } else {
                                                $jsonValue[$lang] = $partValue;
                                            }
                                        }
                                    }
                                    
                                    // Пропускаем пустые значения
                                    if (empty($jsonValue)) {
                                        continue;
                                    }
                                    
                                    // Убеждаемся, что есть значения для всех языков (если нет, дублируем ru)
                                    if (!isset($jsonValue['ru']) && !isset($jsonValue['en'])) {
                                        continue;
                                    }
                                    
                                    // Если нет значения для какого-то языка, используем значение из ru
                                    if (!isset($jsonValue['ru']) && isset($jsonValue['en'])) {
                                        $jsonValue['ru'] = $jsonValue['en'];
                                    }
                                    if (!isset($jsonValue['en']) && isset($jsonValue['ru'])) {
                                        $jsonValue['en'] = $jsonValue['ru'];
                                    }
                                    
                                    $finalJsonValue = json_encode($jsonValue, JSON_UNESCAPED_UNICODE);
                                    $similarProduct->features()->attach($featureId, ['value' => $finalJsonValue]);
                                }
                            } else {
                                // Стандартная обработка без разделения по ";;;"
                                // Группируем значения по уникальным комбинациям (для объединения значений разных языков)
                                $groupedValues = [];
                                
                                foreach ($feature['values'] as $valueData) {
                                    if (!isset($valueData['lang']) || !isset($valueData['value'])) {
                                        continue;
                                    }
                                    
                                    $lang = $valueData['lang'];
                                    $value = trim($valueData['value']);
                                    
                                    if ($value === '' || $value === 'null') {
                                        continue;
                                    }
                                    
                                    $decoded = json_decode($value, true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $key = $decoded['ru'] ?? $value;
                                        if (!isset($groupedValues[$key])) {
                                            $groupedValues[$key] = $decoded;
                                        } else {
                                            $groupedValues[$key] = array_merge($groupedValues[$key], $decoded);
                                        }
                                    } else {
                                        $key = $value;
                                        if (!isset($groupedValues[$key])) {
                                            $groupedValues[$key] = [];
                                        }
                                        $groupedValues[$key][$lang] = $value;
                                    }
                                }
                                
                                foreach ($groupedValues as $jsonValue) {
                                    if (!isset($jsonValue['ru']) && !isset($jsonValue['en'])) {
                                        continue;
                                    }
                                    
                                    if (!isset($jsonValue['ru']) && isset($jsonValue['en'])) {
                                        $jsonValue['ru'] = $jsonValue['en'];
                                    }
                                    if (!isset($jsonValue['en']) && isset($jsonValue['ru'])) {
                                        $jsonValue['en'] = $jsonValue['ru'];
                                    }
                                    
                                    $finalJsonValue = json_encode($jsonValue, JSON_UNESCAPED_UNICODE);
                                    $similarProduct->features()->attach($featureId, ['value' => $finalJsonValue]);
                                }
                            }
                        } 
                        // Обратная совместимость: старый формат
                        elseif (isset($feature['value'])) {
                            // Если значение уже в JSON формате, используем как есть
                            $decoded = json_decode($feature['value'], true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $similarProduct->features()->attach($featureId, ['value' => $feature['value']]);
                            } else {
                                // Преобразуем в JSON формат
                                $values = explode(";;;", $feature['value']);
                                foreach ($values as $value) {
                                    $value = trim($value);
                                    if ($value === '' || $value === 'null') {
                                        continue;
                                    }
                                    $jsonValue = json_encode([
                                        'ru' => $value,
                                        'en' => $value
                                    ], JSON_UNESCAPED_UNICODE);
                                    $similarProduct->features()->attach($featureId, ['value' => $jsonValue]);
                                }
                            }
                        }
                    }
                }
            }

            // Add generator data
            $adGeneratorData = AdGenerator::where('product_id', $similarProduct->id)->first(['alt_names', 'pn']);

            if ($whatCopy['alt_names'] == 'Y' && $whatCopy['pn'] == 'Y') {
                $similarProduct->adGenerator()->delete();

                $similarProduct->adGenerator()->create([
                    'alt_names' => $data['ad_generator']['alt_names'],
                    'pn'        => $data['ad_generator']['pn'],
                ]);
            }

            if ($whatCopy['alt_names'] == 'Y' && $whatCopy['pn'] != 'Y') {
                $similarProduct->adGenerator()->delete();

                $similarProduct->adGenerator()->create([
                    'alt_names' => $data['ad_generator']['alt_names'],
                    'pn'        => (isset($adGeneratorData)) ? $adGeneratorData['pn'] : '',
                ]);
            }

            if ($whatCopy['alt_names'] != 'Y' && $whatCopy['pn'] == 'Y') {
                $similarProduct->adGenerator()->delete();

                $similarProduct->adGenerator()->create([
                    'alt_names' => (isset($adGeneratorData)) ? $adGeneratorData['alt_names'] : '',
                    'pn'        => $data['ad_generator']['pn'],
                ]);
            }
        }
    }

    /**
     * Handles where_sn field for product.
     */
    private function whereSn(array $data): void
    {
        // Delete existing where_sn records for this product
        ProductWhereSn::where('product_id', $this->model->id)->delete();

        // Create new where_sn record if description is provided and not empty
        if (isset($data['where_sn']) && !empty(trim($data['where_sn']))) {
            ProductWhereSn::create([
                'product_id' => $this->model->id,
                'where_desc' => $data['where_sn'],
            ]);
        }
        // If where_sn is not set or is empty, just delete existing records (already done above)
    }

    // Возвращает склады с которых идет доставка в текущий город
    private function getCityWarehouses(array $wh): array
    {
        $userCityId = \Helper::getUserCityId();
        $activeWarehouses = array_keys($wh);

        // Определим ID курьерского города
        $courierCity = CourierCity::where('city_id', $userCityId)->first();

        if ($courierCity) {
            // Определим с каких складов идет доставка в город назначения
            $warehouses = DB::table('courier_city_warehouse')
                ->where('courier_city_id', $courierCity->id)
                ->whereIn('warehouse_id', $activeWarehouses)
                ->select('warehouse_id')
                ->get()
                ->pluck('warehouse_id')
                ->toArray();

            // Доставка со склада Москвы доступна в любой город
            $warehouses[] = 1;
        } else {
            $warehouses = [1]; // только со склада Москвы
        }

        return $warehouses;
    }

    /**
     * Объединяет существующие многоязычные значения с новыми значениями из запроса.
     * Сохраняет значения других языков, которые не были переданы в запросе.
     *
     * @param string $fieldName Название поля (name или overview_text)
     * @param string $newValueJson Новое значение в формате JSON
     * @return string Объединенное значение в формате JSON
     */
    private function mergeMultilingualField(string $fieldName, string $newValueJson): string
    {
        // Получаем текущее значение из базы данных
        $currentValueJson = $this->model->getAttribute($fieldName);
        
        // Если текущего значения нет, возвращаем новое
        if (empty($currentValueJson)) {
            return $newValueJson;
        }
        
        // Декодируем текущее значение
        $currentValue = json_decode($currentValueJson, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($currentValue)) {
            // Если текущее значение не является валидным JSON, возвращаем новое
            return $newValueJson;
        }
        
        // Декодируем новое значение
        $newValue = json_decode($newValueJson, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($newValue)) {
            // Если новое значение не является валидным JSON, возвращаем текущее
            return $currentValueJson;
        }
        
        // Объединяем: новые значения перезаписывают существующие для соответствующих языков
        // Существующие значения других языков сохраняются
        $mergedValue = array_merge($currentValue, $newValue);
        
        // Возвращаем объединенное значение в формате JSON
        return json_encode($mergedValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Объединяет многоязычные поля для product_materials_urls (title и url)
     * Сохраняет значения других языков, обновляя только активный язык
     * 
     * @param string|null $currentValueJson Текущее значение в JSON формате
     * @param string $newValue Новое значение для активного языка
     * @param string $activeLocale Активный язык (ru, en и т.д.)
     * @return string Объединенное значение в JSON формате
     */
    private function mergeMultilingualFieldForMaterialsUrls(?string $currentValueJson, string $newValue, string $activeLocale): string
    {
        // Если текущего значения нет, создаем JSON только с активным языком
        if (empty($currentValueJson)) {
            return json_encode([$activeLocale => $newValue], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        
        // Декодируем текущее значение
        $currentValue = json_decode($currentValueJson, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($currentValue)) {
            // Если текущее значение не является валидным JSON, создаем новый JSON с активным языком
            return json_encode([$activeLocale => $newValue], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        
        // Обновляем только значение для активного языка, сохраняя остальные
        $currentValue[$activeLocale] = $newValue;
        
        // Возвращаем объединенное значение в формате JSON
        return json_encode($currentValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Получает описание товара по артикулу.
     *
     * @param string $article Артикул товара
     * @return string|null
     */
    public function getDescriptionByArticle(string $article): ?string
    {
        $product = $this->model
            ->where('vendor_code', $article)
            ->select('overview_text')
            ->first();

        if ($product) {
            $locale = app()->getLocale();
            $overviewText = json_decode($product->overview_text, true);
            $product->overview_text = $overviewText[$locale] ?? '';
        }

        return $product ? $product->overview_text : null;
    }

    /**
     * Получает значение для материала с fallback на ru/en
     * Если текущий язык ru и нет данных на ru, возвращает данные на en
     * Если текущий язык en и нет данных на en, возвращает данные на ru
     * 
     * @param array $data Массив с данными для разных языков
     * @param string $activeLocale Активный язык
     * @return string Значение для активного языка или fallback
     */
    private function getMaterialValueWithFallback(array $data, string $activeLocale): string
    {
        // Если есть значение для активного языка, возвращаем его
        if (isset($data[$activeLocale]) && !empty($data[$activeLocale])) {
            return $data[$activeLocale];
        }
        
        // Fallback: если активный язык ru, возвращаем en, и наоборот
        $fallbackLocale = ($activeLocale === 'ru') ? 'en' : 'ru';
        if (isset($data[$fallbackLocale]) && !empty($data[$fallbackLocale])) {
            return $data[$fallbackLocale];
        }
        
        // Если нет данных ни на одном языке, возвращаем пустую строку
        return '';
    }
}



