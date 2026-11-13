<?php

declare(strict_types=1);

namespace Modules\Mask\Repositories;

/**
 * Interface MaskRepository
 * @package Modules\Mask\Repositories
 */
interface MaskRepository
{
    /**
     * Returns all masks by a specific type.
     *
     * @param  string  $type Masks type
     */
    public function getAll(string $type): array;

    /**
     * Returns all masks by a specific type and specific country code.
     *
     * @param  string  $type Masks type
     * @param  string  $country_code Country code
     */
    public function getByCode(string $type, string $country_code): array;
}
