<?php

declare(strict_types=1);

namespace Modules\Language\Services;

use Cookie;
use App;
use Illuminate\Support\Facades\Config;
use Nwidart\Modules\Facades\Module;
use Modules\Settings\Repositories\SettingsRepository;
use Modules\Language\Repositories\LanguageRepository;

/**
 * Class LanguageService
 * @package Modules\Language\Services
 */
class LanguageService
{
    /** @var string The name of the cookie that stores the client's active language. */
    private string $languageCookieName;

    /** @var string Fallback language code. */
    private string $fallbackCode;

    /** @var int Cookies lifetime in minutes. */
    private int $lifetimeCookieInMin;

    /** @var SettingsRepository An instance of the Settings Repository. */
    private SettingsRepository $settingsRepository;

    /** @var LanguageRepository An instance of the Language Repository. */
    private LanguageRepository $languageRepository;

    /** @var string[] Stores the module settings. */
    private array $settings;

    /**
     * LanguageService constructor.
     */
    public function __construct(SettingsRepository $settingsRepository, LanguageRepository $languageRepository)
    {
        $moduleName = Module::getModuleName(__NAMESPACE__);

        $this->settings['default_language'] = Config::get($moduleName . '.settings_codes.default_language');

        $this->languageCookieName   = Config::get($moduleName . '.cookie_name');
        $this->fallbackCode         = Config::get($moduleName . '.fallback_code');
        $this->lifetimeCookieInMin  = Config::get($moduleName . '.lifetime_cookie_in_min');

        $this->settingsRepository = $settingsRepository;
        $this->languageRepository = $languageRepository;
    }

    /**
     * Returns the system default language.
     */
    public function getDefault(): string
    {
        $defaultLanguage = $this->settingsRepository->getSettingByCode($this->settings['default_language']);

        if (!is_null($defaultLanguage)) {
            return $defaultLanguage;
        }

        return $this->fallbackCode;
    }

    /**
     * Returns the name of the cookie that stores the client's active language.
     */
    public function getLanguageCookieName(): string
    {
        return $this->languageCookieName;
    }

    /**
     * Checks the system support the specified language.
     *
     * @param  string|null  $code Language code
     */
    public function canSupport(string|null $code): bool
    {
        if (!is_null($code) && $this->languageRepository->checkSupportsLanguage($code)) {
            return true;
        }

        return false;
    }

    /**
     * Sets active language into cookie.
     *
     * @param  string|null  $code Language code
     */
    public function makeActive(string|null $code): void
    {
        Cookie::queue(
            Cookie::make($this->languageCookieName, $code, $this->lifetimeCookieInMin)
        );
    }

    /**
     * Gets active language from cookie.
     */
    public function retrieveActive(): string|null
    {
        // Приоритет 1: заголовок X-Client-Lang
        if (request()->header('X-Client-Lang') !== null) {
            $code = request()->header('X-Client-Lang');
            $this->makeActive($code);

            return $code;
        }

        // Приоритет 2: параметр lang в URL (выше чем cookie)
        $langFromUrl = request()->query('lang');
        if ($langFromUrl !== null && $this->canSupport($langFromUrl)) {
            $this->makeActive($langFromUrl);
            return $langFromUrl;
        }

        // Приоритет 3: cookie
        return Cookie::get($this->languageCookieName);
    }

    /**
     * Sets the locale for the application.
     */
    public function setLocale(): void
    {
        $lang = $this->retrieveActive();

        if (is_null($lang) || !$this->canSupport($lang)) {
            $lang = $this->getDefault();
            $this->makeActive($lang);
        }

        App::setLocale($lang);
    }
}
