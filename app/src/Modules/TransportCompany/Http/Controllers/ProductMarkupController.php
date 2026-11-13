<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\TransportCompany\Http\Requests\CreateOrUpdateProductMarkupRequest;
use Modules\TransportCompany\Repositories\ProductMarkupRepository;

/**
 * Class ProductMarkupController
 * @package Modules\TransportCompany\Http\Controllers
 */
class ProductMarkupController extends Controller
{
    /** @var ProductMarkupRepository An instance of the Product Markup Repository. */
    private ProductMarkupRepository $productMarkupRepo;

    /**
     * PickupPointController constructor.
     */
    public function __construct(ProductMarkupRepository $productMarkupRepo)
    {
        $this->productMarkupRepo = $productMarkupRepo;
    }

    /**
     * Returns all product markups.
     */
    public function index(): JsonResponse
    {
        return JsonResponse::successful($this->productMarkupRepo->all());
    }

    /**
     * Creates a new product markup.
     */
    public function store(CreateOrUpdateProductMarkupRequest $request): JsonResponse
    {
        $markupId = $this->productMarkupRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $markupId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns product markup data by id.
     */
    public function show(ProductMarkupRepository $productMarkupRepo): JsonResponse
    {
        return JsonResponse::successful($productMarkupRepo->data());
    }

    /**
     * Updates product markup data.
     */
    public function update(
        CreateOrUpdateProductMarkupRequest $request,
        ProductMarkupRepository $productMarkupRepo
    ): JsonResponse {
        $productMarkupRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the product markup.
     */
    public function destroy(ProductMarkupRepository $productMarkupRepo): JsonResponse
    {
        $productMarkupRepo->delete();

        return JsonResponse::successful();
    }
}
