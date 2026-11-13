<?php

declare(strict_types=1);

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Lang;
use App\Helpers\LangPhrases;
use Modules\Client\Http\Requests\LoginRequest;
use Modules\DashboardMenu\Entities\DashboardMenu;
use Illuminate\Support\Str;

/**
 * Class AuthController
 * @package Modules\Client\Http\Controllers
 */
class AuthController extends Controller
{
    /** @var string Where to redirect a client after login. */
    private string $redirectPathLogin;

    /** @var string Where to redirect a client after logout. */
    private string $redirectPathLogout;

    /** @var string Guard driver name. */
    private string $guard;

    /** @var bool Should remember the client authentication. */
    private bool $shouldRememberAuth;

    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->middleware('throttle:5,1')->only('login');

        $moduleName = Str::singular(request()->segment(1) ?? 'client');

        $this->redirectPathLogin  = Config::get($moduleName . '.redirect_path_after_login');
        $this->redirectPathLogout = Config::get($moduleName . '.redirect_path_after_logout');
        $this->shouldRememberAuth = Config::get($moduleName . '.should_remember_auth');
        $this->guard              = Config::get($moduleName . '.guard');
    }

    /**
     * Client log in.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('phone', 'email', 'password');

        $message = Lang::get('client::auth.failed');

        if (Auth::guard($this->guard)->attempt($credentials, true)) {
            if ((Auth::guard($this->guard)->user())->is_blocked === 'N') {
                if ($this->guard === 'staff') {
                    $profileData['groups'] = (Auth::guard($this->guard)->user())->getGroups();

                    if (isset($profileData['groups'][0])) {
                        $startPolicy = $profileData['groups'][0]['start_policy'];

                        $redirectData = DashboardMenu::where('policy', $startPolicy)->first(['href']);
                        if (isset($redirectData['href'])) {
                            $this->redirectPathLogin = $redirectData['href'];
                        }
                    }
                }

                return Response::json(
                    [
                        'message'     => LangPhrases::SUCCESS_MESSAGE,
                        'redirect_to' => $this->redirectPathLogin,
                        'session_id'  => Session::getId(),
                    ]
                );
            } else {
                $message = Lang::get('client::auth.blocked');
                Auth::guard($this->guard)->logout();
            }
        }

        return Response::json([ 'message' => $message ], 400, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Checks that the session is active
     */
    public function isSessionActive($session_id): JsonResponse
    {
        $isSessionActive = (bool)Session::getHandler()->read($session_id);

        return Response::json([ 'is_session_active' => $isSessionActive ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Client log out
     */
    public function logout(): JsonResponse
    {
        Auth::guard($this->guard)->logout();

        return Response::json(
            [
                'message' => LangPhrases::SUCCESS_MESSAGE,
                'redirect_to' => $this->redirectPathLogout
            ]
        );
    }
}
