<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories;

/**
 * Interface BlockedSectionsPMRepository
 * @package Modules\TransportCompany\Repositories
 */
interface BlockedSectionsPMRepository
{
    /**
     * Saves catalog sections excluded from the payment method.
     */
    public function save(int $pmId, array $sections): bool;

    /**
     * Returns catalog sections excluded from the payment method.
     */
    public function all(int $pmId): array;
}
