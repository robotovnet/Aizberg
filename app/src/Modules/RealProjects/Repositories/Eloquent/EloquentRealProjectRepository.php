<?php

declare(strict_types=1);

namespace Modules\RealProjects\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Modules\RealProjects\Entities\RealProject;
use Modules\RealProjects\Repositories\RealProjectRepository;

/**
 * Class EloquentRealProjectRepository
 * @package Modules\RealProjects\Repositories\Eloquent
 */
class EloquentRealProjectRepository implements RealProjectRepository
{
    /** @var RealProject An instance of the Eloquent Model. */
    public RealProject $model;

    /**
     * Repository constructor.
     */
    public function __construct(RealProject $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function all(): Collection
    {
        return $this->model->newQuery()
            ->orderBy('sort')
            ->orderBy('id')
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function find(int $id): RealProject
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): RealProject
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * @inheritDoc
     */
    public function update(int $id, array $data): RealProject
    {
        $project = $this->find($id);
        $project->update($data);

        return $project;
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id): void
    {
        $this->find($id)->delete();
    }
}
