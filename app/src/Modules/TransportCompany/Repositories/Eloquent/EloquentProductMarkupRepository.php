<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories\Eloquent;

use Modules\TransportCompany\Entities\ProductMarkup;
use Modules\TransportCompany\Repositories\ProductMarkupRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class EloquentProductMarkupRepository
 * @package Modules\TransportCompany\Repositories\Eloquent
 */
class EloquentProductMarkupRepository implements ProductMarkupRepository
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
            throw new ModelNotFoundException('Product Markup not found.');
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
    public function all(): array
    {
        return ProductMarkup::get(ProductMarkup::SELECT_ATTRIBUTES)->toArray();
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
        return ProductMarkup::find($this->model->id, ProductMarkup::SELECT_ATTRIBUTES)->toArray();
    }
}
