<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Enums;

/**
 * Допустимые типы лицензий.
 */
final class MarketplaceLicenseType
{
    public const MIT = 'MIT';
    public const OTHER = 'Other';
    public const GPLV2 = 'GPLv2';
    public const APACHE_2 = 'Apache 2.0';
    public const GPLV3 = 'GPLv3';
    public const BSD_3 = 'BSD 3-Clause';
    public const UNLICENSE = 'Unlicense';
    public const BSD_2 = 'BSD 2-Clause';
    public const LGPLV3 = 'LGPLv3';
    public const AGPLV3 = 'AGPLv3';

    /** @return string[] */
    public static function all(): array
    {
        return [
            self::MIT,
            self::OTHER,
            self::GPLV2,
            self::APACHE_2,
            self::GPLV3,
            self::BSD_3,
            self::UNLICENSE,
            self::BSD_2,
            self::LGPLV3,
            self::AGPLV3,
        ];
    }
}
