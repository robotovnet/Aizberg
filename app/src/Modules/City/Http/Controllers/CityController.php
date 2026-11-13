<?php

declare(strict_types=1);

namespace Modules\City\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\City\Repositories\CityRepository;
use Modules\City\Http\Requests\CreateOrUpdateCityRequest;
use Modules\Settings\Entities\Settings;

/**
 * Class CityController
 * @package Modules\City\Http\Controllers
 */
class CityController extends Controller
{
    /** @var CityRepository An instance of the City Repository. */
    private CityRepository $cityRepo;

    /**
     * CityController constructor.
     */
    public function __construct(CityRepository $cityRepo)
    {
        $this->cityRepo = $cityRepo;
    }

    /**
     * Returns all cities.
     */
    public function getAllByCountryCode(Request $request, string $country_code): JsonResponse
    {
        $page = $request->input('page', 1);

        $cities = $this->cityRepo->getAllByCountryCode($country_code, (int)$page);

        return JsonResponse::successful($cities);
    }

    /**
     * Creates a new city.
     */
    public function store(CreateOrUpdateCityRequest $request): JsonResponse
    {
        $request->merge(['name' => Str::getNamesJson($request->names)]);
        $cityId = $this->cityRepo->create($request->all());

        return JsonResponse::successful([
            'id'      => $cityId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns all cities.
     */
    public function index(Request $request, ?string $mode = null): JsonResponse
    {
        $page = $request->input('page', null);
        $page = is_null($page) ? null : (int)$page;
        $search = $request->input('search', null);

        return JsonResponse::successful($this->cityRepo->all($page, $mode, $search));
    }

    /**
     * Returns city data by id.
     */
    public function show(CityRepository $cityRepo): JsonResponse
    {
        return JsonResponse::successful($cityRepo->data());
    }

    /**
     * Updates city data.
     */
    public function update(CreateOrUpdateCityRequest $request, CityRepository $cityRepo): JsonResponse
    {
        $request->merge(['name' => Str::getNamesJson($request->names)]);
        $cityRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the city.
     */
    public function destroy(CityRepository $cityRepo): JsonResponse
    {
        $cityRepo->delete();

        return JsonResponse::successful();
    }

    /**
     * Returns the total number of cities.
     */
    public function total(CityRepository $cityRepo, Request $request): JsonResponse
    {
        $countryCode = $request->input('country_code', '');

        return JsonResponse::successful([
            'total_amount'   => $cityRepo->total($countryCode),
            'total_per_page' => (int)Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'],
        ]);
    }
}
