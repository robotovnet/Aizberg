<?php

declare(strict_types=1);

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Lang;
use App\Helpers\LangPhrases;
use Modules\Client\Http\Requests\PasswordRestoreFirstStepRequest;
use Modules\Client\Http\Requests\PasswordRestoreSecondStepRequest;
use Modules\Client\Http\Requests\PasswordRestoreThirdStepRequest;
use Modules\Client\Services\PasswordRestoreService;
use Modules\Client\Mail\PasswordRestore;
use Modules\Client\Repositories\ClientRepository;
use Modules\Sms\Services\SmsService;
use Modules\Email\Services\MailerService;

/**
 * Class PasswordRestoreController
 * @package Modules\Client\Http\Controllers
 */
class PasswordRestoreController extends Controller
{
    /** @var PasswordRestoreService An instance of the Password Restore Service. */
    private PasswordRestoreService $passwordRestoreService;

    /** @var ClientRepository An instance of the Client Repository. */
    private ClientRepository $clientRepository;

    /** @var MailerService An instance of the Mailer Service. */
    private MailerService $mailerService;

    /**
     * RegisterController constructor.
     */
    public function __construct(
        PasswordRestoreService $passwordRestoreService,
        ClientRepository $clientRepository,
        MailerService $mailerService
    ) {
        $this->passwordRestoreService = $passwordRestoreService;
        $this->clientRepository       = $clientRepository;
        $this->mailerService          = $mailerService;
    }

    /**
     * First step: Password recovery process by phone.
     */
    public function firstStepByPhone(PasswordRestoreFirstStepRequest $request, SmsService $smsService): JsonResponse
    {
        //if (!$request->has('phone')) {
        //    $message = Lang::get('client::password_restore.phone_required');
        //    return Response::json([ 'message' => $message ], 200, [], JSON_UNESCAPED_UNICODE);
        //}

        if ($request->has('phone')) {
            $client = $this->clientRepository->findClientByPhone($request->input('phone'));
        } else {
            $client = $this->clientRepository->findClientByEmail($request->input('email'));
        }

        $this->passwordRestoreService
            ->generateCode()
            ->setAttemptsCount(0)
            ->resetCodeCheck()
            ->rememberClient($client['id']);

        $code = $this->passwordRestoreService->getCode();

        $message = Lang::get('client::password_restore.sms_message_text', ['code' => $code]);
        $smsService->send($client['phone'], $message);

        return Response::json(
            [
                'message' => LangPhrases::SUCCESS_MESSAGE,
                'redirect_to' => $this->passwordRestoreService->redirectToSecondStep()
            ]
        );
    }

    /**
     * First step: Password recovery process by email.
     */
    public function firstStepByEmail(PasswordRestoreFirstStepRequest $request): JsonResponse
    {
        //if (!$request->has('email')) {
        //    $message = Lang::get('client::password_restore.email_required');
        //    return Response::json([ 'message' => $message ], 200, [], JSON_UNESCAPED_UNICODE);
        //}

        if ($request->has('email')) {
            $client = $this->clientRepository->findClientByEmail($request->input('email'));
        } else {
            $client = $this->clientRepository->findClientByPhone($request->input('phone'));
        }

        $this->passwordRestoreService
            ->generateCode()
            ->setAttemptsCount(0)
            ->resetCodeCheck()
            ->rememberClient($client['id']);

        $html = (new PasswordRestore($this->passwordRestoreService->getCode()))->render();
        $this->mailerService->send(Lang::get('client::emails/password_restore.subject'), $html, $client['email']);

        return Response::json(
            [
                'message' => LangPhrases::SUCCESS_MESSAGE,
                'redirect_to' => $this->passwordRestoreService->redirectToSecondStep()
            ]
        );
    }

    /**
     * Second step: Checking the entered code for truth.
     */
    public function secondStep(PasswordRestoreSecondStepRequest $request): JsonResponse
    {
        if (!$this->passwordRestoreService->checkCodeEntryAttempts()) {
            $message = Lang::get('client::password_restore.too_many_attempts');
            return Response::json([ 'message' => $message ], 400, [], JSON_UNESCAPED_UNICODE);
        }

        if ($this->passwordRestoreService->getCode() === intval($request->input('code'))) {
            $this->passwordRestoreService->codeSuccessfullyVerified();

            return Response::json(
                [
                    'message' => LangPhrases::SUCCESS_MESSAGE,
                    'redirect_to' => $this->passwordRestoreService->redirectToThirdStep()
                ]
            );
        }

        $message = Lang::get('client::password_restore.invalid_code');
        return Response::json([ 'message' => $message ], 400, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Third step: Set new password for client
     */
    public function thirdStep(PasswordRestoreThirdStepRequest $request): JsonResponse
    {
        if ($this->passwordRestoreService->isCodeVerified() && $this->passwordRestoreService->getClientId()) {
            $this->clientRepository->setNewPassword(
                $this->passwordRestoreService->getClientId(),
                $request->input('password')
            );

            $this->passwordRestoreService->resetClientId()->resetCodeCheck();

            return Response::json([
                'message'     => LangPhrases::SUCCESS_MESSAGE,
                'redirect_to' => '/' . request()->segment(1) . '/login',
            ]);
        }

        $message = Lang::get('client::password_restore.password_not_changed');
        return Response::json(['message' => $message ], 400, [], JSON_UNESCAPED_UNICODE);
    }
}
