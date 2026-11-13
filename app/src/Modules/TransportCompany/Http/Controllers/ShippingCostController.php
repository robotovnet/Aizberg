<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\TransportCompany\Http\Requests\CreateOrUpdateShippingCostRequest;
use Modules\TransportCompany\Repositories\ShippingCostRepository;

/**
 * Class ShippingCostController
 * @package Modules\TransportCompany\Http\Controllers
 */
class ShippingCostController extends Controller
{
    /** @var ShippingCostRepository An instance of the Shipping Cost Repository. */
    private ShippingCostRepository $shippingCostRepo;

    /**
     * ShippingCostController constructor.
     */
    public function __construct(ShippingCostRepository $shippingCostRepo)
    {
        $this->shippingCostRepo = $shippingCostRepo;
    }

    /**
     * Returns all shipping rates.
     */
    public function index(): JsonResponse
    {
        return JsonResponse::successful($this->shippingCostRepo->all());
    }

    /**
     * Creates a new shipping cost.
     */
    public function store(CreateOrUpdateShippingCostRequest $request): JsonResponse
    {
        $shippingCostId = $this->shippingCostRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $shippingCostId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns shipping cost data by id.
     */
    public function show(ShippingCostRepository $shippingCostRepo): JsonResponse
    {
        return JsonResponse::successful($shippingCostRepo->data());
    }

    /**
     * Updates shipping cost data.
     */
    public function update(
        CreateOrUpdateShippingCostRequest $request,
        ShippingCostRepository $shippingCostRepo
    ): JsonResponse {
        $shippingCostRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the shipping cost.
     */
    public function destroy(ShippingCostRepository $shippingCostRepo): JsonResponse
    {
        $shippingCostRepo->delete();

        return JsonResponse::successful();
    }
}
