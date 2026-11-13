<?php

declare(strict_types=1);

namespace Modules\OrderOneClick\Http\Controllers;

use Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Modules\OrderOneClick\Http\Requests\GetOrdersRequest;
use Modules\OrderOneClick\Http\Requests\SetCatalogSectionsRequest;
use Modules\OrderOneClick\Http\Requests\SetSubscribersRequest;
use Modules\OrderOneClick\Repositories\OrderOneClickRepository;

/**
 * Class OrderOneClickController
 * @package Modules\OrderOneClick\Http\Controllers
 */
class OrderOneClickController extends Controller
{
    /**
     * Returns data of all one click orders.
     */
    public function index(GetOrdersRequest $request, OrderOneClickRepository $repo)
    {
        $onlyCompleted = Request::get('only_completed', 'ALL');
        $page          = Request::get('page', 1);
        $dateFrom      = Request::get('date_from', date('d/m/Y'));
        $dateTo        = Request::get('date_to', date('d/m/Y'));
        $onlyCount     = Request::get('onlyCount', null);

        return JsonResponse::successful($repo->all($onlyCompleted, $dateFrom, $dateTo, (int)$page, $onlyCount));
    }

    /**
     * Get a list of email addresses to which information about new orders is sent in one click.
     */
    public function getSubscribers(OrderOneClickRepository $repo)
    {
        return JsonResponse::successful($repo->getSubscribers());
    }

    /**
     * Get catalog sections with the activated ability to place orders in one click.
     */
    public function getCatalogSections(OrderOneClickRepository $repo)
    {
        return JsonResponse::successful($repo->getCatalogSections());
    }

    /**
     * Set catalog sections with support for placing orders in one click.
     */
    public function setCatalogSections(SetCatalogSectionsRequest $request, OrderOneClickRepository $repo): JsonResponse
    {
        $repo->setCatalogSections($request->input('catalog_sections'));

        return JsonResponse::successful();
    }

    /**
     *  Set a list of recipients of information about new orders in one click.
     */
    public function setSubscribers(SetSubscribersRequest $request, OrderOneClickRepository $repo): JsonResponse
    {
        $repo->setSubscribers($request->input('subscribers'));

        return JsonResponse::successful();
    }

    /**
     * Allows to mark an order as completed.
     */
    public function completed(\Illuminate\Http\Request $request, OrderOneClickRepository $repo): JsonResponse
    {
        $isCompleted = ($request->input('is_completed') == 'Y') ? 'Y' : 'N';

        $repo->completed($request->input('order_id'), $isCompleted);

        return JsonResponse::successful();
    }
}
