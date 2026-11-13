<?php

declare(strict_types=1);

namespace Modules\RealProjects\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\RealProjects\Entities\RealProject;

interface RealProjectRepository
{
    /**
     * Returns all projects ordered by "sort", then by ID.
     *
     * @return Collection<int, RealProject>
     */
    public function all(): Collection;

    /**
     * Returns a project by ID.
     */
    public function find(int $id): RealProject;

    /**
     * Creates a new project.
     */
    public function create(array $data): RealProject;

    /**
     * Updates a project.
     */
    public function update(int $id, array $data): RealProject;

    /**
     * Deletes a project.
     */
    public function delete(int $id): void;
}
