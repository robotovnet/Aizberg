<?php

declare(strict_types=1);

namespace Modules\ServiceCenter\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\Settings;
use Modules\ServiceCenter\Http\Requests\CreateOrUpdateServiceCenterRequest;
use Modules\ServiceCenter\Repositories\ServiceCenterRepository;
use Illuminate\Http\Request;

/**
 * Class ServiceCenterController
 * @package Modules\ServiceCenter\Http\Controllers
 */
class ServiceCenterController extends Controller
{
    /** @var ServiceCenterRepository An instance of the Service Center Repository. */
    private ServiceCenterRepository $serviceCenterRepo;

    /** @var int|null Cached ENTITY_PER_PAGE; loaded on first use (not in constructor — avoids DB during e.g. route:list). */
    private ?int $totalPerPage = null;

    /**
     * ServiceCenterController constructor.
     */
    public function __construct(ServiceCenterRepository $serviceCenterRepo)
    {
        $this->serviceCenterRepo = $serviceCenterRepo;
    }

    private function totalPerPage(): int
    {
        if ($this->totalPerPage === null) {
            $this->totalPerPage = (int) Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'];
        }

        return $this->totalPerPage;
    }

    /**
     * Returns all service centers.
     */
    public function index(Request $request): JsonResponse
    {
        $page         = $request->input('page', 1);
        $cityId       = $request->input('city_id', 0);

        return JsonResponse::successful(
            $this->serviceCenterRepo->all((int)$page, $this->totalPerPage(), (int)$cityId)
        );
    }

    /**
     * Creates a new service center.
     */
    public function store(CreateOrUpdateServiceCenterRequest $request): JsonResponse
    {
        $serviceCenterId = $this->serviceCenterRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $serviceCenterId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns service center data by id.
     */
    public function show(ServiceCenterRepository $serviceCenterRepo): JsonResponse
    {
        return JsonResponse::successful($serviceCenterRepo->data());
    }

    /**
     * Updates service center data.
     */
    public function update(
        CreateOrUpdateServiceCenterRequest $request,
        ServiceCenterRepository $serviceCenterRepo
    ): JsonResponse {
        $serviceCenterRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the service center.
     */
    public function destroy(ServiceCenterRepository $serviceCenterRepo): JsonResponse
    {
        $serviceCenterRepo->delete();

        return JsonResponse::successful();
    }

    /**
     * Returns the total number of service centers.
     */
    public function total(ServiceCenterRepository $serviceCenterRepo, Request $request): JsonResponse
    {
        $cityId = $request->input('city_id', 0);

        return JsonResponse::successful([
            'total_amount'   => $serviceCenterRepo->total((int)$cityId),
            'total_per_page' => $this->totalPerPage(),
        ]);
    }

    /**
     * Returns a list of cities that have service centers.
     */
    public function cities(ServiceCenterRepository $serviceCenterRepo): JsonResponse
    {
        return JsonResponse::successful($serviceCenterRepo->cities());
    }
}
