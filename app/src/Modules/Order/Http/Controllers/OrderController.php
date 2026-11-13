<?php

namespace Modules\Order\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Order\Http\Requests\ChangePaymentMethodRequest;
use Modules\Order\Http\Requests\ChangeStatusRequest;
use Modules\Order\Http\Requests\ChangeTracknumberRequest;
use Modules\Order\Http\Requests\CreateOrderRequest;
use Modules\Order\Repositories\OrderRepository;

/**
 * Class OrderController
 * @package Modules\Order\Http\Controllers
 */
class OrderController extends Controller
{
    /** @var OrderRepository An instance of the Product Repository. */
    private OrderRepository $orderRepo;

    /**
     * ProductController constructor.
     */
    public function __construct(OrderRepository $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    /**
     * Returns data of all orders.
     */
    public function all(Request $request)
    {
        $search     = $request->input('search', "");
        $page       = $request->input('page', 1);
        $filters    = $request->input('filters', []);
        $onlyCount  = $request->input('onlyCount', null);

        return JsonResponse::successful(
            $this->orderRepo->all((int)$page, $filters, (string)$search, $onlyCount)
        );
    }

    /**
     * Returns order details.
     */
    public function details(string $orderId)
    {
        return JsonResponse::successful($this->orderRepo->details((int)$orderId));
    }

    /**
     * Thanks for the feedback.
     */
    public function thanksFeedback(string $orderId)
    {
        $this->orderRepo->thanksFeedback((int)$orderId);

        return JsonResponse::successful([
            'message' => 'success',
        ]);
    }

    /**
     * Changes the order status.
     */
    public function changeStatus(ChangeStatusRequest $request, string $orderId)
    {
        $status = $request->input('status');
        $this->orderRepo->changeStatus((int)$orderId, $status);

        return JsonResponse::successful([
            'message' => 'success',
        ]);
    }

    /**
     * Changes the order payment method.
     */
    public function changeMethodPay(ChangePaymentMethodRequest $request, string $orderId)
    {
        $paymentMethodPay = $request->input('payment_method_id');
        $this->orderRepo->changeMethodPay((int)$orderId, $paymentMethodPay);

        return JsonResponse::successful([
            'message' => 'success',
        ]);
    }

    /**
     * Sets the track number assigned to the order.
     */
    public function changeTracknumber(ChangeTracknumberRequest $request, string $orderId)
    {
        $tracknumber = $request->input('tracknumber', '');
        $this->orderRepo->changeTracknumber((int)$orderId, $tracknumber);

        return JsonResponse::successful([
            'message' => 'success',
        ]);
    }

    /**
     * Creates a new order.
     */
    public function create(CreateOrderRequest $request)
    {
        $orderId = $this->orderRepo->create($request->all());

        return JsonResponse::successful([
            'message' => 'success',
            'orderId' => $orderId,
        ]);
    }
}
