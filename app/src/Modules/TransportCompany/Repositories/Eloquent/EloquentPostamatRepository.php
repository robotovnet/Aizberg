<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\City\Entities\City;
use Modules\TransportCompany\Entities\Postamat;
use Modules\TransportCompany\Entities\TransportCompany;
use Modules\TransportCompany\Entities\Warehouse;
use Modules\TransportCompany\Repositories\PostamatRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class EloquentPostamatRepository
 * @package Modules\TransportCompany\Repositories\Eloquent
 */
class EloquentPostamatRepository implements PostamatRepository
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

        $postamatId = (int)request()->segment(3);
        $this->attacheWarehouses($attributes['warehouses'], $postamatId);
        $this->attacheTransportCompanies($attributes['transport_companies'], $postamatId);

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
            throw new ModelNotFoundException('Postamat not found.');
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
    public function all(int $page, int $totalPerPage, string $addingMethod, int $city, int $tcId): array
    {
        $all = Postamat::skip($this->getSkipPos($page, $totalPerPage))
            ->take($totalPerPage)
            ->when($addingMethod != 'all', function ($query) use ($addingMethod) {
                return $query->where('adding_method', $addingMethod);
            })
            ->when($city > 0, function ($query) use ($city) {
                return $query->where('city_id', $city);
            })
            ->with(['transportCompanies' => function ($query) {
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
            ->get(Postamat::SELECT_ATTRIBUTES)
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
            Postamat::with(['transportCompanies' => function ($query) {
                $query->select(TransportCompany::SELECT_ATTRIBUTES);
            }])
                ->with(['warehouses' => function ($query) {
                    $query->select(Warehouse::SELECT_ATTRIBUTES);
                }])
                ->find($this->model->id, Postamat::SELECT_ATTRIBUTES)
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
    public function total(string $addingMethod, int $city, int $tcId): int
    {
        return Postamat::when($addingMethod != 'all', function ($query) use ($addingMethod) {
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
            from `postamats`
            where `postamats`.`deleted_at` is null
             and exists (select * from
            `transport_companies`
            inner join `postamat_transport_company` on `transport_companies`.`id` =
            `postamat_transport_company`.`transport_company_id`
            where `postamats`.`id` = `postamat_transport_company`.`postamat_id`
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
    private function attacheWarehouses(array $warehouses, int $postamatId): void
    {
        $postamat = Postamat::find($postamatId);
        $postamat->warehouses()->detach();

        foreach ($warehouses as $warehouse) {
            $postamat->warehouses()->attach($warehouse);
        }
    }

    /**
     * Attaches transport companies.
     */
    private function attacheTransportCompanies(array $transportCompanies, int $postamatId): void
    {
        $postamat = Postamat::find($postamatId);
        $postamat->transportCompanies()->detach();

        foreach ($transportCompanies as $company) {
            $postamat->transportCompanies()->attach($company);
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
