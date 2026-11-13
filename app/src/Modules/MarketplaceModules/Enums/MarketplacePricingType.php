<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Enums;

final class MarketplacePricingType
{
    /** Бесплатный (Free). */
    public const FREE = 'free';

    /** Платный (Free Trial). */
    public const FREE_TRIAL = 'free_trial';

    /** Платный модуль. */
    public const PAID = 'paid';

    /** @return string[] */
    public static function all(): array
    {
        return [self::FREE, self::FREE_TRIAL, self::PAID];
    }
}
