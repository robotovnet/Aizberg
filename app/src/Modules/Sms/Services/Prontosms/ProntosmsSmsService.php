<?php

declare(strict_types=1);

namespace Modules\Sms\Services\Prontosms;

use Illuminate\Support\Facades\Config;
use Modules\Sms\Services\SmsService;
use Nwidart\Modules\Facades\Module;

/**
 * Class ProntosmsSmsService
 * @package Modules\Sms\Services\Prontosms
 */
class ProntosmsSmsService implements SmsService
{
    /** @var string Provider endpoint. */
    private string $endpoint;

    /** @var string Sender name. */
    private string $sender;

    /** @var string Account name. */
    private string $user;

    /** @var string Account password. */
    private string $password;

    /** @var int Activate text transliteration sms from Cyrillic to Latin. Boolean, accepts 1 or 0 */
    private int $translite;

    /** @var string Provider name. */
    private const PROVIDER_NAME = 'prontosms';

    /** @var array|bool[][] Stores context parameters. */
    private array $contextOptions = [
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
    ];

    public function __construct()
    {
        $moduleName = Module::getModuleName(__NAMESPACE__);

        $this->endpoint  = Config::get($moduleName . '.providers.' . self::PROVIDER_NAME . '.endpoint');
        $this->sender    = Config::get($moduleName . '.providers.' . self::PROVIDER_NAME . '.sender');
        $this->user      = Config::get($moduleName . '.providers.' . self::PROVIDER_NAME . '.user');
        $this->password  = Config::get($moduleName . '.providers.' . self::PROVIDER_NAME . '.password');
        $this->translite = Config::get($moduleName . '.providers.' . self::PROVIDER_NAME . '.translite');
    }

    /**
     * @inheritDoc
     */
    public function send(string $phone, string $message): bool|string
    {
        $url = $this->generateUrl($phone, $message);

        return file_get_contents($url, false, stream_context_create($this->contextOptions));
    }

    /**
     * Generates the destination URL.
     */
    private function generateUrl(string $phone, string $message): string
    {
        return $this->endpoint
            . '?user=' . $this->user
            . '&pwd='  . $this->password
            . '&sadr=' . $this->sender
            . '&dadr=' . $phone
            . '&text=' . urlencode($message)
            . '&translite=' . $this->translite;
    }
}
