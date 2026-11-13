<?php

declare(strict_types=1);

namespace Modules\ClientType\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\ClientType\Http\Requests\CreateOrUpdateClientTypeRequest;
use Modules\ClientType\Repositories\ClientTypeRepository;

/**
 * Class ClientTypeController
 * @package Modules\ClientType\Http\Controllers
 */
class ClientTypeController extends Controller
{
    /** @var ClientTypeRepository An instance of the Client Type Repository. */
    private ClientTypeRepository $clientTypeRepo;

    /**
     * CityController constructor.
     */
    public function __construct(ClientTypeRepository $clientTypeRepo)
    {
        $this->clientTypeRepo = $clientTypeRepo;
    }

    /**
     * Creates a new client type.
     */
    public function store(CreateOrUpdateClientTypeRequest $request): JsonResponse
    {
        $request->merge(['name' => Str::getNamesJson($request->names)]);
        $clientTypeId = $this->clientTypeRepo->create($request->all());

        return JsonResponse::successful([
            'id'      => $clientTypeId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns all client types.
     */
    public function index(): JsonResponse
    {
        return JsonResponse::successful($this->clientTypeRepo->all());
    }

    /**
     * Returns client type data by id.
     */
    public function show(ClientTypeRepository $clientTypeRepo): JsonResponse
    {
        return JsonResponse::successful($clientTypeRepo->data());
    }

    /**
     * Updates client type data.
     */
    public function update(CreateOrUpdateClientTypeRequest $request, ClientTypeRepository $clientTypeRepo): JsonResponse
    {
        $request->merge(['name' => Str::getNamesJson($request->names)]);
        $clientTypeRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the client type.
     */
    public function destroy(ClientTypeRepository $clientTypeRepo): JsonResponse
    {
        $clientTypeRepo->delete();

        return JsonResponse::successful();
    }
}
