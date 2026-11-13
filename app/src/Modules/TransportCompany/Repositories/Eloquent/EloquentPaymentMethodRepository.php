<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Modules\ClientType\Entities\ClientType;
use Modules\TransportCompany\Entities\PaymentMethod;
use Modules\TransportCompany\Entities\TransportCompany;
use Modules\TransportCompany\Repositories\PaymentMethodRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\Helper;

/**
 * Class EloquentPaymentMethodRepository
 * @package Modules\TransportCompany\Repositories\Eloquent
 */
class EloquentPaymentMethodRepository implements PaymentMethodRepository
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
        Helper::havePhraseOnDefaultLang($attributes['additional_comment'], 'additional_comment');

        $paymentMethodId = (int)request()->segment(3);
        $this->attacheClientTypes($attributes['client_types'], $paymentMethodId);
        $this->attacheTransportCompanies($attributes['transport_companies'], $paymentMethodId);

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
            throw new ModelNotFoundException('Payment Method not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModelId(): int
    {
        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        Helper::havePhraseOnDefaultLang($data['name'], 'name');
        Helper::havePhraseOnDefaultLang($data['additional_comment'], 'additional_comment');

        $this->model = $this->model->create($data);

        $this->attacheClientTypes($data['client_types'], $this->model->id);
        $this->attacheTransportCompanies($data['transport_companies'], $this->model->id);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $all = $this->model->get(PaymentMethod::SELECT_ATTRIBUTES)->toArray();

        return Arr::onlyActiveLang(['name', 'additional_comment'], App::getLocale(), $all);
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
        $data = $this->model->
            with(['clientTypes' => function ($query) {
                $query->select(ClientType::SELECT_ATTRIBUTES);
            }])
            ->with(['transportCompanies' => function ($query) {
                $query->select(TransportCompany::SELECT_ATTRIBUTES);
            }])
            ->find($this->model->id, PaymentMethod::SELECT_ATTRIBUTES)
            ->toArray();

        $data['transport_companies'] = Arr::onlyActiveLang(
            ['name', 'additional_comment'],
            App::getLocale(),
            $data['transport_companies']
        );

        $data['client_types'] = Arr::onlyActiveLang(
            ['name'],
            App::getLocale(),
            $data['client_types']
        );

        return Arr::onlyActiveLang(['name', 'additional_comment'], App::getLocale(), [$data]);
    }

    /**
     * @inheritDoc
     */
    public function order(array $orders): self
    {
        foreach ($orders as $order) {
            PaymentMethod::where('id', $order['pmId'])->update(['order' => $order['order']]);
        }

        return $this;
    }

    /**
     * Attaches transport companies.
     */
    private function attacheTransportCompanies(array $transportCompanies, int $paymentMethodId): void
    {
        $pm = PaymentMethod::find($paymentMethodId);
        $pm->transportCompanies()->detach();

        foreach ($transportCompanies as $company) {
            $addParams = [
                'is_support_courier_delivery' => $company['is_support_courier_delivery'],
                'is_support_postamats'        => $company['is_support_postamats'],
                'is_support_pickup_points'    => $company['is_support_pickup_points'],
            ];

            $pm->transportCompanies()->attach($company['transport_company_id'], $addParams);
        }
    }

    /**
     * Attaches client types.
     */
    private function attacheClientTypes(array $clientTypes, int $paymentMethodId): void
    {
        $pm = PaymentMethod::find($paymentMethodId);
        $pm->clientTypes()->detach();

        foreach ($clientTypes as $type) {
            $pm->clientTypes()->attach($type);
        }
    }
}
