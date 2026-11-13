<?php

namespace Modules\Email\Services;

use Aws\Credentials\Credentials;
use Aws\SesV2\SesV2Client;
use Aws\SesV2\Exception\SesV2Exception;
use Illuminate\Support\Facades\Config;
use Nwidart\Modules\Facades\Module;

// Описание и полезные даннные по сервису AWS SES V2
// https://boto3.amazonaws.com/v1/documentation/api/latest/reference/services/sesv2/client/send_email.html
// https://awscli.amazonaws.com/v2/documentation/api/latest/reference/sesv2/send-email.html
// https://github.com/jmhobbs/Swiftmailer-Transport--AWS-SES/blob/master/classes/Swift/AWSTransport.php
// https://github.com/domdomegg/aws-ses-v2-local/blob/master/src/v2/sendEmail.ts

class YandexMailerService
{
    private const API_ENDPOINT = "https://postbox.cloud.yandex.net";

    private string $fromName;
    private string $fromEmail;

    private SesV2Client $sesClient;

    public function __construct()
    {
        $moduleName = Module::getModuleName(__NAMESPACE__);

        $this->fromName   = config('app.name');
        $this->fromEmail  = Config::get($moduleName . '.fromEmail');

        $credentials = new Credentials(env('YANDEX_CLOUD_POSTBOX_KEY'), env('YANDEX_CLOUD_POSTBOX_SECRET'));

        $this->sesClient = new SesV2Client([
            'version'     => 'latest',
            'endpoint'    => self::API_ENDPOINT,
            'region'      => 'ru-central1',
            'credentials' => $credentials,
        ]);
    }

    public function send(string $subject, string $html, array|string $toEmail): void
    {
        $toAddresses = (is_array($toEmail)) ? $toEmail : [$toEmail];

        try {
            $result = $this->sesClient->sendEmail([
                'Destination' => [
                    'ToAddresses' => $toAddresses,
                ],
                'ReplyToAddresses' => [$this->fromEmail],
                'FromEmailAddress' => $this->fromName . " <" . $this->fromEmail . ">",
                'Content' => [
                    'Simple' => [
                        'Body' => [
                            'Html' => [
                                'Charset' => 'UTF-8',
                                'Data' => $html,
                            ]
                        ],
                        'Subject' => [
                            'Charset' => 'UTF-8',
                            'Data' => $subject,
                        ],
                    ],
                ],
            ]);
            //var_dump($result);
        } catch (SesV2Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }
}
