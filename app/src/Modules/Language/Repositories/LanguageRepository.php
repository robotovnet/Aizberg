<?php

declare(strict_types=1);

namespace Modules\Language\Repositories;

/**
 * Interface LanguageRepository
 * @package Modules\Language\Repositories
 */
interface LanguageRepository
{
    /**
     * Returns id language by country code.
     *
     * * @param  string  $code Country code
     */
    public function getIdByCode(string $code): int;

    /**
     * Checks whether the language is supported by the system.
     *
     * @param  string  $code Language code
     */
    public function checkSupportsLanguage(string $code): bool;
}
