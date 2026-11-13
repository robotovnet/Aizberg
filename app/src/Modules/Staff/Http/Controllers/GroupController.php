<?php

declare(strict_types=1);

namespace Modules\Staff\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use Modules\Staff\Http\Requests\CreateOrUpdateGroupRequest;
use Modules\Staff\Repositories\StaffGroupRepository;
use Modules\Staff\Repositories\StaffPermissionRepository;
use Modules\Staff\Services\PermissionsService;

/**
 * Class GroupController
 * @package Modules\Staff\Http\Controllers
 */
class GroupController extends Controller
{
    /** @var StaffGroupRepository An instance of the Staff Group Repository */
    private StaffGroupRepository $groupRepo;

    /** @var StaffPermissionRepository An instance of the Staff Permission Repository */
    private StaffPermissionRepository $permissionRepo;

    /**
     * GroupController constructor.
     */
    public function __construct(StaffGroupRepository $groupRepo, StaffPermissionRepository $permissionRepo)
    {
        $this->groupRepo = $groupRepo;
        $this->permissionRepo = $permissionRepo;
    }

    /**
     * Saves a new group to the storage.
     */
    public function store(CreateOrUpdateGroupRequest $request, PermissionsService $permissionsService): JsonResponse
    {
        $newGroup = $this->groupRepo
            ->create($request->only(['name', 'start_policy']))
            ->addPermissions($permissionsService->getPermissionIds(request('permissions')));

        return JsonResponse::successful([
            'id' => $newGroup->getId(),
            'message' => 'success',
        ]);
    }

    /**
     * Returns data about all created groups.
     */
    public function index(): JsonResponse
    {
        $groups = $this->groupRepo->getAll();

        return JsonResponse::successful($groups);
    }

    /**
     * Returns data about certain group.
     */
    public function show(StaffGroupRepository $groupRepo): JsonResponse
    {
        $group = $groupRepo->getCurrent();

        return JsonResponse::successful($group);
    }

    /**
     * Updates group data.
     */
    public function update(
        CreateOrUpdateGroupRequest $request,
        StaffGroupRepository $groupRepo,
        PermissionsService $permissionsService
    ): JsonResponse {
        $groupRepo
            ->update($request->only(['name', 'start_policy']))
            ->deletePermissions($this->permissionRepo->getAllIds())
            ->addPermissions($permissionsService->getPermissionIds(request('permissions')));

        return JsonResponse::successful();
    }

    /**
     * Deletes a group.
     */
    public function destroy(StaffGroupRepository $groupRepo): JsonResponse
    {
        if ($groupRepo->haveActiveMembers((int)request()->segment(3))) {
            return response()->json([
                'errors'  => ['Невозможно удалить группу, в которой есть участники'],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $groupRepo
                ->deletePermissions($this->permissionRepo->getAllIds())
                ->delete();

            return JsonResponse::successful();
        }
    }
}

