<?php

declare(strict_types=1);

namespace Modules\Timezone\Repositories;

/**
 * Interface TimezoneRepository
 * @package Modules\Timezone\Repositories
 */
interface TimezoneRepository
{
    /**
     * Returns id timezone by name.
     *
     * @param  string  $name Timezone name
     */
    public function getIdByName(string $name): int;
}
