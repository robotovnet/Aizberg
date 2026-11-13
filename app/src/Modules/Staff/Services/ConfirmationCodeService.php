<?php

declare(strict_types=1);

namespace Modules\Staff\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Modules\Sms\Services\SmsService;
use Modules\Staff\Mail\ConfirmationCode;
use Illuminate\Support\Str;
use Modules\Email\Services\MailerService;

/**
 * Class ConfirmationCodeService
 * @package Modules\Staff\Services
 */
class ConfirmationCodeService
{
    /** @var string Session variable name. */
    private const SESSION_VAR_NAME = 'confirmation_code';

    /** @var string Prefix of session variables. */
    private string $entity;

    /** @var MailerService An instance of the Mailer Service. */
    private MailerService $mailerService;

    /**
     * ConfirmationCodeService constructor.
     * @param  string  $entity What entity the user is updating.
     */
    public function __construct(string $entity)
    {
        $this->entity = $entity;
        $this->mailerService = new MailerService();
    }

    /**
     * Starts sending the confirmation code.
     */
    public function sendCode(): void
    {
        $entity = ($this->entity == 'email') ? 'phone' : 'email';

        $sendFunctionName = 'sendCodeBy' . Str::ucfirst($entity);
        $this->$sendFunctionName(Auth::user()->$entity);
    }

    /**
     * Checks the confirmation code for truth.
     */
    public function checkCode(int $code): bool
    {
        if ($code === $this->getCode()) {
            $this->resetCode();

            return true;
        }

        return false;
    }

    /*
     * Returns the entity that the user updates.
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * Sends the code to the email address.
     */
    private function sendCodeByEmail(string $email): void
    {
        $html = (new ConfirmationCode($this->generateCode()))->render();
        $this->mailerService->send(Lang::get('staff::emails/confirmation_code.subject'), $html, $email);
    }

    /**
     * Sends code in sms.
     */
    private function sendCodeByPhone(string $phone): void
    {
        $smsService = App::make(SmsService::class);

        $message = Lang::get('staff::profile.sms_message_text', ['code' => $this->generateCode()]);
        $smsService->send($phone, $message);
    }

    /**
     * Saves code to session.
     */
    private function rememberCode(int $code): void
    {
        Session::put($this->entity . '.' . self::SESSION_VAR_NAME, $code);
    }

    /**
     * Generates a confirmation code.
     */
    private function generateCode(): int
    {
        $code = mt_rand(100000, 999999);
        $this->rememberCode($code);

        return $code;
    }

    /**
     * Returns the code.
     */
    private function getCode(): ?int
    {
        return Session::get($this->entity . '.' . self::SESSION_VAR_NAME) ?? null;
    }

    /**
     * Reset code.
     */
    private function resetCode(): void
    {
        Session::forget($this->entity . '.' . self::SESSION_VAR_NAME);
    }
}
