<?php

declare(strict_types=1);

namespace App\Helpers;

use Modules\Settings\Entities\Settings;

/**
 * Class LangPhrases
 * @package App\Helpers
 */
class LangPhrases
{
    public const SUCCESS_MESSAGE = 'success';
    public const ERROR_MESSAGE   = 'The given data was invalid.';

    /**
     * Returns the language phrase in the requested language.
     * If the requested language is not available, uses the default language.
     */
    public static function getPhraseInRequiredLang(string $langCode, string $phrase)
    {
        $decoded = json_decode($phrase, true);
        
        if (!is_array($decoded)) {
            return '';
        }

        // Если запрошенный язык существует, возвращаем его
        if (isset($decoded[$langCode])) {
            return $decoded[$langCode];
        }

        // Получаем язык по умолчанию из настроек
        $defaultLangCode = Settings::where('code', 'DEFAULT_LANGUAGE')->first()?->toArray()['value'] ?? 'ru';

        // Если язык по умолчанию существует, возвращаем его
        if (isset($decoded[$defaultLangCode])) {
            return $decoded[$defaultLangCode];
        }

        // Если 'ru' существует, возвращаем его
        if (isset($decoded['ru'])) {
            return $decoded['ru'];
        }

        // Возвращаем первое доступное значение или пустую строку
        return !empty($decoded) ? reset($decoded) : '';
    }
}
