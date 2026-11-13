<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories\Eloquent;

use Modules\TransportCompany\Entities\DeliverySettings;
use Modules\TransportCompany\Repositories\DeliverySettingsRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

/**
 * Class EloquentDeliverySettingsRepository
 * @package Modules\TransportCompany\Repositories\Eloquent
 */
class EloquentDeliverySettingsRepository implements DeliverySettingsRepository
{
    /** @var Model An instance of the Eloquent Model. */
    protected Model $model;

    /**
     * EloquenCurrencyRepository constructor.
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
            throw new ModelNotFoundException('Delivery Settings not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        $this->model = $this->model->create($data);

        if (isset($data['copy_settings']) && count($data['copy_settings']) > 0) {
            $this->copySettings($data, Arr::only($data, ['copy_settings']));
        }

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        $this->model->update($attributes);

        if (isset($attributes['copy_settings']) && count($attributes['copy_settings']) > 0) {
            $this->copySettings($attributes, Arr::only($attributes, ['copy_settings']));
        }

        return $this;
    }

    /**
     * Copies similar settings for other delivery objects.
     */
    private function copySettings($data, $whatCopy)
    {
        $supportTypes = [
            'postamat',
            'courier_city',
            'pickup_point',
        ];

        Arr::forget($data, 'copy_settings');

        foreach ($whatCopy['copy_settings'] as $copiedSetting) {
            if (!in_array($copiedSetting, $supportTypes)) {
                continue;
            }

            DeliverySettings::where('transport_company_id', $data['transport_company_id'])
                ->where('delivery_point_id', $data['delivery_point_id'])
                ->where('delivery_type', $copiedSetting)
                ->delete();

            $data['delivery_type'] = $copiedSetting;

            DeliverySettings::create($data);
        }
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
        return $this->model->only(DeliverySettings::SELECT_ATTRIBUTES);
    }
}
