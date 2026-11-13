<?php

declare(strict_types=1);

namespace Modules\Staff\Repositories\Eloquent;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Modules\Staff\Entities\StaffPermission;
use Modules\Staff\Entities\StaffGroup;
use Modules\Staff\Repositories\StaffGroupRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class EloquentStaffGroupRepository
 * @package Modules\Staff\Repositories\Eloquent
 */
class EloquentStaffGroupRepository implements StaffGroupRepository
{
    /** @var Model An instance of the Eloquent Model */
    protected Model $model;

    /**
     * EloquentStaffGroupRepository constructor.
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
            throw new ModelNotFoundException('Group not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): self
    {
        $this->model = $this->model->create($data);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function update(array $data): self
    {
        $this->model->update($data);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addPermissions(array $permissions): self
    {
        $this->model->permissions()->attach($permissions);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function haveActiveMembers(int $groupId): bool
    {
        if (StaffGroup::where('id', $groupId)->withCount('members')->first()['members_count'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function deletePermissions(array $permissionIds): self
    {
        $this->model->permissions()->detach($permissionIds);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        $groups = $this->getBasicQuery()
                    ->get(StaffGroup::SELECT_ATTRIBUTES);

        return $this->localizeAndCleanUp($groups);
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function getCurrent(): array
    {
        $group = $this->getBasicQuery()
                    ->find($this->model->getAttribute('id'), StaffGroup::SELECT_ATTRIBUTES);

        return $this->localizeAndCleanUp(collect([$group]))[0];
    }

    /**
     *  Implements localization and removes unnecessary data.
     */
    private function localizeAndCleanUp($collection): array
    {
        return Collection::localizeAndCleanUp(
            $collection,
            'permissions',
            'pivot',
            StaffPermission::LOCALIZATION_ATTRIBUTES
        )->toArray();
    }

    /**
     * Builds a basic request to get data about groups.
     */
    private function getBasicQuery(): Builder
    {
        return $this->model
            ->withCount('members') // returns the number of group members
            ->with(['permissions' => function ($query) {
                $query->select(StaffPermission::SELECT_ATTRIBUTES);
            }]);
    }
}
