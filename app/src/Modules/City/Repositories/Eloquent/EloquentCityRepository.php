<?php

declare(strict_types=1);

namespace Modules\City\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Modules\City\Entities\City;
use Modules\City\Repositories\CityRepository;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use Modules\Settings\Entities\Settings;
use Illuminate\Validation\ValidationException;

/**
 * Class EloquentCityRepository
 * @package Modules\City\Repositories\Eloquent
 */
class EloquentCityRepository implements CityRepository
{
    /**
     * @var Model An instance of the Eloquent Model
     */
    protected Model $model;

    /**
     * EloquentLanguageRepository constructor.
     * @param  Model  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function getAllByCountryCode(string $code, int $page): array
    {
        $totalPerPage = (int)Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'];

        $cities = $this->model
            ->skip($this->getSkipPos($page, $totalPerPage))
            ->take($totalPerPage)
            ->where('country_code', $code)
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), $cities);
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        Helper::havePhraseOnDefaultLang($attributes['name'], 'name');
        $this->checkCityNameUnique($attributes['name'], $attributes['country_code'], $this->model->id);

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
            throw new ModelNotFoundException('City not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        Helper::havePhraseOnDefaultLang($data['name'], 'name');
        $this->checkCityNameUnique($data['name'], $data['country_code']);

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
        $data = $this->model->only(City::SELECT_ATTRIBUTES);

        return Arr::onlyActiveLang(['name'], App::getLocale(), [$data])[0];
    }

    /**
     * @inheritDoc
     */
    public function all(?int $page, ?string $mode, ?string $search): array
    {
        $totalPerPage = (int)Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'];

        $all = $this->model
            ->when($mode !== null && $mode == 'clients', function ($query) {
                return $query
                    ->join('city_client', 'cities.id', '=', 'city_client.city_id');
            })
            ->when($page !== null, function ($query) use ($page, $totalPerPage) {
                return $query
                    ->skip($this->getSkipPos($page, $totalPerPage))
                    ->take($totalPerPage);
            })
            ->when($search !== null && $search !== '', function ($query) use ($search) {
                return $query
                    ->where('name', 'LIKE', '%' . $search . '%');
            })
            ->orderBy('name', 'asc')
            ->distinct()
            ->get(City::SELECT_ATTRIBUTES)->toArray();

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

    /**
     * @inheritDoc
     */
    public function total(string $countryCode): int
    {
        return $this->model
            ->when($countryCode != '', function ($query) use ($countryCode) {
                return $query->where('country_code', $countryCode);
            })
            ->get(City::SELECT_ATTRIBUTES)
            ->count();
    }

    /**
     * Returns skip position.
     */
    private function getSkipPos(int $page, int $totalPerPage): int
    {
        return (--$page * $totalPerPage);
    }

    /**
     * Checks the city name for uniqueness.
     * @throws ValidationException
     */
    private function checkCityNameUnique(string $jsonName, string $countryCode, int $exceptId = 0): void
    {
        $langCode = Settings::where('code', 'DEFAULT_LANGUAGE')->first()->toArray()['value'];
        $name = mb_strtolower(json_decode($jsonName, true)[$langCode] . $countryCode);

        $cities = City::where('id', '!=', $exceptId)->get(['name', 'country_code']);
        foreach ($cities as $city) {
            $cityName = json_decode($city['name'], true);

            if (isset($cityName[$langCode]) && mb_strtolower($cityName[$langCode] . $city['country_code']) == $name) {
                throw ValidationException::withMessages(
                    ['names.0.value' => 'Город с данным именем уже существует']
                );
            }
        }
    }
}
