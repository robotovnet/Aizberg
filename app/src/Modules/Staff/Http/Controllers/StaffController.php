<?php

declare(strict_types=1);

namespace Modules\Staff\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Staff\Http\Requests\CreateStaffRequest;
use Modules\Staff\Http\Requests\StaffUploadUserpicRequest;
use Modules\Staff\Http\Requests\StaffUpdateRequest;
use Modules\Staff\Http\Requests\StaffSetPasswordRequest;
use Modules\Staff\Repositories\StaffRepository;
use Modules\Staff\Services\UserpicService;

/**
 * Class StaffController
 * @package Modules\Staff\Http\Controllers
 */
class StaffController extends Controller
{
    /** @var StaffRepository An instance of the Staff Repository */
    private StaffRepository $staffRepo;

    /**
     * StaffController constructor.
     */
    public function __construct(StaffRepository $staffRepo)
    {
        $this->staffRepo = $staffRepo;
    }

    /**
     * Creates a new staff.
     */
    public function store(CreateStaffRequest $request): JsonResponse
    {
        $newStaff = $this->staffRepo
            ->create($request->only(['first_name', 'last_name', 'email', 'phone', 'password', 'whatsapp', 'telegram', 'max']))
            ->addGroups($request->groups);

        return JsonResponse::successful([
            'message' => 'success',
            'id'      => $newStaff->model->id,
        ]);
    }

    /**
     * Sets a new password.
     */
    public function setPassword(StaffSetPasswordRequest $request, StaffRepository $staffRepo): JsonResponse
    {
        $staffRepo->update([ 'password' => $request->password ]);

        return JsonResponse::successful();
    }

    /**
     * Updates the user's picture.
     */
    public function uploadUserpic(
        StaffUploadUserpicRequest $request,
        StaffRepository $staffRepo,
        UserpicService $userpic
    ): JsonResponse {
        $userpic->save();
        $userpicPath = $userpic->getPathToFile();

        $staffRepo->update([ 'userpic_name' => Str::of($userpicPath)->basename()]);

        return JsonResponse::successful([ 'image_path' => $userpicPath ]);
    }

    /**
     * Updates staff profile data.
     */
    public function update(StaffUpdateRequest $request, StaffRepository $staffRepo): JsonResponse
    {
        $staffRepo
            ->update($request->only(['first_name', 'last_name', 'email', 'phone', 'whatsapp', 'telegram', 'max']))
            ->syncGroups($request->groups);

        return JsonResponse::successful();
    }

    /**
     * Blocks the staff.
     */
    public function block(StaffRepository $staffRepo): JsonResponse
    {
        $staffRepo->block();

        return JsonResponse::successful();
    }

    /**
     * Unblocks the staff.
     */
    public function unblock(StaffRepository $staffRepo): JsonResponse
    {
        $staffRepo->unblock();

        return JsonResponse::successful();
    }

    /**
     * Returns staff profile data.
     */
    public function profile(StaffRepository $staffRepo): JsonResponse
    {
        return JsonResponse::successful($staffRepo->profile());
    }

    /**
     * Returns all staff.
     */
    public function index(int $groupId = 0): JsonResponse
    {
        return JsonResponse::successful($this->staffRepo->getAll($groupId));
    }
}
