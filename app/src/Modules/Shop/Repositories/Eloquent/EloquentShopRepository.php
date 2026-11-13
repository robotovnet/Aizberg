<?php

declare(strict_types=1);

namespace Modules\Shop\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Modules\Shop\Repositories\ShopRepository;
use Modules\Shop\Entities\Shop;
use Modules\City\Entities\City;

/**
 * Class EloquentShopRepository
 * @package Modules\Shop\Repositories\Eloquent
 */
class EloquentShopRepository implements ShopRepository
{
    /**
     * @var Model An instance of the Eloquent Model
     */
    protected Model $model;

    /**
     * EloquentShopRepository constructor.
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
            throw new ModelNotFoundException('Shop not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
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
        $data = $this->model
            ->with(['city' => function ($query) {
                $query->select(City::SELECT_ATTRIBUTES);
            }])
            ->where('id', $this->model->id)
            ->first(Shop::SELECT_ATTRIBUTES)
            ->toArray();

        return Arr::onlyActiveLang('name', App::getLocale(), $data);
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $shops = $this->model
            ->with(['city' => function ($query) {
                $query->select(City::SELECT_ATTRIBUTES);
            }])
            ->orderBy('subdomain')
            ->get(Shop::SELECT_ATTRIBUTES)
            ->toArray();

        foreach ($shops as $key => $shop) {
            $shops[$key] = Arr::onlyActiveLang(['name'], App::getLocale(), $shop);
        }

        return $shops;
    }
}
