<?php

declare(strict_types=1);

namespace Modules\Ad\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Modules\Ad\Entities\Yml;
use Modules\Ad\Repositories\YmlRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\TransportCompany\Entities\Warehouse;

/**
 * Class EloquentYmlRepository
 * @package Modules\Ad\Repositories\Eloquent
 */
class EloquentYmlRepository implements YmlRepository
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
            throw new ModelNotFoundException('Yml not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        $this->model = $this->model->create($data);

        $this->model->warehouses()->attach($data['warehouses']);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        $this->model->update($attributes);

        $this->model->warehouses()->detach();
        $this->model->warehouses()->attach($attributes['warehouses']);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        $this->model->warehouses()->detach();

        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $all = Yml::with(['warehouses' => function ($query) {
                $query->select(Warehouse::SELECT_ATTRIBUTES);
        }])
        ->get(Yml::SELECT_ATTRIBUTES)->toArray();

        foreach ($all as &$yml) {
            $yml['warehouses'] = Arr::onlyActiveLang(
                ['name'],
                App::getLocale(),
                $yml['warehouses']
            );

            $yml['total_offers'] = 12000; // TODO determine the number of sentences in the yml file
        }

        return $all;
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $data = Yml::with(['warehouses' => function ($query) {
            $query->select(Warehouse::SELECT_ATTRIBUTES);
        }])
        ->find($this->model->id, Yml::SELECT_ATTRIBUTES)->toArray();

        $data['warehouses'] = Arr::onlyActiveLang(
            ['name'],
            App::getLocale(),
            $data['warehouses']
        );

        return $data;
    }
}

