<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\TransportCompany\Http\Requests\CreateOrUpdateWarehouseRequest;
use Modules\TransportCompany\Repositories\WarehouseRepository;

/**
 * Class WarehouseController
 * @package Modules\TransportCompany\Http\Controllers
 */
class WarehouseController extends Controller
{
    /** @var WarehouseRepository An instance of the Warehouse Repository. */
    private WarehouseRepository $warehouseRepo;

    /**
     * PostamatController constructor.
     */
    public function __construct(WarehouseRepository $warehouseRepo)
    {
        $this->warehouseRepo = $warehouseRepo;
    }

    /**
     * Returns all warehouses.
     */
    public function index(): JsonResponse
    {
        return JsonResponse::successful($this->warehouseRepo->all());
    }

    /**
     * Creates a new warehouse.
     */
    public function store(CreateOrUpdateWarehouseRequest $request): JsonResponse
    {
        $request->merge(['name' => Str::getNamesJson($request->names)]);
        $warehouseId = $this->warehouseRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $warehouseId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns werahouse data by id.
     */
    public function show(WarehouseRepository $warehouseRepo): JsonResponse
    {
        return JsonResponse::successful($warehouseRepo->data());
    }

    /**
     * Updates warehouse data.
     */
    public function update(
        CreateOrUpdateWarehouseRequest $request,
        WarehouseRepository $warehouseRepo
    ): JsonResponse {
        $request->merge(['name' => Str::getNamesJson($request->names)]);
        $warehouseRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the warehouse.
     */
    public function destroy(WarehouseRepository $warehouseRepo): JsonResponse
    {
        $warehouseRepo->delete();

        return JsonResponse::successful();
    }
}
