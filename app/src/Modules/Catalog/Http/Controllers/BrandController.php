<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Catalog\Http\Requests\CreateOrUpdateBrandRequest;
use Modules\Catalog\Repositories\BrandRepository;
use Illuminate\Http\Request;

/**
 * Class BrandController
 * @package Modules\Catalog\Http\Controllers
 */
class BrandController extends Controller
{
    /** @var BrandRepository An instance of the Brand Repository. */
    private BrandRepository $brandRepo;

    /**
     * FeatureController constructor.
     */
    public function __construct(BrandRepository $brandRepo)
    {
        $this->brandRepo = $brandRepo;
    }

    /**
     * Returns all brands.
     */
    public function index(Request $request): JsonResponse
    {
        $isHidden = (bool)$request->input('is_hidden', false);
        $search   = $request->input('search', null);

        return JsonResponse::successful($this->brandRepo->all($isHidden, $search));
    }

    /**
     * Creates a new brand.
     */
    public function store(CreateOrUpdateBrandRequest $request): JsonResponse
    {
        $request->merge([
            'name' => Str::getNamesJson($request->names),
        ]);

        $brandId = $this->brandRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $brandId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns brand data by id.
     */
    public function show(BrandRepository $brandRepo): JsonResponse
    {
        return JsonResponse::successful($brandRepo->data());
    }

    /**
     * Updates brand data.
     */
    public function update(
        CreateOrUpdateBrandRequest $request,
        BrandRepository $brandRepo
    ): JsonResponse {
        $request->merge([
            'name' => Str::getNamesJson($request->names),
        ]);

        $brandRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the brand.
     */
    public function destroy(BrandRepository $brandRepo): JsonResponse
    {
        $brandRepo->delete();

        return JsonResponse::successful();
    }
}
