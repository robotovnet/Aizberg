<?php

declare(strict_types=1);

namespace Modules\Staff\Repositories\Eloquent;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\DashboardMenu\Entities\DashboardMenu;
use Modules\Staff\Entities\StaffGroup;
use Modules\Staff\Entities\Staff;
use Modules\Staff\Repositories\StaffRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EloquentStaffRepository
 * @package Modules\Staff\Repositories\Eloquent
 */
class EloquentStaffRepository implements StaffRepository
{
    /** @var Model An instance of the Eloquent Model */
    public Model $model;

    /**
     * EloquentStaffRepository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes = []): self
    {
        $this->model->update($attributes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setModel(int $id): self
    {
        $this->model = $this->model->find($id);

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
    public function addGroups(array $groups): self
    {
        $this->model->groups()->attach($groups);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function syncGroups(array $groups): self
    {
        $this->model->groups()->sync($groups);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function block(): self
    {
        $this->model->setAttribute('is_blocked', 'Y')->save();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unblock(): self
    {
        $this->model->setAttribute('is_blocked', 'N')->save();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function profile(): array
    {
        $profile = Staff::with(['groups' => function ($query) {
            $query->select(StaffGroup::SELECT_ATTRIBUTES);
        }])->find($this->model->id, Staff::SELECT_ATTRIBUTES);

        $profile['full_name'] = $profile->full_name;

        return Collection::removeNestedItems(collect([$profile]), 'groups', 'pivot')
            ->toArray()[0];
    }

    /**
     * @inheritDoc
     */
    public function getAll(int $groupId): array
    {
        $allStaff = $this->model
            ->with(['groups' => function ($query) {
                $query->select(StaffGroup::SELECT_ATTRIBUTES);
            }])
            ->when($groupId > 0, function ($query) use ($groupId) {
                return $query->whereHas('groups', function ($query) use ($groupId) {
                    $query->where('staff_groups.id', $groupId);
                });
            })
            ->orderBy('last_name', 'asc')
            ->select(DB::raw("*, CONCAT(last_name, ' ', first_name) as full_name"))
            ->get(Staff::SELECT_ATTRIBUTES);

        return Collection::removeNestedItems($allStaff, 'groups', 'pivot')
            ->toArray();
    }
}
