<?php

declare(strict_types=1);

namespace Modules\Sms\Services;

/**
 * Interface SmsService
 * @package Modules\Sms\Services
 */
interface SmsService
{
    /**
     * Sends sms message.
     */
    public function send(string $phone, string $message): bool|string;
}
