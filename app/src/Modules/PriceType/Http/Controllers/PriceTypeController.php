<?php

declare(strict_types=1);

namespace Modules\PriceType\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\PriceType\Repositories\PriceTypeRepository;
use Modules\PriceType\Http\Requests\CreateOrUpdatePriceTypeRequest;

/**
 * Class PriceTypeController
 * @package Modules\PriceType\Http\Controllers
 */
class PriceTypeController extends Controller
{
    /** @var PriceTypeRepository An instance of the Price Type Repository */
    private PriceTypeRepository $priceTypeRepo;

    /**
     * PriceTypeController constructor.
     */
    public function __construct(PriceTypeRepository $priceTypeRepo)
    {
        $this->priceTypeRepo = $priceTypeRepo;
    }

    /**
     * Creates a new price type.
     */
    public function store(CreateOrUpdatePriceTypeRequest $request): JsonResponse
    {
        $request->merge(['name' => Str::getNamesJson($request->names)]);
        $priceTypeId = $this->priceTypeRepo->create($request->all());

        return JsonResponse::successful([
            'id'      => $priceTypeId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns all price types.
     */
    public function index(): JsonResponse
    {
        return JsonResponse::successful($this->priceTypeRepo->all());
    }

    /**
     * Returns price type data by id.
     */
    public function show(PriceTypeRepository $priceTypeRepo): JsonResponse
    {
        return JsonResponse::successful($priceTypeRepo->data());
    }

    public function update(CreateOrUpdatePriceTypeRequest $request, PriceTypeRepository $priceTypeRepo): JsonResponse
    {
        $request->merge(['name' => Str::getNamesJson($request->names)]);
        $priceTypeRepo
            ->update($request->all());

        return JsonResponse::successful();
    }

    public function destroy(PriceTypeRepository $priceTypeRepo): JsonResponse
    {
        $priceTypeRepo->delete();

        return JsonResponse::successful();
    }
}
