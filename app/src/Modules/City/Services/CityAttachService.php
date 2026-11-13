<?php

declare(strict_types=1);

namespace Modules\City\Services;

/**
 * Class CityAttachService.
 * @package Modules\City\Services
 */
class CityAttachService
{
    /**
     * Returns id city.
     */
    public function getEntityId(array $data): int
    {
        return intval($data['city_id']);
    }
}
