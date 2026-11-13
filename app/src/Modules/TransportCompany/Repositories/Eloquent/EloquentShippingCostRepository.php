<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories\Eloquent;

use Modules\TransportCompany\Entities\ShippingCost;
use Modules\TransportCompany\Repositories\ShippingCostRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class EloquentShippingCostRepository
 * @package Modules\TransportCompany\Repositories\Eloquent
 */
class EloquentShippingCostRepository implements ShippingCostRepository
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
            throw new ModelNotFoundException('Shipping Cost not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        $data['rates'] = json_encode($data['rates']);
        $this->model = $this->model->create(array_merge($data, $this->shippingMethods($data['shipping_methods'])));

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        $attributes['rates'] = json_encode($attributes['rates']);
        $this->model->update(array_merge($attributes, $this->shippingMethods($attributes['shipping_methods'])));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return ShippingCost::get(ShippingCost::SELECT_ATTRIBUTES)->toArray();
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        return ShippingCost::find($this->model->id, ShippingCost::SELECT_ATTRIBUTES)->toArray();
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
     * Returns activated shipping methods.
     */
    private function shippingMethods(array $methods): array
    {
        $data = [
            'is_support_courier_delivery' => 'N',
            'is_support_postamats'        => 'N',
            'is_support_pickup_points'    => 'N',
        ];

        foreach ($methods as $method) {
            $data['is_support_' . $method] = 'Y';
        }

        return $data;
    }
}

