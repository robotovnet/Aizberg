<?php

namespace Modules\MatrixType\Repositories;

interface MatrixTypeRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Returns all matrix types.
     */
    public function all(): array;

    /**
     * Returns a matrix type by ID.
     */
    public function find(int $id): array;

    /**
     * Creates a new matrix type.
     */
    public function create(array $data): array;

    /**
     * Updates a matrix type.
     */
    public function update(int $id, array $data): array;

    /**
     * Deletes a matrix type.
     */
    public function delete(int $id): int;

    /**
     * Get matrix type ID by product ID
     * 
     * @param int $productId
     * @return int|null Matrix type ID or null if not found
     */
    public function getMatrixTypeIdByProduct(int $productId): ?int;
} 