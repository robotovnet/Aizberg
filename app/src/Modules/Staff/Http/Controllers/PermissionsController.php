<?php

declare(strict_types=1);

namespace Modules\Staff\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Staff\Repositories\StaffPermissionRepository;

/**
 * Class GroupController
 * @package Modules\Staff\Http\Controllers
 */
class PermissionsController extends Controller
{
    /** @var StaffPermissionRepository An instance of the Staff Permission Repository */
    private StaffPermissionRepository $permissionRepo;

    /**
     * PermissionsController constructor.
     * @param  StaffPermissionRepository  $permissionRepo
     */
    public function __construct(StaffPermissionRepository $permissionRepo)
    {
        $this->permissionRepo = $permissionRepo;
    }

    /**
     * Returns data about all created permissions.
     */
    public function index(): JsonResponse
    {
        $permissions = $this->permissionRepo->getAll();

        return JsonResponse::successful($permissions);
    }
}
