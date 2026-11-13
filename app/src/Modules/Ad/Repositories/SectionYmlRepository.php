<?php

declare(strict_types=1);

namespace Modules\Ad\Repositories;

/**
 * Interface SectionYmlRepository
 * @package Modules\Ad\Repositories
 */
interface SectionYmlRepository
{
    /**
     * Updates sections list for yml.
     */
    public function save(array $sections): bool;

    /**
     * Returns sections list for yml.
     */
    public function all(): array;
}
