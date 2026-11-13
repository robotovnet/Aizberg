<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories\Eloquent;

use Modules\TransportCompany\Entities\PMBlockedSection;
use Modules\TransportCompany\Repositories\BlockedSectionsPMRepository;

/**
 * Class EloquentBlockedSectionsPMRepository
 * @package Modules\TransportCompany\Repositories\Eloquent
 */
class EloquentBlockedSectionsPMRepository implements BlockedSectionsPMRepository
{
    /**
     * @inheritDoc
     */
    public function save(int $pmId, array $sections): bool
    {
        PMBlockedSection::where('pm_id', $pmId)->delete();

        foreach ($sections as $section) {
            PMBlockedSection::create([
                'section_id' => $section,
                'pm_id'      => $pmId,
            ]);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function all(int $pmId): array
    {
        return PMBlockedSection::where('pm_id', $pmId)
            ->get(PMBlockedSection::SELECT_ATTRIBUTES)
            ->toArray();
    }
}
