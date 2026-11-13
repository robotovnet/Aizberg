<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Enums;

final class MarketplaceRatingValue
{
    public const MIN = 1;
    public const MAX = 5;

    /** @return int[] */
    public static function all(): array
    {
        return range(self::MIN, self::MAX);
    }
}
