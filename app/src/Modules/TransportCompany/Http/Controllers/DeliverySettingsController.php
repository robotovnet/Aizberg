<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\TransportCompany\Http\Requests\CreateOrUpdateDeliverySettingsRequest;
use Modules\TransportCompany\Repositories\DeliverySettingsRepository;

/**
 * Class DeliverySettingsController
 * @package Modules\TransportCompany\Http\Controllers
 */
class DeliverySettingsController extends Controller
{
    /** @var DeliverySettingsRepository An instance of the Delivery Settings Repository. */
    private DeliverySettingsRepository $deliverySettingsRepo;

    /**
     * DeliverySettingsController constructor.
     */
    public function __construct(DeliverySettingsRepository $deliverySettingsRepo)
    {
        $this->deliverySettingsRepo = $deliverySettingsRepo;
    }

    /**
     * Creates a new delivery settings.
     */
    public function store(CreateOrUpdateDeliverySettingsRequest $request): JsonResponse
    {
        $deliverySettingsId = $this->deliverySettingsRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $deliverySettingsId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns delivery settings data by id.
     */
    public function show(DeliverySettingsRepository $deliverySettingsRepo): JsonResponse
    {
        return JsonResponse::successful($deliverySettingsRepo->data());
    }

    /**
     * Updates delivery settings data.
     */
    public function update(
        CreateOrUpdateDeliverySettingsRequest $request,
        DeliverySettingsRepository $deliverySettingsRepo
    ): JsonResponse {
        $deliverySettingsRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the delivery settings.
     */
    public function destroy(DeliverySettingsRepository $deliverySettingsRepo): JsonResponse
    {
        $deliverySettingsRepo->delete();

        return JsonResponse::successful();
    }
}
