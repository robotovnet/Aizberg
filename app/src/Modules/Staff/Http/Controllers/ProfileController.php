<?php

declare(strict_types=1);

namespace Modules\Staff\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Modules\DashboardMenu\Entities\DashboardMenu;
use Modules\Staff\Repositories\StaffRepository;
use Modules\Staff\Http\Requests\ProfileUpdateNameRequest;
use Modules\Staff\Http\Requests\ProfileUpdatePhoneRequest as ReqPhoneUpdate;
use Modules\Staff\Http\Requests\ProfileUpdateEmailRequest;
use Modules\Staff\Http\Requests\ProfileUpdatePasswordRequest;
use Modules\Staff\Http\Requests\ProfileUpdateUserpicRequest;
use Modules\Staff\Services\UserpicService;
use Modules\Staff\Services\ConfirmationCodeService as CodeService;

/**
 * Class ProfileController
 * @package Modules\Staff\Http\Controllers
 */
class ProfileController extends Controller
{
    /** @var StaffRepository An instance of the Staff Repository */
    private StaffRepository $staffRepository;

    /**
     * ProfileController constructor.
     */
    public function __construct(StaffRepository $staffRepository)
    {
        $this->middleware('throttle:5,1,code_entry')->only(['updateEmail', 'updatePhone']);
        $this->middleware('throttle:1,2,email')->only(['updateEmailStart']);
        $this->middleware('throttle:1,2,phone')->only(['updatePhoneStart']);

        $this->staffRepository = $staffRepository;

        $this->middleware(function ($request, $next) {
            $this->staffRepository->setModel(Auth::id());
            return $next($request);
        });
    }

    /**
     * Returns employee profile data.
     */
    public function profile(Authenticatable $user): JsonResponse
    {
        $profileData = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'email' => $user->email,
            'userpic_name' => $user->userpic_name,
            'full_name' => $user->full_name,
            'whatsapp' => $user->whatsapp,
            'telegram' => $user->telegram,
            'max' => $user->max,
        ];

        $profileData['permissions'] = Auth::user()->getAllPermission();
        $profileData['groups']      = Auth::user()->getGroups();
        $profileData['redirect_to'] = $this->getRedirectTo($profileData['groups']);

        return JsonResponse::successful($profileData);
    }

    /**
     * Updates the name of the employee profile.
     */
    public function updateName(ProfileUpdateNameRequest $request): JsonResponse
    {
        $data = $request->only('first_name', 'last_name', 'whatsapp', 'telegram', 'max');
        $this->staffRepository->update($data);

        return JsonResponse::successful();
    }

    /**
     * Updates the email of the employee profile.
     */
    public function updateEmail(ProfileUpdateEmailRequest $request, CodeService $codeService): JsonResponse
    {
        return $this->updateEmailOrPhone($codeService);
    }

    /**
     * Updates the phone of the employee profile.
     */
    public function updatePhone(ReqPhoneUpdate $request, CodeService $codeService): JsonResponse
    {
        return $this->updateEmailOrPhone($codeService);
    }

    /**
     * Starting the procedure for changing the email
     */
    public function updateEmailStart(CodeService $codeService): JsonResponse
    {
        return $this->updateEmailOrPhone($codeService);
    }

    /**
     * Starting the procedure for changing the phone
     */
    public function updatePhoneStart(CodeService $codeService): JsonResponse
    {
        return $this->updateEmailOrPhone($codeService);
    }

    /**
     * Sets a new password.
     */
    public function updatePassword(ProfileUpdatePasswordRequest $request, Authenticatable $user): JsonResponse
    {
        if (Hash::check($request->get('current_password'), $user->password)) {
            $data = $request->only('password');
            $this->staffRepository->update($data);

            return JsonResponse::successful();
        }

        return JsonResponse::failed(['message' => Lang::get('staff::profile.failed_change_password')]);
    }

    /**
     * Uploading a new user picture
     */
    public function updateUserpic(ProfileUpdateUserpicRequest $request, UserpicService $userpic): JsonResponse
    {
        $userpic->save();
        $this->staffRepository->update(['userpic_name' => Str::of($userpic->getPathToFile())->basename()]);

        return JsonResponse::successful([ 'image_path' => $userpic->getPathToFile() ]);
    }

    /**
     * Replaces the old phone number (email) with a new number (email).
     */
    private function updateEmailOrPhone(CodeService $codeService): JsonResponse
    {
        $entity = $codeService->getEntity();

        if (!request()->has('code')) {
            $codeService->sendCode();

            return JsonResponse::successful(['message' => Lang::get('staff::profile.code_send')]);
        } elseif ($codeService->checkCode((int)request()->input('code'))) {
            $data = request()->only($entity);
            $this->staffRepository->update($data);

            return JsonResponse::successful();
        }

        return JsonResponse::failed(['message' => Lang::get('staff::profile.invalid_code')]);
    }

    private function getRedirectTo($groups)
    {
        if (isset($groups[0])) {
            $startPolicy = $groups[0]['start_policy'];

            $redirectData = DashboardMenu::where('policy', $startPolicy)->first(['href']);
            if (isset($redirectData['href'])) {
                return $redirectData['href'];
            }
        }

        return null;
    }
}
