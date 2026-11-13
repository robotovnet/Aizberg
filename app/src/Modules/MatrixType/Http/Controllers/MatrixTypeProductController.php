<?php

namespace Modules\MatrixType\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\MatrixType\Repositories\MatrixTypeRepository;

class MatrixTypeProductController extends Controller
{
    private MatrixTypeRepository $repository;

    public function __construct(MatrixTypeRepository $repository) 
    {
        $this->repository = $repository;
    }

    /**
     * Get matrix type ID for product
     *
     * @param int $productId
     * @return JsonResponse
     */
    public function getMatrixTypeId(int $productId): JsonResponse
    {
        $matrixTypeId = $this->repository->getMatrixTypeIdByProduct($productId);

        if ($matrixTypeId === null) {
            $matrixTypeId = 0;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'matrix_type_id' => $matrixTypeId
            ]
        ]);
    }
} 