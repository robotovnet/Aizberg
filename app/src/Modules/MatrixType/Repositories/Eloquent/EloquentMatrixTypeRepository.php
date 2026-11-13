<?php

namespace Modules\MatrixType\Repositories\Eloquent;

use Modules\MatrixType\Repositories\MatrixTypeRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class EloquentMatrixTypeRepository
 * @package Modules\MatrixType\Repositories\Eloquent
 */
class EloquentMatrixTypeRepository implements MatrixTypeRepository
{
    /** @var Model An instance of the Eloquent Model. */
    public Model $model;

    /**
     * Repository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function setModel(int $id): self
    {
        try {
            $this->model = $this->model->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ModelNotFoundException('MatrixType not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->model->all()->toArray();
    }

    /**
     * @inheritDoc
     */
    public function find(int $id): array
    {
        return $this->model->findOrFail($id)->toArray();
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): array
    {
        return $this->model->create($data)->toArray();
    }

    /**
     * @inheritDoc
     */
    public function update(int $id, array $data): array
    {
        $matrixType = $this->model->findOrFail($id);
        $matrixType->update($data);
        
        return $matrixType->toArray();
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id): int
    {
        return $this->model->destroy($id);
    }

    /**
     * @inheritDoc
     */
    public function getMatrixTypeIdByProduct(int $productId): ?int
    {
        $matrixType = DB::table('product_matrix_type')->where('product_id', $productId)->first();
        
        return $matrixType ? $matrixType->matrix_type_id : null;
    }
} 