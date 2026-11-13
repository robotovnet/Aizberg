<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\Settings;
use Modules\TransportCompany\Http\Requests\CreateOrUpdatePickupPointRequest;
use Modules\TransportCompany\Repositories\PickupPointRepository;
use Illuminate\Http\Request;

/**
 * Class PickupPointController
 * @package Modules\TransportCompany\Http\Controllers
 */
class PickupPointController extends Controller
{
    /** @var PickupPointRepository An instance of the Pickup Point Repository. */
    private PickupPointRepository $pickupPointRepo;

    /** @var int|null Cached ENTITY_PER_PAGE; loaded on first use (not in constructor — avoids DB during e.g. route:list). */
    private ?int $totalPerPage = null;

    /**
     * PickupPointController constructor.
     */
    public function __construct(PickupPointRepository $pickupPointRepo)
    {
        $this->pickupPointRepo = $pickupPointRepo;
    }

    private function totalPerPage(): int
    {
        if ($this->totalPerPage === null) {
            $this->totalPerPage = (int) Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'];
        }

        return $this->totalPerPage;
    }

    /**
     * Returns all pickup points.
     */
    public function index(Request $request): JsonResponse
    {
        $addingMethod = $request->input('adding_method', 'all');
        $page         = $request->input('page', 1);
        $cityId       = $request->input('city_id', 0);
        $tcId         = $request->input('tc_id', 0);

        return JsonResponse::successful(
            $this->pickupPointRepo->all((int)$page, $this->totalPerPage(), $addingMethod, (int)$cityId, (int)$tcId)
        );
    }

    /**
     * Creates a new pickup point.
     */
    public function store(CreateOrUpdatePickupPointRequest $request): JsonResponse
    {
        $pickupPointId = $this->pickupPointRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $pickupPointId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns pickup point data by id.
     */
    public function show(PickupPointRepository $pickupPointRepo): JsonResponse
    {
        return JsonResponse::successful($pickupPointRepo->data());
    }

    /**
     * Updates pickup point data.
     */
    public function update(
        CreateOrUpdatePickupPointRequest $request,
        PickupPointRepository $pickupPointRepo
    ): JsonResponse {
        $pickupPointRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the pickup point.
     */
    public function destroy(PickupPointRepository $pickupPointRepo): JsonResponse
    {
        $pickupPointRepo->delete();

        return JsonResponse::successful();
    }

    /**
     * Returns the total number of pickup points.
     */
    public function total(PickupPointRepository $pickupPointRepo, Request $request): JsonResponse
    {
        $addingMethod = $request->input('adding_method', 'all');
        $cityId       = $request->input('city_id', 0);
        $tcId         = $request->input('tc_id', 0);

        return JsonResponse::successful([
            'total_amount'   => $pickupPointRepo->total($addingMethod, (int)$cityId, (int)$tcId),
            'total_per_page' => $this->totalPerPage(),
        ]);
    }

    /**
     * Returns a list of cities that have pickup points.
     */
    public function cities(PickupPointRepository $pickupPointRepo): JsonResponse
    {
        return JsonResponse::successful($pickupPointRepo->cities());
    }
}
