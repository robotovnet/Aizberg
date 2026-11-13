<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Settings\Entities\Settings;
use Modules\City\Entities\City;
use Modules\Shop\Entities\Shop;

/**
 * Class Helper
 * @package App\Helpers
 */
class Helper
{
    /**
     * Verifies that the environment is non-production.
     *
     * @return bool
     */
    public static function isNonProductEnv(): bool
    {
        $nonProductEnvs = [
            'local',
            'dev',
            'development',
            'integration',
            'test',
            'testing',
            'QC',
            'staging',
            'stage',
            'pre-production',
            'demo'
        ];

        $env = App::environment();

        return in_array($env, $nonProductEnvs);
    }

    /**
     * Checks for the presence of a phrase in the default language.
     * @throws ValidationException
     */
    public static function havePhraseOnDefaultLang(string $json, $fieldName): bool
    {
        $langCode = Settings::where('code', 'DEFAULT_LANGUAGE')->first()->toArray()['value'];

        $decoded = json_decode($json, true);
        $jsonError = json_last_error();
        
        // Проверяем, что JSON валиден и является массивом
        if ($jsonError !== JSON_ERROR_NONE || !is_array($decoded)) {
            $errorMessage = 'Поле \'' . $fieldName . '\' имеет невалидный JSON формат';
            if ($jsonError !== JSON_ERROR_NONE) {
                $errorMessage .= ' (ошибка: ' . json_last_error_msg() . ')';
            }
            throw ValidationException::withMessages([$fieldName => $errorMessage]);
        }

        // Проверяем наличие значения для языка по умолчанию
        //if (!isset($decoded[$langCode]) || trim($decoded[$langCode]) === '') {
        //    throw ValidationException::withMessages(
        //        [$fieldName => 'Поле \'' . $fieldName . '\' обязательно для заполнения на языке по умолчанию (' . $langCode . ')']
        //    );
        //}

        return true;
    }

    // Returns client city id
    public static function getUserCityId(): ?int
    {
        $userCity = Helper::getUserCityName();

        $cityName = '"ru":"' . $userCity . '"';
        $city = City::where('name', 'LIKE', '%' . $cityName . '%')->first(['id']);

        return ($city) ? $city['id'] : null;
    }

    // Returns client city name
    public static function getUserCityName(): string
    {
        return isset($_COOKIE['user_city']) ?
            html_entity_decode(preg_replace('/%u([0-9A-F]+)/', '&#x$1;', $_COOKIE['user_city']), ENT_COMPAT, 'UTF-8') :
            (string) config('storefront.default_city', '');
    }

    // Получает номер телефона магазина конкретного города
    public static function getShopPhone(bool $withoutFormatted = false): string
    {
        $cityId = \Helper::getUserCityId();

        $shop = Shop::where('city_id', $cityId)->first();

        if ($shop) {
            if (!$withoutFormatted && preg_match("/^(\d)(\d{3})(\d{3})(\d{2})(\d{2})$/", $shop->phone, $matches)) {
                $phoneFormatted = '+' . $matches[1] . '(' . $matches[2] . ') ' . $matches[3] . '-' . $matches[4] . '-' . $matches[5];
                return $phoneFormatted;
            }

            return '+' . $shop->phone;
        }

        return (string) config('storefront.phone_number', '');
    }

    /**
     * Base URL of the storefront with a trailing slash, including the optional path prefix.
     * See config/storefront.php.
     */
    public static function storefrontUrl(): string
    {
        $url = trim((string) config('storefront.url', ''));

        if ($url === '') {
            $host = $_SERVER['HTTP_HOST'] ?? '';
            $url  = $host !== '' ? 'https://' . $host : (string) config('app.url', '');
        }

        $prefix = trim((string) config('storefront.prefix_url', ''), '/');

        return rtrim($url, '/') . '/' . ($prefix !== '' ? $prefix . '/' : '');
    }

    /**
     * Absolute URL of a product page on the storefront.
     */
    public static function productUrl(string $productName, int $productId): string
    {
        $pattern = (string) config('storefront.product_url_pattern', 'item/{slug}_{id}.html');

        return self::storefrontUrl() . str_replace(
            ['{slug}', '{id}'],
            [Str::slug($productName), (string) $productId],
            ltrim($pattern, '/')
        );
    }
}
