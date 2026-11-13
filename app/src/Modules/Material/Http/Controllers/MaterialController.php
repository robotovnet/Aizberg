<?php

declare(strict_types=1);

namespace Modules\Material\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Material\Http\Requests\CreateOrUpdateMaterialRequest;
use Modules\Material\Repositories\MaterialRepository;
use Illuminate\Http\Request;

/**
 * Class MaterialController
 * @package Modules\Material\Http\Controllers
 */
class MaterialController extends Controller
{
    /** @var MaterialRepository An instance of the Material Repository. */
    private MaterialRepository $materialRepo;

    /**
     * MaterialController constructor.
     */
    public function __construct(MaterialRepository $materialRepo)
    {
        $this->materialRepo = $materialRepo;
    }

    /**
     * Returns all materials.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search', null);

        return JsonResponse::successful($this->materialRepo->all($search));
    }

    /**
     * Returns all sections of materials.
     */
    public function getSections(): JsonResponse
    {
        return JsonResponse::successful($this->materialRepo->sections());
    }

    /**
     * Creates a new material.
     */
    public function store(CreateOrUpdateMaterialRequest $request): JsonResponse
    {
        $materialId = $this->materialRepo->create($request->all());

        return JsonResponse::successful([
            'id'      => $materialId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns material data by id.
     */
    public function show(MaterialRepository $materialRepo): JsonResponse
    {
        return JsonResponse::successful($materialRepo->data());
    }

    /**
     * Updates material data.
     */
    public function update(
        CreateOrUpdateMaterialRequest $request,
        MaterialRepository $materialRepo
    ): JsonResponse {
        $materialRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the material.
     */
    public function destroy(MaterialRepository $materialRepo): JsonResponse
    {
        $materialRepo->delete();

        return JsonResponse::successful();
    }
}
