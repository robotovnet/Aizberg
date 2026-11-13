<?php

declare(strict_types=1);

namespace Modules\Email\Services;

use Modules\Settings\Entities\Settings;
use Illuminate\Support\Facades\App;

/**
 * Class MailerService
 * @package Modules\Email\Services
 */
class MailerService
{
    private string $mailTransport;
    private object $mailer;

    public function __construct()
    {
        if (App::environment('local')) {
            $this->mailTransport = Settings::where('code', 'EMAIL_TRANSPORT')->first()->toArray()['value'];

            $className = "Modules\Email\Services\\" . ucfirst($this->mailTransport) . 'MailerService';
            $this->mailer = new $className();
        }
    }

    /**
     * Sends a message.
     */
    public function send(string $subject, string $html, array|string $toEmail, string|false $replyTo = false): void
    {
        $this->mailer->send($subject, $html, $toEmail, $replyTo);
    }
}
