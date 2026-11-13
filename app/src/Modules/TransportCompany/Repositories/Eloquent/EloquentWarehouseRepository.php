<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Modules\TransportCompany\Entities\Warehouse;
use Modules\TransportCompany\Repositories\WarehouseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use App\Helpers\LangPhrases;

/**
 * Class EloquentWarehouseRepository
 * @package Modules\TransportCompany\Repositories\Eloquent
 */
class EloquentWarehouseRepository implements WarehouseRepository
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
        Helper::havePhraseOnDefaultLang($attributes['name'], 'name');

        $warehouseId = (int)request()->segment(3);
        $this->saveWarehouseTransportCompanies($attributes['transport_companies'], $warehouseId);

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
            throw new ModelNotFoundException('Warehouse not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        Helper::havePhraseOnDefaultLang($data['name'], 'name');

        $this->model = $this->model->create($data);
        $this->saveWarehouseTransportCompanies($data['transport_companies'], $this->model->id);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function all($active = false): array
    {
        $activeValues = ($active) ? ["Y"] : ["Y", "N"];

        $all = $this->model->get(Warehouse::SELECT_ATTRIBUTES)
            ->whereIn('is_active', $activeValues)
            ->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), $all);
    }

    /**
     * @inheritDoc
     */
    public function allWithTransportCompanies(): array
    {
        $all = $this->model->with()->get(Warehouse::SELECT_ATTRIBUTES)->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), $all);
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        // TODO еще удалять записи из таблиц
        // postamat_transport_company
        // postamat_warehouse
        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getWarehousesInCity(int $cityId, string $langCode): array
    {
        $sql = 'SELECT t.name, t.id
                FROM
                (
                    (SELECT warehouses.name, warehouses.id as id, city_id FROM warehouses
                    LEFT OUTER JOIN courier_city_warehouse ON warehouses.id = warehouse_id
                    LEFT OUTER JOIN courier_cities ON courier_cities.id = courier_city_id
                    WHERE warehouses.deleted_at IS NULL AND is_active="Y")

                    UNION

                    (SELECT warehouses.name, warehouses.id as id, city_id FROM warehouses
                    LEFT OUTER JOIN pickup_point_warehouse ON warehouses.id = warehouse_id
                    LEFT OUTER JOIN pickup_points ON pickup_points.id = pickup_point_id
                    WHERE warehouses.deleted_at IS NULL AND is_active="Y")

                    UNION

                    (SELECT warehouses.name, warehouses.id as id, city_id FROM warehouses
                    LEFT OUTER JOIN postamat_warehouse ON warehouses.id = warehouse_id
                    LEFT OUTER JOIN postamats ON postamats.id = postamat_id
                    WHERE warehouses.deleted_at IS NULL AND is_active="Y")
                ) t
                WHERE t.city_id=?';

        $warehouses = DB::select($sql, [$cityId]);

        array_walk($warehouses, function ($warehouse) use ($langCode) {
            $warehouse->name = LangPhrases::getPhraseInRequiredLang($langCode, $warehouse->name);
        });

        return $warehouses;
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $warehouse = Warehouse::with(['transportCompanies' => function ($query) {
            $query->select(['transport_companies.id']);
        }])->where('id', $this->model->id)->get(Warehouse::SELECT_ATTRIBUTES)->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), $warehouse)[0];
    }

    /**
     * Keeps the transport companies working with the warehouse.
     */
    private function saveWarehouseTransportCompanies(array $transportCompanies, int $warehouseId): void
    {
        $warehouse = Warehouse::find($warehouseId);
        $warehouse->transportCompanies()->detach();

        foreach ($transportCompanies as $company) {
            $addParams = [
                'for_all_courier_cities' => $company['for_all_courier_cities'],
                'for_all_postamats'      => $company['for_all_postamats'],
                'for_all_pickup_points'  => $company['for_all_pickup_points'],
            ];

            $warehouse->transportCompanies()->attach($company['transport_company_id'], $addParams);
        }
    }
}
