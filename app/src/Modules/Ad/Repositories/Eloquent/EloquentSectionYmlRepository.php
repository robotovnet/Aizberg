<?php

declare(strict_types=1);

namespace Modules\Ad\Repositories\Eloquent;

use Modules\Ad\Entities\SectionYml;
use Modules\Ad\Repositories\SectionYmlRepository;

/**
 * Class EloquentSectionYmlRepository
 * @package Modules\Ad\Repositories\Eloquent
 */
class EloquentSectionYmlRepository implements SectionYmlRepository
{
    /**
     * @inheritDoc
     */
    public function save(array $sections): bool
    {
        SectionYml::truncate();

        foreach ($sections as $section) {
            SectionYml::create(['section_id' => $section]);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return SectionYml::all(SectionYml::SELECT_ATTRIBUTES)->toArray();
    }
}
