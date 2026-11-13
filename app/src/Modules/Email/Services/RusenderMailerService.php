<?php

namespace Modules\Email\Services;

use Illuminate\Support\Facades\Config;
use Nwidart\Modules\Facades\Module;

// https://rusender.ru/developer/api/email/
class RusenderMailerService
{
    private const API_ENDPOINT = "https://api.beta.rusender.ru/api/v1/external-mails/send";

    private string $fromName;
    private string $fromEmail;
    private string $replyToEmail;

    public function __construct()
    {
        $moduleName = Module::getModuleName(__NAMESPACE__);

        $this->fromName   = config('app.name');
        $this->fromEmail  = Config::get($moduleName . '.fromEmail');
        $this->replyToEmail = Config::get($moduleName . '.replyToEmail');
    }

    public function send(string $subject, string $html, array|string $toEmail, string|false $replyTo = false): void
    {
        // Если $toEmail - массив, отправляем письмо каждому получателю отдельно
        if (is_array($toEmail)) {
            foreach ($toEmail as $email) {
                $this->sendSingleEmail($subject, $html, $email, $replyTo);
            }
        } else {
            $this->sendSingleEmail($subject, $html, $toEmail, $replyTo);
        }
    }

    private function sendSingleEmail(string $subject, string $html, string $toEmail, string|false $replyTo = false): void
    {
        $data = [
            "mail" => [
                "to" => [
                    "email" => $toEmail,
                ],
                "from" => [
                 "email" => $this->fromEmail,
                 "name"  => $this->fromName,
                ],
                "subject" => $subject,
                "html"    => $html,
                "headers" => [
                    'Reply-To' => $replyTo ?? $this->replyToEmail,
                ],
            ]
        ];

        $headers = [
           'Content-Type: application/json',
           'X-Api-Key: ' . env('RUSENDER_API_KEY'),
        ];

        $ch = curl_init(self::API_ENDPOINT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
    }
}
