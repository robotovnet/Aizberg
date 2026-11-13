<?php

declare(strict_types=1);

namespace Modules\Client\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Client\Entities\Client;
use Modules\Country\Repositories\CountryRepository;
use Modules\Timezone\Repositories\TimezoneRepository;

class ClientProfileService
{
    private CountryRepository $countryRepository;

    private TimezoneRepository $timezoneRepository;

    public function __construct(
        CountryRepository $countryRepository,
        TimezoneRepository $timezoneRepository
    ) {
        $this->countryRepository = $countryRepository;
        $this->timezoneRepository = $timezoneRepository;
    }

    /**
     * Partially updates profile fields and related entities.
     *
     * @param array<string, mixed> $attributes
     */
    public function update(Client $client, array $attributes): void
    {
        DB::transaction(function () use ($client, $attributes): void {
            $client->fill(Arr::only($attributes, [
                'first_name',
                'last_name',
                'email',
                'phone',
            ]));

            if (array_key_exists('country_code', $attributes)) {
                $client->country()->sync([
                    $this->countryRepository->getIdByCode($attributes['country_code']),
                ]);
            }

            if (array_key_exists('city_id', $attributes)) {
                $client->city()->sync([(int)$attributes['city_id']]);
            }

            if (array_key_exists('timezone', $attributes)) {
                $client->timezone()->sync([
                    $this->timezoneRepository->getIdByName($attributes['timezone']),
                ]);
            }

            $client->save();
        });
    }
}
