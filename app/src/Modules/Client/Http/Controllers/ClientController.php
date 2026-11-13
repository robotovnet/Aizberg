<?php

declare(strict_types=1);

namespace Modules\Client\Http\Controllers;

use App\Helpers\LangPhrases;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Modules\Client\Http\Requests\ChangePasswordRequest;
use Modules\Client\Http\Requests\UpdateOwnProfileRequest;
use Modules\Client\Http\Requests\UpdateClientCardRequest;
use Modules\Client\Repositories\ClientRepository;
use Modules\Client\Services\ClientProfileService;
use Modules\Client\Services\ClientUserpicService;

/**
 * Class ClientController
 * @package Modules\Client\Http\Controllers
 */
class ClientController extends Controller
{
    public function profile(ClientUserpicService $userpicService): JsonResponse
    {
        $user = Auth::guard('client')->user();
        $user->loadMissing('userpic');
        $user->setAttribute('avatar_url', $userpicService->publicUrl($user->userpic?->path));
        $user->makeHidden(['password', 'remember_token', 'userpic']);

        $data = [
            'message' => LangPhrases::SUCCESS_MESSAGE,
            'user' => $user
        ];

        return Response::json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Changes the password of the authenticated client.
     */
    public function updatePassword(ChangePasswordRequest $request): JsonResponse
    {
        $client = Auth::guard('client')->user();

        if (!Hash::check($request->input('current_password'), $client->password)) {
            return JsonResponse::failed([
                'message' => Lang::get('client::profile.failed_change_password'),
            ]);
        }

        // Client's password mutator hashes the plain-text value.
        $client->password = $request->input('password');
        $client->save();

        return JsonResponse::successful();
    }

    /**
     * Partially updates the authenticated client's profile.
     */
    public function updateOwnProfile(
        UpdateOwnProfileRequest $request,
        ClientProfileService $clientProfileService
    ): JsonResponse {
        $clientProfileService->update(
            Auth::guard('client')->user(),
            $request->validated()
        );

        return JsonResponse::successful();
    }

    /**
     * Updates client profile data.
     */
    public function update(
        UpdateClientCardRequest $request,
        ClientRepository $clientRepo
    ): JsonResponse {
        $clientRepo->updateClientProfile($request->all());

        return JsonResponse::successful();
    }

    /**
     * Returns user profile data by id.
     */
    public function show(ClientRepository $clientRepo): JsonResponse
    {
        return JsonResponse::successful($clientRepo->getClientProfile());
    }

    /**
     * Returns users data.
     */
    public function all(Request $request, ClientRepository $clientRepo): JsonResponse
    {
        $cityId       = (int)$request->input('city_id', 0);
        $staffId      = (int)$request->input('staff_id', 0);
        $regDateFrom  = $request->input('reg_date_from', '');
        $regDateTo    = $request->input('reg_date_to', '');
        $search       = $request->input('search', '');
        $clientTypeId = (int)$request->input('client_type_id', 0);
        $page         = (int)$request->input('page', 1);
        $onlyCount    = $request->input('onlyCount', null);
        $sortBy       = $request->input('sort_by', '');
        $sortOrder    = $request->input('sort_order', 'desc');

        return JsonResponse::successful($clientRepo->all(
            $cityId,
            $staffId,
            $regDateFrom,
            $regDateTo,
            $clientTypeId,
            $search,
            $page,
            $onlyCount,
            $sortBy,
            $sortOrder
        ));
    }

    /**
     * Deletes the client.
     */
    public function destroy(ClientRepository $clientRepo): JsonResponse
    {
        $clientRepo->delete();

        return JsonResponse::successful();
    }
}
