<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\Settings;
use Modules\TransportCompany\Http\Requests\CreateOrUpdateCourierCityRequest;
use Modules\TransportCompany\Repositories\CourierCityRepository;
use Illuminate\Http\Request;

/**
 * Class CourierCityController
 * @package Modules\TransportCompany\Http\Controllers
 */
class CourierCityController extends Controller
{
    /** @var CourierCityRepository An instance of the Courier City Repository. */
    private CourierCityRepository $courierCityRepo;

    /** @var int|null Cached ENTITY_PER_PAGE; loaded on first use (not in constructor — avoids DB during e.g. route:list). */
    private ?int $totalPerPage = null;

    /**
     * CourierCityController constructor.
     */
    public function __construct(CourierCityRepository $courierCityRepo)
    {
        $this->courierCityRepo = $courierCityRepo;
    }

    private function totalPerPage(): int
    {
        if ($this->totalPerPage === null) {
            $this->totalPerPage = (int) Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'];
        }

        return $this->totalPerPage;
    }

    /**
     * Returns all courier cities.
     */
    public function index(Request $request): JsonResponse
    {
        $addingMethod = $request->input('adding_method', 'all');
        $page         = $request->input('page', 1);
        $tcId         = $request->input('tc_id', 0);
        $search       = $request->input('search', null);

        return JsonResponse::successful(
            $this->courierCityRepo->all((int)$page, $this->totalPerPage(), $addingMethod, (int)$tcId, null, $search)
        );
    }

    /**
     * Creates a new courier city.
     */
    public function store(CreateOrUpdateCourierCityRequest $request): JsonResponse
    {
        $courierCityId = $this->courierCityRepo->create($request->all());

        return JsonResponse::successful([
            'id'      => $courierCityId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns courier city data by id.
     */
    public function show(CourierCityRepository $courierCityRepository): JsonResponse
    {
        return JsonResponse::successful($courierCityRepository->data());
    }

    /**
     * Updates courier city data.
     */
    public function update(
        CreateOrUpdateCourierCityRequest $request,
        CourierCityRepository $courierCityRepository
    ): JsonResponse {
        $courierCityRepository->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the courier city.
     */
    public function destroy(CourierCityRepository $courierCityRepository): JsonResponse
    {
        $courierCityRepository->delete();

        return JsonResponse::successful();
    }

    /**
     * Returns the total number of courier cities.
     */
    public function total(CourierCityRepository $courierCityRepository, Request $request): JsonResponse
    {
        $addingMethod = $request->input('adding_method', 'all');
        $tcId         = $request->input('tc_id', 0);

        return JsonResponse::successful([
            'total_amount'   => $courierCityRepository->total($addingMethod, (int)$tcId),
            'total_per_page' => $this->totalPerPage(),
        ]);
    }
}
