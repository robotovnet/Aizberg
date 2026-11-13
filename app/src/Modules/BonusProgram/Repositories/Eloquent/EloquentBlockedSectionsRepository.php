<?php

declare(strict_types=1);

namespace Modules\BonusProgram\Repositories\Eloquent;

use Modules\BonusProgram\Entities\BlockedSection;
use Modules\BonusProgram\Repositories\BlockedSectionsRepository;

/**
 * Class EloquentBlockedSectionsRepository
 * @package Modules\Ad\Repositories\Eloquent
 */
class EloquentBlockedSectionsRepository implements BlockedSectionsRepository
{
    /**
     * @inheritDoc
     */
    public function save(array $sections): bool
    {
        BlockedSection::truncate();

        foreach ($sections as $section) {
            BlockedSection::create(['section_id' => $section]);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return BlockedSection::all(BlockedSection::SELECT_ATTRIBUTES)->toArray();
    }
}
