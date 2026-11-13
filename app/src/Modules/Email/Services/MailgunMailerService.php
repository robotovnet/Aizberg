<?php

declare(strict_types=1);

namespace Modules\Email\Services;

use Illuminate\Support\Facades\Config;
use Nwidart\Modules\Facades\Module;
use Mail;

/**
 * Class MailgunMailerService
 * @package Modules\Email\Services
 */
class MailgunMailerService
{
    private string $fromName;
    private string $fromEmail;

    public function __construct()
    {
        $moduleName = Module::getModuleName(__NAMESPACE__);

        $this->fromName   = config('app.name');
        $this->fromEmail  = Config::get($moduleName . '.fromEmail');

        Config::set('mail.default', 'mailgun');
        Config::set('mail.mailers.mailgun.domain', Config::get($moduleName . '.mailgun_domain'));
        Config::set('mail.mailers.mailgun.secret', Config::get($moduleName . '.mailgun_secret'));
    }

    /**
     * Sends a message.
     */
    public function send(string $subject, string $html, array|string $toEmail): void
    {
        Mail::send([], [], function ($message) use ($subject, $html, $toEmail) {
            $message->from($this->fromEmail, $this->fromName);
            $message->to($toEmail);
            $message->subject($subject);
            $message->setBody($html, 'text/html');
        });
    }
}
