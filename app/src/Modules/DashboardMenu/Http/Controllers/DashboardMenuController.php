<?php

declare(strict_types=1);

namespace Modules\DashboardMenu\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\DashboardMenu\Repositories\DashboardMenuRepository;

/**
 * Class DashboardMenuController
 * @package Modules\DashboardMenu\Http\Controllers
 */
class DashboardMenuController extends Controller
{
    /** @var DashboardMenuRepository An instance of the Dashboard Menu Repository. */
    private DashboardMenuRepository $dashboardMenuRepo;

    /**
     * CityController constructor.
     */
    public function __construct(DashboardMenuRepository $dashboardMenuRepo)
    {
        $this->dashboardMenuRepo = $dashboardMenuRepo;
    }

    /**
     * Returns all menu sections and items.
     */
    public function index(): JsonResponse
    {
        return JsonResponse::successful($this->dashboardMenuRepo->all());
    }
}
