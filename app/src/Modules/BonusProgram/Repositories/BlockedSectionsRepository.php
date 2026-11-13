<?php

declare(strict_types=1);

namespace Modules\BonusProgram\Repositories;

/**
 * Interface BlockedSectionsRepository
 * @package Modules\BonusProgram\Repositories
 */
interface BlockedSectionsRepository
{
    /**
     * Saves catalog sections excluded from the bonus program.
     */
    public function save(array $sections): bool;

    /**
     * Returns catalog sections excluded from the bonus program.
     */
    public function all(): array;
}
