<?php

declare(strict_types=1);

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Client\Http\Requests\CreateClientRequest;
use Modules\Client\Services\ClientRegistrationService;
use Illuminate\Support\Facades\Response;
use App\Helpers\LangPhrases;

/**
 * Class RegisterController
 * @package Modules\Client\Http\Controllers
 */
class RegisterController extends Controller
{
    /** @var ClientRegistrationService An instance of the Client Registration Service. */
    private ClientRegistrationService $clientRegistration;

    /**
     * RegisterController constructor.
     */
    public function __construct(ClientRegistrationService $clientRegistration)
    {
        $this->clientRegistration = $clientRegistration;
    }

    /**
     * Create a new client.
     */
    public function createNewClient(CreateClientRequest $request): JsonResponse
    {
        $clientId = $this->clientRegistration->createNew($request->all());

        return Response::json(
            [
                'id'          => $clientId,
                'message'     => LangPhrases::SUCCESS_MESSAGE,
                'redirect_to' => $this->clientRegistration->getRedirectPath()
            ]
        );
    }
}
