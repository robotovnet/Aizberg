<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\City\Entities\City;
use Modules\TransportCompany\Entities\DeliverySettings;
use Modules\TransportCompany\Entities\PickupPoint;
use Modules\TransportCompany\Entities\TransportCompany;
use Modules\TransportCompany\Entities\Warehouse;
use Modules\TransportCompany\Repositories\PickupPointRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class EloquentPickupPointRepository
 * @package Modules\TransportCompany\Repositories\Eloquent
 */
class EloquentPickupPointRepository implements PickupPointRepository
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
        $this->checkTime($attributes['break_work_time']);
        $this->checkTime($attributes['work_time']);

        $pickupPointId = (int)request()->segment(3);
        $this->attacheWarehouses($attributes['warehouses'], $pickupPointId);
        $this->attacheTransportCompanies($attributes['transport_companies'], $pickupPointId);

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
            throw new ModelNotFoundException('Pickup Point not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        $this->checkTime($data['break_work_time']);
        $this->checkTime($data['work_time']);

        $this->model = $this->model->create($data);

        $this->attacheWarehouses($data['warehouses'], $this->model->id);
        $this->attacheTransportCompanies($data['transport_companies'], $this->model->id);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function all(int $page, int $totalPerPage, string $addingMethod, int $city, int $tcId, array $citiesOrder = []): array
    {
        $all = PickupPoint::skip($this->getSkipPos($page, $totalPerPage))
            ->take($totalPerPage)
            ->when($addingMethod != 'all', function ($query) use ($addingMethod) {
                return $query->where('adding_method', $addingMethod);
            })
            ->when($city > 0, function ($query) use ($city) {
                return $query->where('city_id', $city);
            })
            ->when(count($citiesOrder) > 0, function ($query) use ($citiesOrder) {
                return $query->orderByRaw("FIELD(city_id, " . implode(",", $citiesOrder) . ")");
            })
            ->with(['transportCompanies' => function ($query) {
                $query->select(TransportCompany::SELECT_ATTRIBUTES);
            }])
            ->with(['warehouses' => function ($query) {
                $query->select(Warehouse::SELECT_ATTRIBUTES);
            }])
            ->when($tcId != 0, function ($query) use ($tcId) {
                $query->whereHas('transportCompanies', function ($query) use ($tcId) {
                        return $query->where('transport_company_id', '=', $tcId);
                });
            })
            ->get(PickupPoint::SELECT_ATTRIBUTES)
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
            PickupPoint::with(['transportCompanies' => function ($query) {
                $query->select(TransportCompany::SELECT_ATTRIBUTES);
            }])
            ->with(['warehouses' => function ($query) {
                $query->select(Warehouse::SELECT_ATTRIBUTES);
            }])
            ->find($this->model->id, PickupPoint::SELECT_ATTRIBUTES)
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

        //$data['phone'] = preg_replace("/[^0-9]/", '', $data['phone']);
        $data['phone'] = preg_replace("/^8/", "7", $data['phone']);

        $data['delivery_settings'] = DeliverySettings::query()
            ->where('delivery_type', 'pickup_point')
            ->where('delivery_point_id', $this->model->id)
            ->get(DeliverySettings::SELECT_ATTRIBUTES)
            ->toArray();

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function total(string $addingMethod, int $city, int $tcId): int
    {
        return PickupPoint::when($addingMethod != 'all', function ($query) use ($addingMethod) {
                return $query->where('adding_method', $addingMethod);
        })
        ->when($city > 0, function ($query) use ($city) {
            return $query->where('city_id', $city);
        })
        ->when($tcId > 0, function ($query) use ($tcId) {
                $query->whereHas('transportCompanies', function ($query) use ($tcId) {
                        return $query->where('transport_company_id', '=', $tcId);
                });
        })
        ->count();
    }

    /**
     * @inheritDoc
     */
    public function cities(): array
    {
        $citiesIds = DB::select("select `city_id`
            from `pickup_points`
            where `pickup_points`.`deleted_at` is null
             and exists (select * from `transport_companies`
            inner join `pickup_point_transport_company` on `transport_companies`.`id` =
            `pickup_point_transport_company`.`transport_company_id`
            where `pickup_points`.`id` = `pickup_point_transport_company`.`pickup_point_id`
            and `transport_companies`.`deleted_at` is null) ");

        $ids = [];
        foreach ($citiesIds as $item) {
            $ids[] = $item->city_id;
        }

        $citiesData = City::whereIn('id', $ids)
            ->orderBy('name')
            ->get(City::SELECT_ATTRIBUTES)
            ->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), $citiesData);
    }

    /**
     * Attaches warehouses.
     */
    private function attacheWarehouses(array $warehouses, int $pickupPointId): void
    {
        $pickupPoint = PickupPoint::find($pickupPointId);
        $pickupPoint->warehouses()->detach();

        foreach ($warehouses as $warehouse) {
            $pickupPoint->warehouses()->attach($warehouse);
        }
    }

    /**
     * Attaches transport companies.
     */
    private function attacheTransportCompanies(array $transportCompanies, int $pickupPointId): void
    {
        $pickupPoint = PickupPoint::find($pickupPointId);
        $pickupPoint->transportCompanies()->detach();

        foreach ($transportCompanies as $company) {
            $pickupPoint->transportCompanies()->attach($company);
        }
    }

    /**
     * Returns skip position.
     */
    private function getSkipPos(int $page, int $totalPerPage): int
    {
        return (--$page * $totalPerPage);
    }

    /**
     * Checks times into json.
     * @throws ValidationException
     */
    private function checkTime(string $jsonData): void
    {
        $data = json_decode($jsonData, true);

        if (is_null($data)) {
            throw ValidationException::withMessages(
                ['break_work_time' => 'Некорректно заполнен график работы']
            );
        }

        $rules = [
            'Mn' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
            'Tu' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
            'Wd' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
            'Th' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
            'Fr' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
            'Sa' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
            'Sn' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
        ];

        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            throw ValidationException::withMessages(
                ['break_work_time' => 'Некорректно заполнен график работы']
            );
        }
    }
}
