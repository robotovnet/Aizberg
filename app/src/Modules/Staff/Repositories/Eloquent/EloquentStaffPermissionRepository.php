<?php

declare(strict_types=1);

namespace Modules\Staff\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\Staff\Entities\StaffPermission;
use Modules\Staff\Repositories\StaffPermissionRepository;

/**
 * Class EloquentStaffPermissionRepository
 * @package Modules\Staff\Repositories\Eloquent
 */
class EloquentStaffPermissionRepository implements StaffPermissionRepository
{
    /** @var Model An instance of the Eloquent Model */
    protected Model $model;

    /**
     * EloquentStaffPermissionRepository constructor.
     * @param  Model  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function getIdsByPolicyNames(array $permissionsByPolicyNames): array
    {
        return $this->model->whereIn('policy', $permissionsByPolicyNames)->pluck('id')->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getAllIds(): array
    {
        return $this->model->all()->pluck('id')->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        $allPermissions = $this->model->get(StaffPermission::SELECT_ATTRIBUTES)->toArray();

        return Arr::onlyActiveLang(
            StaffPermission::LOCALIZATION_ATTRIBUTES,
            App::getLocale(),
            $allPermissions
        );
    }
}
