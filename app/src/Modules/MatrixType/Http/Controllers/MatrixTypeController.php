<?php

namespace Modules\MatrixType\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\MatrixType\Repositories\MatrixTypeRepository;
use Modules\MatrixType\Http\Requests\CreateOrUpdateMatrixTypeRequest;
use Illuminate\Http\JsonResponse;

class MatrixTypeController extends Controller
{
    private MatrixTypeRepository $repository;

    public function __construct(MatrixTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(): JsonResponse
    {
        return JsonResponse::successful($this->repository->all());
    }

    public function show(int $matrixTypeId): JsonResponse
    {
        return JsonResponse::successful($this->repository->find($matrixTypeId));
    }

    public function store(CreateOrUpdateMatrixTypeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $this->repository->create($validated);

        return JsonResponse::successful(null, 201);
    }

    public function update(CreateOrUpdateMatrixTypeRequest $request, int $matrixTypeId): JsonResponse
    {
        $validated = $request->validated();

        $this->repository->update($matrixTypeId, $validated);

        return JsonResponse::successful(null, 204);
    }

    public function destroy(int $matrixTypeId): JsonResponse
    {
        $this->repository->delete($matrixTypeId);

        return JsonResponse::successful(null, 204);
    }
}
