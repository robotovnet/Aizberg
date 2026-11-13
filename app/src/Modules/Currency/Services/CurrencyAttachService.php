<?php

declare(strict_types=1);

namespace Modules\Currency\Services;

use Modules\Currency\Repositories\CurrencyRepository;

/**
 * Class CurrencyAttachService
 * @package Modules\Currency\Services
 */
class CurrencyAttachService
{
    /** @var CurrencyRepository An instance of the Currency Repository. */
    private CurrencyRepository $currencyRepository;

    /**
     * CurrencyAttachService constructor.
     */
    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * Returns id currency.
     */
    public function getEntityId(array $data): int
    {
        return $this->currencyRepository->getIdByCode($data['country_code']);
    }
}
