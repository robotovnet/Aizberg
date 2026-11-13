<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Modules\Catalog\Entities\Feature;
use Modules\Catalog\Entities\Filter;
use Modules\Catalog\Entities\GroupFeature;
use Modules\Catalog\Repositories\FeatureRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\Helper;
use Modules\Catalog\Entities\Group;
use Modules\Catalog\Entities\Product;
use Modules\Settings\Entities\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class EloquentFeatureRepository
 * @package Modules\Catalog\Repositories\Eloquent
 */
class EloquentFeatureRepository implements FeatureRepository
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
            throw new ModelNotFoundException('Feature not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        // Данные name и unit уже приходят в JSON формате из контроллера
        // Проверяем наличие значения для языка по умолчанию
        Helper::havePhraseOnDefaultLang($data['name'], 'name');
        if (isset($data['unit']) && $data['unit'] !== null) {
            Helper::havePhraseOnDefaultLang($data['unit'], 'unit');
        }

        $this->checkNameUnique($data['name']);

        $this->model = $this->model->create($data);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        // Обработка поля name: объединяем с существующими данными
        if (isset($attributes['name'])) {
            // Получаем текущее значение из базы данных
            $currentName = $this->model->name;
            $existingNames = [];
            
            if ($currentName !== null) {
                $decoded = json_decode($currentName, true);
                if (is_array($decoded)) {
                    $existingNames = $decoded;
                }
            }
            
            // Если name пришел как JSON строка (уже обработанные данные из контроллера)
            $newNames = json_decode($attributes['name'], true);
            if (is_array($newNames)) {
                // Объединяем существующие данные с новыми (новые перезаписывают старые для конкретного языка)
                $existingNames = array_merge($existingNames, $newNames);
            }
            
            // Преобразуем обратно в JSON
            $attributes['name'] = json_encode($existingNames, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // Обработка поля unit: объединяем с существующими данными
        if (isset($attributes['unit'])) {
            // Получаем текущее значение из базы данных
            $currentUnit = $this->model->unit;
            $existingUnits = [];
            
            if ($currentUnit !== null) {
                $decoded = json_decode($currentUnit, true);
                if (is_array($decoded)) {
                    $existingUnits = $decoded;
                }
            }
            
            // Если unit пришел как JSON строка (уже обработанные данные из контроллера)
            $newUnits = json_decode($attributes['unit'], true);
            if (is_array($newUnits)) {
                // Объединяем существующие данные с новыми (новые перезаписывают старые для конкретного языка)
                $existingUnits = array_merge($existingUnits, $newUnits);
            }
            
            // Преобразуем обратно в JSON
            $attributes['unit'] = json_encode($existingUnits, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        Helper::havePhraseOnDefaultLang($attributes['name'], 'name');
        if (isset($attributes['unit']) && $attributes['unit'] !== null) {
            Helper::havePhraseOnDefaultLang($attributes['unit'], 'unit');
        }

        $this->checkNameUnique($attributes['name'], $this->model->id);

        $this->model->update($attributes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        GroupFeature::where('feature_id', $this->model->id)->delete();
        Filter::where('feature_id', $this->model->id)->delete();

        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(?string $search): array
    {
        $locale = App::getLocale();
        $defaultLangCode = Settings::where('code', 'DEFAULT_LANGUAGE')->first()->toArray()['value'] ?? 'ru';
        
        // Сортировка по значению name для активного языка, с fallback на язык по умолчанию
        $orderByRaw = "COALESCE(
            JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}')),
            JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$defaultLangCode}')),
            name
        ) ASC";
        
        $all = Feature::orderByRaw($orderByRaw)
            ->when($search !== null && $search !== '', function ($query) use ($search, $locale, $defaultLangCode) {
                return $query->where(function($q) use ($search, $locale, $defaultLangCode) {
                    // Поиск в JSON для активного языка
                    $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}')) LIKE ?", ['%' . $search . '%'])
                      // Поиск в JSON для языка по умолчанию
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$defaultLangCode}')) LIKE ?", ['%' . $search . '%'])
                      // Обратная совместимость со старыми данными
                      ->orWhere('name', 'LIKE', '%' . $search . '%');
                });
            })
            ->get(Feature::SELECT_ATTRIBUTES)
            ->toArray();

        return Arr::onlyActiveLang(['name', 'unit'], App::getLocale(), $all);
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $data = Feature::find($this->model->id, Feature::SELECT_ATTRIBUTES)->toArray();
        $groups = GroupFeature::where('feature_id', $this->model->id)->get();

        $usedForProductGroups = [];
        $locale = App::getLocale();
        $defaultLangCode = Settings::where('code', 'DEFAULT_LANGUAGE')->first()->toArray()['value'] ?? 'ru';
        
        foreach ($groups as $group) {
            $productGroup = Group::find($group->group_id);
            if ($productGroup) {
                $decodedName = json_decode($productGroup->plural_name, true);
                $productGroupName = '';
                
                if (is_array($decodedName)) {
                    // Сначала пытаемся получить значение для текущего языка
                    if (isset($decodedName[$locale])) {
                        $productGroupName = $decodedName[$locale];
                    } 
                    // Если нет значения для текущего языка, используем язык по умолчанию
                    elseif (isset($decodedName[$defaultLangCode])) {
                        $productGroupName = $decodedName[$defaultLangCode];
                    }
                    // Если и языка по умолчанию нет, берем первое доступное значение
                    elseif (!empty($decodedName)) {
                        $productGroupName = reset($decodedName);
                    }
                }
                
                // Если не удалось получить значение из JSON, используем исходное значение
                if ($productGroupName === '' && $productGroup->plural_name !== null) {
                    $productGroupName = $productGroup->plural_name;
                }
                
                $usedForProductGroups[$group->group_id] = $productGroupName;
            }
        }

        $data['used_for_product_groups'] = $usedForProductGroups;

        return Arr::onlyActiveLang(['name', 'unit'], App::getLocale(), [$data])[0];
    }

    /**
     * Checks the feature name for uniqueness.
     * @throws ValidationException
     */
    private function checkNameUnique(string $jsonName, int $exceptId = 0): void
    {
        $locale = App::getLocale();
        $decoded = json_decode($jsonName, true);
        
        if (!is_array($decoded) || !isset($decoded[$locale])) {
            throw ValidationException::withMessages(
                ['feature_name_missing' => 'Имя параметра должно быть указано для активного языка']
            );
        }
        
        $name = mb_strtolower($decoded[$locale]);

        $records = Feature::where('id', '!=', $exceptId)->get();
        foreach ($records as $item) {
            $itemName = json_decode($item['name'], true);

            if (isset($itemName[$locale]) && mb_strtolower($itemName[$locale]) == $name) {
                throw ValidationException::withMessages(
                    ['feature_exist' => 'Параметр с данным именем уже существует']
                );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getValues(): array
    {
        $orderBy = ($this->model->sort_type == 'string') ? 'value asc' : 'value * 1 asc';
        $locale = App::getLocale();

        $values = Arr::pluck(
            DB::table('feature_product')
                ->where('feature_id', $this->model->id)
                ->distinct()
                ->orderByRaw($orderBy)
                ->get(['value']),
            'value'
        );

        // Извлекаем значение только на активном языке из JSON
        return array_map(function ($value) use ($locale) {
            $decoded = json_decode($value, true);
            if (is_array($decoded) && isset($decoded[$locale])) {
                return $decoded[$locale];
            }
            // Если не удалось декодировать или нет значения для локали, возвращаем как есть
            return $value;
        }, $values);
    }

    /**
     * Обновляет значение в JSON только для активного языка, оставляя остальные языки без изменений.
     * Если значение не в JSON формате, конвертирует его в JSON.
     * 
     * @param string $currentValue Текущее значение (может быть JSON или обычная строка)
     * @param string $newValue Новое значение для активного языка
     * @param string $locale Активный язык (например, 'ru', 'en')
     * @return string Обновленное значение в JSON формате
     */
    private function updateValueForLocale(string $currentValue, string $newValue, string $locale): string
    {
        $decoded = json_decode($currentValue, true);
        
        // Если значение уже в JSON формате
        if (is_array($decoded)) {
            // Обновляем только значение для активного языка
            $decoded[$locale] = $newValue;
            return json_encode($decoded, JSON_UNESCAPED_UNICODE);
        }
        
        // Если значение не в JSON формате (старые данные), создаем JSON
        return json_encode([$locale => $newValue], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @inheritDoc
     */
    public function migrateValues(int $sourceFeatureId, int $targetFeatureId, ?string $currentValue = null, ?string $newValue = null): array
    {
        // Получаем активный язык клиента
        $locale = App::getLocale();
        
        // Проверяем существование исходного и целевого параметров
        $sourceFeature = Feature::find($sourceFeatureId);
        $targetFeature = Feature::find($targetFeatureId);

        if (!$sourceFeature) {
            throw new ModelNotFoundException('Исходный параметр не найден.');
        }

        if (!$targetFeature) {
            throw new ModelNotFoundException('Целевой параметр не найден.');
        }

        // Если параметры одинаковые, это обновление значения в том же параметре
        $isSameFeature = $sourceFeatureId === $targetFeatureId;

        // Для обновления в том же параметре обязательно нужно указать текущее и новое значение
        if ($isSameFeature) {
            if ($currentValue === null || $newValue === null) {
                return [
                    'migrated_count' => 0,
                    'message' => 'Для обновления значения в том же параметре необходимо указать текущее и новое значение'
                ];
            }

            if ($currentValue === $newValue) {
                return [
                    'migrated_count' => 0,
                    'message' => 'Текущее и новое значение не могут быть одинаковыми'
                ];
            }
        }

        // Получаем записи из feature_product для исходного параметра
        $query = DB::table('feature_product')
            ->where('feature_id', $sourceFeatureId);

        // Если указано текущее значение, фильтруем по нему с учетом активного языка
        if ($currentValue !== null) {
            $query->where(function($q) use ($currentValue, $locale) {
                // Поиск в JSON для активного языка
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(value, '$.{$locale}')) = ?", [$currentValue])
                  // Обратная совместимость со старыми данными (если значение не в JSON формате)
                  ->orWhere('value', $currentValue);
            });
        }

        $sourceRecords = $query->get(['product_id', 'value']);

        if ($sourceRecords->isEmpty()) {
            $message = $currentValue !== null 
                ? "Нет записей с значением '{$currentValue}' для миграции"
                : 'Нет данных для миграции';
            
            return [
                'migrated_count' => 0,
                'message' => $message
            ];
        }

        $migratedCount = 0;
        
        // Начинаем транзакцию
        DB::beginTransaction();

        try {
            if ($isSameFeature) {
                // Обновление значения в том же параметре
                // Обновляем только значение для активного языка в JSON
                foreach ($sourceRecords as $record) {
                    $updatedValue = $this->updateValueForLocale($record->value, $newValue, $locale);
                    
                    DB::table('feature_product')
                        ->where('feature_id', $sourceFeatureId)
                        ->where('product_id', $record->product_id)
                        ->where(function($q) use ($currentValue, $locale) {
                            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(value, '$.{$locale}')) = ?", [$currentValue])
                              ->orWhere('value', $currentValue);
                        })
                        ->update(['value' => $updatedValue]);
                    
                    $migratedCount++;
                }
            } else {
                // Перенос в другой параметр (оригинальная логика)
                foreach ($sourceRecords as $record) {
                    // Определяем значение для нового параметра
                    if ($newValue !== null) {
                        // Если указано новое значение, используем его для активного языка
                        $valueToSet = $this->updateValueForLocale($record->value, $newValue, $locale);
                    } else {
                        // Если новое значение не указано, просто копируем текущее значение
                        $valueToSet = $record->value;
                    }

                    // Проверяем, существует ли уже запись для этого товара и целевого параметра
                    $existingRecord = DB::table('feature_product')
                        ->where('product_id', $record->product_id)
                        ->where('feature_id', $targetFeatureId)
                        ->first();

                    if ($existingRecord) {
                        // Обновляем существующую запись: обновляем только значение для активного языка
                        if ($newValue !== null) {
                            // Если указано новое значение, используем его
                            $updatedValue = $this->updateValueForLocale($existingRecord->value, $newValue, $locale);
                        } else {
                            // Если новое значение не указано, берем значение для активного языка из исходной записи
                            $sourceDecoded = json_decode($record->value, true);
                            $sourceValueForLocale = is_array($sourceDecoded) && isset($sourceDecoded[$locale]) 
                                ? $sourceDecoded[$locale] 
                                : $record->value; // Если не JSON, берем как есть
                            $updatedValue = $this->updateValueForLocale($existingRecord->value, $sourceValueForLocale, $locale);
                        }
                        
                        DB::table('feature_product')
                            ->where('product_id', $record->product_id)
                            ->where('feature_id', $targetFeatureId)
                            ->update(['value' => $updatedValue]);
                    } else {
                        // Создаем новую запись
                        DB::table('feature_product')->insert([
                            'product_id' => $record->product_id,
                            'feature_id' => $targetFeatureId,
                            'value' => $valueToSet
                        ]);
                    }

                    $migratedCount++;
                }

                // Удаляем записи исходного параметра (только те, которые были мигрированы)
                $deleteQuery = DB::table('feature_product')
                    ->where('feature_id', $sourceFeatureId);
                
                if ($currentValue !== null) {
                    $deleteQuery->where(function($q) use ($currentValue, $locale) {
                        $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(value, '$.{$locale}')) = ?", [$currentValue])
                          ->orWhere('value', $currentValue);
                    });
                }
                
                $deleteQuery->delete();
            }

            DB::commit();

            $langCode = Settings::where('code', 'DEFAULT_LANGUAGE')->first()->toArray()['value'];
            $decodedName = json_decode($sourceFeature->name, true);
            $featureName = is_array($decodedName) && isset($decodedName[$langCode]) 
                ? $decodedName[$langCode] 
                : 'Неизвестный параметр';
            
            if ($isSameFeature) {
                $message = "Обновление значения параметра «{$featureName}» с '{$currentValue}' на '{$newValue}' завершено";
            } else {
                $message = "Миграция значений параметра «{$featureName}» завершена";
            }

            return [
                'migrated_count' => $migratedCount,
                'source_feature_id' => $sourceFeatureId,
                'target_feature_id' => $targetFeatureId,
                'current_value' => $currentValue,
                'new_value' => $newValue,
                'message' => $message
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'migrated_count' => 0,
                'error' => $e->getMessage(),
                'message' => 'Ошибка при миграции значений параметров'
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function getProductsByFeatureValue(int $featureId, string $value): array
    {
        // Получаем активный язык клиента
        $locale = App::getLocale();
        
        // Получаем ID товаров, у которых есть указанный параметр с указанным значением
        // Ищем значение в JSON для активного языка и обратная совместимость со старыми данными
        $productIds = DB::table('feature_product')
            ->where('feature_id', $featureId)
            ->where(function($query) use ($value, $locale) {
                // Поиск в JSON для активного языка
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(value, '$.{$locale}')) = ?", [$value])
                      // Обратная совместимость со старыми данными (если значение не в JSON формате)
                      ->orWhere('value', $value);
            })
            ->pluck('product_id')
            ->toArray();

        if (empty($productIds)) {
            return [];
        }

        // Получаем данные товаров
        $products = Product::whereIn('id', $productIds)
            ->select(['id', 'name', 'vendor_code'])
            ->get()
            ->toArray();

        // Формируем результат с URL для каждого товара
        $result = [];
        
        foreach ($products as $product) {
            $productName = json_decode($product['name'], true);
            $productNameRu = $productName['ru'] ?? $productName['en'] ?? '';
            
            $url = \Helper::productUrl($productNameRu, (int) $product['id']);
            
            $result[] = [
                'id' => $product['id'],
                'name' => $productNameRu,
                'vendor_code' => $product['vendor_code'],
                'url' => $url
            ];
        }

        return $result;
    }
}
