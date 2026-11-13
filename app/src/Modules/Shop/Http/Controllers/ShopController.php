<?php

declare(strict_types=1);

namespace Modules\Shop\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Shop\Http\Requests\CreateOrUpdateShopRequest;
use Modules\Shop\Repositories\ShopRepository;

/**
 * Class ShopController
 * @package Modules\Shop\Http\Controllers
 */
class ShopController extends Controller
{
    /** @var ShopRepository An instance of the Shop Repository. */
    private ShopRepository $shopRepo;

    /**
     * ShopController constructor.
     */
    public function __construct(ShopRepository $shopRepo)
    {
        $this->shopRepo = $shopRepo;
    }

    /**
     * Creates a new shop.
     */
    public function store(CreateOrUpdateShopRequest $request): JsonResponse
    {
        $shopId = $this->shopRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $shopId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns all shops.
     */
    public function index(): JsonResponse
    {
        return JsonResponse::successful($this->shopRepo->all());
    }

    /**
     * Returns shop data by id.
     */
    public function show(ShopRepository $shopRepo): JsonResponse
    {
        return JsonResponse::successful($shopRepo->data());
    }

    /**
     * Updates shop data.
     */
    public function update(CreateOrUpdateShopRequest $request, ShopRepository $shopRepo): JsonResponse
    {
        $shopRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the shop.
     */
    public function destroy(ShopRepository $shopRepo): JsonResponse
    {
        $shopRepo->delete();

        return JsonResponse::successful();
    }
}
