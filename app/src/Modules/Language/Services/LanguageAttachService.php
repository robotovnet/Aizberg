<?php

declare(strict_types=1);

namespace Modules\Language\Services;

use Modules\Country\Repositories\CountryRepository;
use Modules\Language\Repositories\LanguageRepository;

/**
 * Class LanguageAttachService
 * @package Modules\Language\Services
 */
class LanguageAttachService
{
    /** @var CountryRepository An instance of the Country Repository. */
    private CountryRepository $countryRepository;

    /** @var LanguageRepository An instance of the Language Repository. */
    private LanguageRepository $languageRepository;

    /** @var LanguageService An instance of the Language Service. */
    private LanguageService $languageService;

    /**
     * LanguageAttachService constructor.
     */
    public function __construct(
        CountryRepository $countryRepository,
        LanguageRepository $languageRepository,
        LanguageService $languageService
    ) {
        $this->countryRepository  = $countryRepository;
        $this->languageRepository = $languageRepository;
        $this->languageService    = $languageService;
    }

    /**
     * Returns id language.
     */
    public function getEntityId(array $data): int
    {
        $langCode = $this->countryRepository->getLanguageByCode($data['country_code']);

        // Setting the client's active language
        $this->languageService->makeActive($data['country_code']);

        return $this->languageRepository->getIdByCode($langCode);
    }
}
