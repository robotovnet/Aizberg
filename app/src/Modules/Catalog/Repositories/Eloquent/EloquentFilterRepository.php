<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories\Eloquent;

use App\Helpers\LangPhrases;
use Modules\Catalog\Entities\Filter;
use Modules\Catalog\Repositories\FilterRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DB;

/**
 * Class EloquentFilterRepository
 * @package Modules\Catalog\Repositories\Eloquent
 */
class EloquentFilterRepository implements FilterRepository
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
            throw new ModelNotFoundException('Filter not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): array
    {
        $first = true;
        $ids = [];

        foreach ($data as $filter) {
            if ($first) {
                Filter::where('product_group_id', $filter['product_group_id'])->delete();
                $first = false;
            }

            $filter = Filter::create($filter);
            $ids[] = $filter->id;
        }

        return $ids;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        $this->model->update($attributes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function updateMany(array $data): self
    {
        foreach ($data as $filter) {
            Filter::where('id', $filter['id'])->update($filter);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return Filter::get(Filter::SELECT_ATTRIBUTES)->toArray();
    }

    /**
     * @inheritDoc
     */
    public function deleteAllFiltersByGroup(int $groupId): self
    {
        Filter::where('product_group_id', $groupId)->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        return Filter::find($this->model->id, Filter::SELECT_ATTRIBUTES)->toArray();
    }

    /**
     * @inheritDoc
     */
    public function totalByProductGroup(): array
    {
        return Filter::groupBy('product_group_id')
            ->select('product_group_id', DB::raw('count(*) as total'))
            ->get()
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getAll(int $productGroupId, string $langCode): array
    {
        $sql = 'SELECT DISTINCT feature_id, features.name, features.unit, `order`, sort_type
            FROM catalog_filters
            LEFT JOIN features ON features.id = feature_id
            WHERE product_group_id=? AND catalog_filters.deleted_at IS NULL
            ORDER BY `order` ASC';

        $features = DB::select($sql, [$productGroupId]);

        array_walk($features, function ($feature) use ($langCode) {
            $feature->name = LangPhrases::getPhraseInRequiredLang($langCode, $feature->name);
            $feature->unit = LangPhrases::getPhraseInRequiredLang($langCode, $feature->unit);
        });

        return $features;
    }

    /**
     * @inheritDoc
     */
    public function getFiltersValues(int $sectionId, array $filtersId, bool $hasPrice = true, int $priceTypeId = 1): array
    {
        $clientTypeId = (auth()->guard('client')->check()) ?
            auth()->guard('client')->user()->load(['clientType'])->clientType[0]->id : 1;

        // Не показывать юзерам товары НЕ ДЛЯ ВСЕХ
        $notForEveryone = "";
        if (!in_array($clientTypeId, config('storefront.not_for_everyone_client_types', []))) {
            $notForEveryone = " AND products.not_for_everyone!='Y'";
        }

        // Условие проверки цены
        $priceCondition = '';
        if ($hasPrice) {
            $priceCondition = 'EXISTS (SELECT * FROM product_prices
                    WHERE product_prices.product_id=feature_product.product_id
                    AND product_prices.value>=0
                    AND product_prices.deleted_at IS NULL
                    AND product_prices.price_type_id=' . $priceTypeId . ')
                AND ';
        }

        $sql = 'SELECT DISTINCT value, feature_id
            FROM feature_product
            WHERE
                ' . $priceCondition . 'feature_id IN (' . implode(',', $filtersId) . ')
                AND product_id IN (SELECT product_id FROM product_section
                    LEFT JOIN products ON products.id = product_id
                    WHERE section_id=? AND products.is_hidden=? ' . $notForEveryone . ')';

        $results = DB::select($sql, [$sectionId, 'N']);
        
        // Локализуем значения value и убираем дубликаты после локализации:
        // DISTINCT в SQL работает по сырому value (JSON/строка), а на экране — по локализованному значению.
        $locale = \Illuminate\Support\Facades\App::getLocale();
        $seen = [];
        $deduplicated = [];

        foreach ($results as $result) {
            $valueJson = $result->value;
            $decoded = json_decode($valueJson, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $result->value = $decoded[$locale] ?? $decoded['ru'] ?? $valueJson;
            }

            $key = $result->feature_id . '|' . $result->value;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $deduplicated[] = $result;
        }

        return $deduplicated;
    }

    /*
    private function isAssoc(array $arr): bool
    {
        if ([] === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    */
}
