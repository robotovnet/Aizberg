<?php

declare(strict_types=1);

namespace Modules\Timezone\Services;

use Modules\Timezone\Repositories\TimezoneRepository;

/**
 * Class TimezoneAttachService
 * @package Modules\Timezone\Services
 */
class TimezoneAttachService
{
    /** @var TimezoneRepository An instance of the Timezone Repository. */
    private TimezoneRepository $timezoneRepository;

    /**
     * TimezoneAttachService constructor.
     */
    public function __construct(TimezoneRepository $timezoneRepository)
    {
        $this->timezoneRepository = $timezoneRepository;
    }

    /**
     * Returns id timezone.
     */
    public function getEntityId(array $data): int
    {
        return $this->timezoneRepository->getIdByName($data['timezone']);
    }
}
