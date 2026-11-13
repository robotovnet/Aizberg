<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Modules\Catalog\Entities\Feature;
use Modules\Catalog\Entities\GroupFeature;
use Modules\Catalog\Entities\Group;
use Modules\Catalog\Entities\Product;
use Modules\Catalog\Repositories\GroupRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\Helper;
use Modules\Settings\Entities\Settings;
use Str;
use DB;

/**
 * Class EloquentGroupRepository
 * @package Modules\Catalog\Repositories\Eloquent
 */
class EloquentGroupRepository implements GroupRepository
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
            throw new ModelNotFoundException('Group not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        $data['overview_text'] = preg_replace('/\n/', '\n', $data['overview_text']);
        
        // Обработка поля where_sn_desc
        if (isset($data['where_sn_desc'])) {
            $data['where_sn_desc'] = preg_replace('/\n/', '\n', $data['where_sn_desc']);
            Helper::havePhraseOnDefaultLang($data['where_sn_desc'], 'where_sn_desc');
        }

        Helper::havePhraseOnDefaultLang($data['plural_name'], 'plural_name');
        Helper::havePhraseOnDefaultLang($data['singular_name'], 'singular_name');
        Helper::havePhraseOnDefaultLang($data['overview_text'], 'overview_text');
        
        $this->checkNameUnique($data['plural_name'], 'plural_name');

        $this->model = $this->model->create($data);

        $features = $this->getFeaturesData($data['features']);
        $this->model->features()->createMany($features);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        // Обработка поля plural_name: объединяем с существующими данными
        if (isset($attributes['plural_name'])) {
            $currentPluralName = $this->model->plural_name;
            $existingPluralNames = [];
            
            if ($currentPluralName !== null) {
                $decoded = json_decode($currentPluralName, true);
                if (is_array($decoded)) {
                    $existingPluralNames = $decoded;
                }
            }
            
            $newPluralNames = json_decode($attributes['plural_name'], true);
            if (is_array($newPluralNames)) {
                $existingPluralNames = array_merge($existingPluralNames, $newPluralNames);
            }
            
            $attributes['plural_name'] = Str::getLangJson($existingPluralNames);
        }
        
        // Обработка поля singular_name: объединяем с существующими данными
        if (isset($attributes['singular_name'])) {
            $currentSingularName = $this->model->singular_name;
            $existingSingularNames = [];
            
            if ($currentSingularName !== null) {
                $decoded = json_decode($currentSingularName, true);
                if (is_array($decoded)) {
                    $existingSingularNames = $decoded;
                }
            }
            
            $newSingularNames = json_decode($attributes['singular_name'], true);
            if (is_array($newSingularNames)) {
                $existingSingularNames = array_merge($existingSingularNames, $newSingularNames);
            }
            
            $attributes['singular_name'] = Str::getLangJson($existingSingularNames);
        }
        
        // Обработка поля overview_text: объединяем с существующими данными
        if (isset($attributes['overview_text'])) {
            $currentOverviewText = $this->model->overview_text;
            $existingOverviewTexts = [];
            
            if ($currentOverviewText !== null) {
                $decoded = json_decode($currentOverviewText, true);
                if (is_array($decoded)) {
                    $existingOverviewTexts = $decoded;
                }
            }
            
            $newOverviewTexts = json_decode($attributes['overview_text'], true);
            if (is_array($newOverviewTexts)) {
                // Применяем preg_replace к новым значениям перед объединением
                foreach ($newOverviewTexts as $lang => $value) {
                    $newOverviewTexts[$lang] = preg_replace('/\n/', '\n', $value);
                }
                $existingOverviewTexts = array_merge($existingOverviewTexts, $newOverviewTexts);
            }
            
            $attributes['overview_text'] = Str::getLangJson($existingOverviewTexts);
        }
        
        // Обработка поля where_sn_desc: объединяем с существующими данными
        if (isset($attributes['where_sn_desc'])) {
            $currentWhereSnDesc = $this->model->where_sn_desc;
            $existingWhereSnDescs = [];
            
            if ($currentWhereSnDesc !== null) {
                $decoded = json_decode($currentWhereSnDesc, true);
                if (is_array($decoded)) {
                    $existingWhereSnDescs = $decoded;
                }
            }
            
            $newWhereSnDescs = json_decode($attributes['where_sn_desc'], true);
            if (is_array($newWhereSnDescs)) {
                // Применяем preg_replace к новым значениям перед объединением
                foreach ($newWhereSnDescs as $lang => $value) {
                    $newWhereSnDescs[$lang] = preg_replace('/\n/', '\n', $value);
                }
                $existingWhereSnDescs = array_merge($existingWhereSnDescs, $newWhereSnDescs);
            }
            
            $attributes['where_sn_desc'] = Str::getLangJson($existingWhereSnDescs);
            Helper::havePhraseOnDefaultLang($attributes['where_sn_desc'], 'where_sn_desc');
        }

        Helper::havePhraseOnDefaultLang($attributes['plural_name'], 'plural_name');
        Helper::havePhraseOnDefaultLang($attributes['singular_name'], 'singular_name');
        Helper::havePhraseOnDefaultLang($attributes['overview_text'], 'overview_text');
        
        $this->checkNameUnique($attributes['plural_name'], 'plural_name', $this->model->id);

        // Получаем текущие feature_id группы перед обновлением
        $currentFeatureIds = $this->model->features()->pluck('feature_id')->toArray();

        $this->model->update($attributes);

        // Получаем новые feature_id группы после обновления
        $newFeatureIds = collect($attributes['features'])->pluck('feature_id')->toArray();

        // Находим удаленные параметры
        $removedFeatureIds = array_diff($currentFeatureIds, $newFeatureIds);

        // Удаляем значения удаленных параметров у всех товаров группы
        if (!empty($removedFeatureIds)) {
            $this->removeDeletedFeaturesFromProducts($removedFeatureIds);
        }

        $this->model->features()->delete();
        $this->model->features()->createMany($this->getFeaturesData($attributes['features']));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        $this->model->features()->delete();
        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(?string $search): array
    {
        $all = Group::withCount('features')
            ->orderBy('plural_name')
            ->when($search !== null && $search !== '', function ($query) use ($search) {
                return $query->where('plural_name', 'LIKE', '%' . $search . '%')
                           ->orWhere('singular_name', 'LIKE', '%' . $search . '%');
            })
            ->get(Group::SELECT_ATTRIBUTES)
            ->toArray();

        foreach ($all as &$group) {
            unset($group['created_at'], $group['updated_at'], $group['deleted_at']);
        }

        return Arr::onlyActiveLang([
            'plural_name', 'singular_name', 'overview_text'], App::getLocale(), $all);
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $data = Group::with(['features' => function ($query) {
            $query->select(GroupFeature::SELECT_ATTRIBUTES);
        }])
        ->with(['features.featureData' => function ($query) {
            $query->select(Feature::SELECT_ATTRIBUTES);
        }])
        ->find($this->model->id, Group::SELECT_ATTRIBUTES)
        ->toArray();

        $data['features'] = Arr::onlyActiveLang(
            ['default_value', 'overview_text'],
            App::getLocale(),
            $data['features']
        );

        foreach ($data['features'] as &$feature) {
            
            if (isset($feature['feature_data'])) {
                $feature['feature_data'] = Arr::onlyActiveLang(
                    ['name', 'unit'],
                    App::getLocale(),
                    $feature['feature_data']
                );

                foreach ($feature['feature_data'] as &$featureData) {
                    if (in_array($featureData['type'], ['radio', 'checkbox'])) {
                        $orderBy = ($featureData['sort_type'] == 'string') ? 'value asc' : 'value * 1 asc';

                        $presetValues = DB::table('feature_product')
                            ->where('feature_id', $feature['feature_id'])
                            ->distinct()
                            ->orderByRaw($orderBy)
                            ->get(['value'])
                            ->pluck('value')
                            ->toArray();
                        
                        // Локализуем значения preset_values
                        $locale = App::getLocale();
                        $localizedPresetValues = [];
                        foreach ($presetValues as $valueJson) {
                            $decoded = json_decode($valueJson, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $localizedPresetValues[] = $decoded[$locale] ?? $decoded['ru'] ?? $valueJson;
                            } else {
                                $localizedPresetValues[] = $valueJson;
                            }
                        }
                        
                        $featureData['preset_values'] = array_unique($localizedPresetValues);
                    }
                }
            }
        }

        $result = Arr::onlyActiveLang(['plural_name', 'singular_name', 'overview_text', 'where_sn_desc'], App::getLocale(), [$data])[0];
        
        return $result;
    }

    /**
     * Generates array with features.
     */
    private function getFeaturesData(array $data): array
    {
        $features = [];

        foreach ($data as $key => $feature) {
            foreach ($feature['overview_texts'] as &$text) {
                $text['value'] = (is_null($text['value'])) ? '' : preg_replace('/\n/', '\n', $text['value']);
            }

            $features[$key]['feature_id']    = $feature['feature_id'];
            $features[$key]['is_required']   = $feature['is_required'];
            $features[$key]['is_main']       = $feature['is_main'];
            $features[$key]['order']         = $feature['order'];
            $features[$key]['overview_text'] = Str::getNamesJson($feature['overview_texts']);
            $features[$key]['default_value'] = Str::getNamesJson($feature['values']);
        }

        return $features;
    }

    /**
     * Checks the group name for uniqueness.
     * @throws ValidationException
     */
    private function checkNameUnique(string $jsonName, string $field, int $exceptId = 0): void
    {
        $langCode = Settings::where('code', 'DEFAULT_LANGUAGE')->first()->toArray()['value'];
        $decodedName = json_decode($jsonName, true);
        
        // Проверяем наличие ключа языка по умолчанию перед обращением
        if (!is_array($decodedName) || !isset($decodedName[$langCode])) {
            // Если нет данных для языка по умолчанию, пропускаем проверку уникальности
            return;
        }
        
        $name = mb_strtolower($decodedName[$langCode]);

        $records = Group::where('id', '!=', $exceptId)->get();
        foreach ($records as $item) {
            $itemName = json_decode($item[$field], true);

            if (isset($itemName[$langCode]) && mb_strtolower($itemName[$langCode]) == $name) {
                throw ValidationException::withMessages(
                    ['group_exist' => 'Тип товаров с данным именем уже существует']
                );
            }
        }
    }

    /**
     * Удаляет значения удаленных параметров у всех товаров группы.
     * 
     * @param array $removedFeatureIds Массив ID удаленных параметров
     */
    private function removeDeletedFeaturesFromProducts(array $removedFeatureIds): void
    {
        // Получаем все товары, принадлежащие данной группе
        $productIds = Product::where('product_group_id', $this->model->id)
            ->pluck('id')
            ->toArray();

        if (!empty($productIds)) {
            // Удаляем значения удаленных параметров из таблицы feature_product
            DB::table('feature_product')
                ->whereIn('product_id', $productIds)
                ->whereIn('feature_id', $removedFeatureIds)
                ->delete();
        }
    }
}
