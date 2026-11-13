<?php

declare(strict_types=1);

namespace Modules\Country\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Modules\Country\Repositories\CountryRepository;
use Modules\Country\Entities\Country;
use App\Helpers\Helper;

/**
 * Class EloquentCountryRepository
 * @package Modules\Country\Repositories\Eloquent
 */
class EloquentCountryRepository implements CountryRepository
{
    /** @var Model An instance of the Eloquent Model. */
    protected Model $model;

    /**
     * EloquentClientRepository constructor.
     *
     * @param  Model  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function getIdByCode(string $code): int
    {
        return ($this->model->where('code', $code)->first(['id'])->toArray())['id'];
    }

    /**
     * @inheritDoc
     */
    public function getLanguageByCode(string $code): string
    {
        return ($this->model->where('code', $code)->first(['language'])->toArray())['language'];
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        Helper::havePhraseOnDefaultLang($attributes['name'], 'name');

        if ($attributes['is_default'] === 'Y') {
            $this->resetDefault($this->model->id);
        }

        $this->model->update($attributes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setModel(int $id): self
    {
        try {
            $this->model = $this->model->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ModelNotFoundException('Country not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        Helper::havePhraseOnDefaultLang($data['name'], 'name');

        if ($data['is_default'] === 'Y') {
            $this->resetDefault();
        }

        $this->model = $this->model->create($data);

        return $this->model->id;
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
    public function data(): array
    {
        $only = [];
        foreach (Country::SELECT_ATTRIBUTES as $select) {
            $only[] = str_replace('countries.', '', $select);
        }

        $data = $this->model->only($only);

        return Arr::onlyActiveLang(['name'], App::getLocale(), [$data])[0];
    }

    /**
     * @inheritDoc
     */
    public function all(string $withcities = 'N', ?string $search): array
    {
        $all = $this->model
            ->when($withcities === 'Y', function ($query) {
                return $query
                    ->rightJoin('cities', 'cities.country_code', '=', 'countries.code')
                    ->distinct();
            })
            ->when($search !== null && $search !== '', function ($query) use ($search) {
                return $query
                    ->where('name', 'LIKE', '%' . $search . '%');
            })
            ->orderBy('name')
            ->get(Country::SELECT_ATTRIBUTES)->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), $all);
    }

    /**
     * Resets default city.
     */
    private function resetDefault(int $withoutId = 0): void
    {
        $this->model->where('id', '!=', $withoutId)
            ->where('is_default', 'Y')
            ->update(['is_default' => 'N']);
    }
}
