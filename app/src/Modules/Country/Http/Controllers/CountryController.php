<?php

declare(strict_types=1);

namespace Modules\Country\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Country\Http\Requests\CreateOrUpdateCountryRequest;
use Modules\Country\Repositories\CountryRepository;

/**
 * Class CountryController
 * @package Modules\Country\Http\Controllers
 */
class CountryController extends Controller
{
    /** @var CountryRepository An instance of the Country Repository. */
    private CountryRepository $countryRepo;

    /**
     * CountryController constructor.
     */
    public function __construct(CountryRepository $countryRepo)
    {
        $this->countryRepo = $countryRepo;
    }

    /**
     * Returns all countries.
     */
    public function index(Request $request): JsonResponse
    {
        $withcities = $request->input('withcities', 'N');
        $search = $request->input('search', null);

        return JsonResponse::successful($this->countryRepo->all($withcities, $search));
    }

    /**
     * Creates a new country.
     */
    public function store(CreateOrUpdateCountryRequest $request): JsonResponse
    {
        $request->merge(['name' => Str::getNamesJson($request->names)]);
        $countryId = $this->countryRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $countryId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns country data by id.
     */
    public function show(CountryRepository $countryRepo): JsonResponse
    {
        return JsonResponse::successful($countryRepo->data());
    }

    /**
     * Updates country data.
     */
    public function update(CreateOrUpdateCountryRequest $request, CountryRepository $countryRepo): JsonResponse
    {
        $request->merge(['name' => Str::getNamesJson($request->names)]);
        $countryRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the country.
     */
    public function destroy(CountryRepository $countryRepo): JsonResponse
    {
        $countryRepo->delete();

        return JsonResponse::successful();
    }
}
