<?php

declare(strict_types=1);

namespace Modules\Country\Services;

use Modules\Country\Repositories\CountryRepository;

/**
 * Class CountryAttachService
 * @package Modules\Country\Services
 */
class CountryAttachService
{
    /** @var CountryRepository An instance of the Country Repository. */
    private CountryRepository $countryRepository;

    /**
     * CountryAttachService constructor.
     */
    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * Returns id country.
     */
    public function getEntityId(array $data): int
    {
        return $this->countryRepository->getIdByCode($data['country_code']);
    }
}
