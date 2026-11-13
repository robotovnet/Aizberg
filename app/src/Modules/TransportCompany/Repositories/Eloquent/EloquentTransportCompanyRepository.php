<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Modules\TransportCompany\Entities\DeliverySettings;
use Modules\TransportCompany\Repositories\TransportCompanyRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\TransportCompany\Entities\TransportCompany;
use App\Helpers\Helper;

/**
 * Class EloquentTransportCompanyRepository
 * @package Modules\TransportCompany\Repositories\Eloquent
 */
class EloquentTransportCompanyRepository implements TransportCompanyRepository
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
    public function update(array $attributes): self
    {
        if (isset($attributes['name'])) {
            Helper::havePhraseOnDefaultLang($attributes['name'], 'name');
        }

        if (isset($attributes['additional_comment'])) {
            Helper::havePhraseOnDefaultLang($attributes['additional_comment'], 'additional_comment');
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
            throw new ModelNotFoundException('Transport company not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): self
    {
        Helper::havePhraseOnDefaultLang($data['name'], 'name');
        Helper::havePhraseOnDefaultLang($data['additional_comment'], 'additional_comment');

        $data['order'] = $this->getMaxOrder() + 1;
        $this->model   = $this->model->create($data);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaxOrder(): int
    {
        $max = TransportCompany::max('order');

        return (is_null($max)) ? 0 : $max;
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
        $data = TransportCompany::with(['deliverySettings' => function ($query) {
            $query->select(DeliverySettings::SELECT_ATTRIBUTES);
        }])->where('id', $this->model->id)->get(TransportCompany::SELECT_ATTRIBUTES)->toArray();

        $row = Arr::onlyActiveLang(['name', 'additional_comment'], App::getLocale(), $data)[0];
        $row['shipping_cost_ids'] = $this->model->shippingCosts()
            ->orderBy('id')
            ->pluck('id')
            ->values()
            ->all();

        return $row;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $all = $this->model->orderBy('order', 'asc')->get(TransportCompany::SELECT_ATTRIBUTES)->toArray();

        return Arr::onlyActiveLang(['name', 'additional_comment'], App::getLocale(), $all);
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return (int)$this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function order(array $orders): self
    {
        foreach ($orders as $order) {
            TransportCompany::where('id', $order['tcId'])->update(['order' => $order['order']]);
        }

        return $this;
    }
}
