<?php

namespace Modules\Order\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Order\Services\ReportsService;

class ReportsController extends Controller
{
    private ReportsService $reportsService;

    public function __construct(ReportsService $service)
    {
        $this->reportsService = $service;
    }

    public function productsInOrdersReport(string $fromDate, string $toDate)
    {
        return $this->reportsService->productsInOrdersReport($fromDate, $toDate);
    }
}
