<?php

declare(strict_types=1);

namespace Modules\Email\Services;

use Illuminate\Support\Facades\Config;
use Nwidart\Modules\Facades\Module;

/**
 * Class SendboxMailerService
 * @package Modules\Email\Services
 */
class SendboxMailerService
{
    private string $fromName;
    private string $fromEmail;

    private string $apiSendMailEndpoint;
    private string $getTokenEndpoint;

    private string $token;
    private string $apiClientId;
    private string $apiClientSecret;

    public function __construct()
    {
        $moduleName = Module::getModuleName(__NAMESPACE__);

        $this->fromEmail           = Config::get($moduleName . '.fromEmail');
        $this->apiSendMailEndpoint = Config::get($moduleName . '.apiSendMailEndpoint');
        $this->getTokenEndpoint    = Config::get($moduleName . '.getTokenEndpoint');
        $this->apiClientId         = Config::get($moduleName . '.apiClientId');
        $this->apiClientSecret     = Config::get($moduleName . '.apiClientSecret');
        $this->fromName = config('app.name');

        $this->getToken();
    }

    /**
     * Receives a token from the server.
     */
    private function getToken(): bool
    {
        $clientCredentials['grant_type']    = 'client_credentials';
        $clientCredentials['client_id']     = $this->apiClientId;
        $clientCredentials['client_secret'] = $this->apiClientSecret;

        $ch = curl_init($this->getTokenEndpoint);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($clientCredentials));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        if (!isset($result->access_token)) {
            return false;
        }

        $this->token = $result->access_token;

        return true;
    }

    /**
     * Sends a message.
     */
    public function send(string $subject, string $html, array|string $toEmail): void
    {
        $toEmails = [];

        if (is_array($toEmail)) {
            foreach ($toEmail as $key => $email) {
                $toEmails[$key]['email'] = $email;
            }
        } else {
            $toEmails[0]['email'] = $toEmail;
        }

        $emailData = [
            'email' => [
                'html' => base64_encode($html),
                'subject' => $subject,
                'from' => [
                    'name' => $this->fromName,
                    'email' => $this->fromEmail,
                ],
                'to' => $toEmails,
            ]
        ];

        $data = json_encode($emailData);

        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data),
            'Authorization: Bearer ' . $this->token,
        ];

        $ch = curl_init($this->apiSendMailEndpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_close($ch);
    }
}
