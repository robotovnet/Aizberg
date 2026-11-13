<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Modules\TransportCompany\Entities\CourierCity;
use Modules\TransportCompany\Entities\TransportCompany;
use Modules\TransportCompany\Entities\Warehouse;
use Modules\TransportCompany\Repositories\CourierCityRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class EloquentCourierCityRepository
 * @package Modules\TransportCompany\Repositories\Eloquent
 */
class EloquentCourierCityRepository implements CourierCityRepository
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
    public function update(array $attributes): self
    {
        $this->checkUniqueness($attributes['country_id'], $attributes['city_id'], $this->model->id);

        $courierCityId = (int)request()->segment(3);
        $this->attacheWarehouses($attributes['warehouses'], $courierCityId);
        $this->attacheTransportCompanies($attributes['transport_companies'], $courierCityId);

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
            throw new ModelNotFoundException('Courier city not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        $this->checkUniqueness($data['country_id'], $data['city_id']);

        $this->model = $this->model->create($data);

        $this->attacheWarehouses($data['warehouses'], $this->model->id);
        $this->attacheTransportCompanies($data['transport_companies'], $this->model->id);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function all(int $page, int $totalPerPage, string $addingMethod, int $tcId, ?int $cityId = null, ?string $search = null): array
    {
        $all = CourierCity::skip($this->getSkipPos($page, $totalPerPage))
            ->take($totalPerPage)
            ->when($addingMethod != 'all', function ($query) use ($addingMethod) {
                return $query->where('adding_method', $addingMethod);
            })
            ->when($cityId != null, function ($query) use ($cityId) {
                return $query->where('city_id', $cityId);
            })
            ->with(['transportCompanies' => function ($query) use ($tcId) {
                $query->select(TransportCompany::SELECT_ATTRIBUTES);
            }])
            ->with(['warehouses' => function ($query) {
                $query->select(Warehouse::SELECT_ATTRIBUTES);
            }])
            ->whereHas('transportCompanies', function ($query) use ($tcId) {
                if ($tcId != 0) {
                    return $query->where('transport_company_id', '=', $tcId);
                }
            })
            ->when($search !== null && $search !== '', function ($query) use ($search) {
                return $query
                    ->where('name', 'LIKE', '%' . $search . '%');
            })
            ->join('cities', 'courier_cities.city_id', '=', 'cities.id')
            ->orderBy('name', 'asc')
            ->get(CourierCity::SELECT_ATTRIBUTES)
            ->toArray();

        foreach ($all as &$city) {
            $city['transport_companies'] = Arr::onlyActiveLang(
                ['name', 'additional_comment'],
                App::getLocale(),
                $city['transport_companies']
            );

            $city['warehouses'] = Arr::onlyActiveLang(
                ['name'],
                App::getLocale(),
                $city['warehouses']
            );
        }

        return $all;
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
        $data =
            CourierCity::with(['transportCompanies' => function ($query) {
                $query->select(TransportCompany::SELECT_ATTRIBUTES);
            }])
                ->with(['warehouses' => function ($query) {
                    $query->select(Warehouse::SELECT_ATTRIBUTES);
                }])
                ->find($this->model->id, CourierCity::SELECT_ATTRIBUTES)
                ->toArray();

        $data['transport_companies'] = Arr::onlyActiveLang(
            ['name', 'additional_comment'],
            App::getLocale(),
            $data['transport_companies']
        );

        $data['warehouses'] = Arr::onlyActiveLang(
            ['name'],
            App::getLocale(),
            $data['warehouses']
        );

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function total(string $addingMethod, int $tcId): int
    {
        return CourierCity::when($addingMethod != 'all', function ($query) use ($addingMethod) {
            return $query->where('adding_method', $addingMethod);
        })
        ->whereHas('transportCompanies', function ($query) use ($tcId) {
            if ($tcId != 0) {
                return $query->where('transport_company_id', '=', $tcId);
            }
        })
        ->get(CourierCity::SELECT_ATTRIBUTES)
        ->count();
    }

    /**
     * Attaches warehouses.
     */
    private function attacheWarehouses(array $warehouses, int $courierCityId): void
    {
        $courierCity = CourierCity::find($courierCityId);
        $courierCity->warehouses()->detach();

        foreach ($warehouses as $warehouse) {
            $courierCity->warehouses()->attach($warehouse);
        }
    }

    /**
     * Attaches transport companies.
     */
    private function attacheTransportCompanies(array $transportCompanies, int $courierCityId): void
    {
        $courierCity = CourierCity::find($courierCityId);
        $courierCity->transportCompanies()->detach();

        foreach ($transportCompanies as $company) {
            $courierCity->transportCompanies()->attach($company);
        }
    }

    /**
     * Returns skip position.
     */
    private function getSkipPos(int $page, int $totalPerPage): int
    {
        return (--$page * $totalPerPage);
    }

    private function checkUniqueness(int $countryId, int $cityId, int $exceptId = 0): void
    {
        $total = CourierCity::where('country_id', $countryId)
            ->where('city_id', $cityId)
            ->where('id', '!=', $exceptId)
            ->count();

        if ($total != 0) {
            throw ValidationException::withMessages(
                ['city_id' => 'Такой город доставки был уже ранее добавлен в систему']
            );
        }
    }
}
