<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Services;

use Modules\MarketplaceModules\Entities\MarketplaceModule;
use Modules\MarketplaceModules\Entities\MarketplaceModuleRating;
/**
 * Пересчёт итоговой оценки модуля по пятибалльной шкале.
 */
class MarketplaceRatingAggregatorService
{
    public function recalculateForModule(int $marketplaceModuleId): void
    {
        $rows = MarketplaceModuleRating::query()
            ->where('marketplace_module_id', $marketplaceModuleId)
            ->get(['value']);

        $count = $rows->count();
        if ($count === 0) {
            MarketplaceModule::query()
                ->whereKey($marketplaceModuleId)
                ->update([
                    'ratings_count' => 0,
                    'aggregate_rating' => null,
                ]);

            return;
        }

        $avg = round($rows->avg(static fn (MarketplaceModuleRating $rating) => (int)$rating->value), 2);

        MarketplaceModule::query()
            ->whereKey($marketplaceModuleId)
            ->update([
                'ratings_count' => $count,
                'aggregate_rating' => $avg,
            ]);
    }
}
