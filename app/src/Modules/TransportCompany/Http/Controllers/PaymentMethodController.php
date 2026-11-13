<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\TransportCompany\Http\Requests\CreateOrUpdateBlockedSectionsPMRequest;
use Modules\TransportCompany\Http\Requests\CreateOrUpdatePaymentMethodRequest;
use Modules\TransportCompany\Http\Requests\OrderPaymentMethodsRequest;
use Modules\TransportCompany\Repositories\BlockedSectionsPMRepository;
use Modules\TransportCompany\Repositories\PaymentMethodRepository;

/**
 * Class PaymentMethodController
 * @package Modules\TransportCompany\Http\Controllers
 */
class PaymentMethodController extends Controller
{
    /** @var PaymentMethodRepository An instance of the Payment Method Repository. */
    private PaymentMethodRepository $paymentMethodRepo;

    /**
     * PostamatController constructor.
     */
    public function __construct(PaymentMethodRepository $paymentMethodRepo)
    {
        $this->paymentMethodRepo = $paymentMethodRepo;
    }

    /**
     * Returns all  payment methods.
     */
    public function index(): JsonResponse
    {
        return JsonResponse::successful($this->paymentMethodRepo->all());
    }

    /**
     * Creates a new payment method.
     */
    public function store(CreateOrUpdatePaymentMethodRequest $request): JsonResponse
    {
        $request->merge([
            'name'               => Str::getNamesJson($request->names),
            'additional_comment' => Str::getNamesJson($request->additional_comments)
        ]);

        $pmId = $this->paymentMethodRepo->create($request->all());

        return JsonResponse::successful([
            'message' => 'success',
            'payment_method_id' => $pmId,
        ]);
    }

    /**
     * Returns payment method data by id.
     */
    public function show(PaymentMethodRepository $paymentMethodRepo): JsonResponse
    {
        return JsonResponse::successful($paymentMethodRepo->data());
    }

    /**
     * Updates payment method data.
     */
    public function update(
        CreateOrUpdatePaymentMethodRequest $request,
        PaymentMethodRepository $paymentMethodRepo
    ): JsonResponse {
        $request->merge([
            'name'               => Str::getNamesJson($request->names),
            'additional_comment' => Str::getNamesJson($request->additional_comments)
        ]);
        $paymentMethodRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the payment method.
     */
    public function destroy(PaymentMethodRepository $paymentMethodRepo): JsonResponse
    {
        $paymentMethodRepo->delete();

        return JsonResponse::successful();
    }

    /**
     * Sets the order of payment methods.
     */
    public function order(
        OrderPaymentMethodsRequest $request,
        PaymentMethodRepository $paymentMethodRepo
    ): JsonResponse {
        $paymentMethodRepo->order($request->orders);

        return JsonResponse::successful();
    }

    /**
     * Sets catalog sections excluded from the payment method.
     */
    public function blockingSections(
        CreateOrUpdateBlockedSectionsPMRequest $request,
        BlockedSectionsPMRepository $repo,
        PaymentMethodRepository $paymentMethod
    ): JsonResponse {
        $repo->save($paymentMethod->getModelId(), $request->input('sections', []));

        return JsonResponse::successful();
    }

    /**
     * Returns catalog sections excluded from the payment method.
     */
    public function getBlockedSections(
        BlockedSectionsPMRepository $repo,
        PaymentMethodRepository $paymentMethod
    ): JsonResponse {
        return JsonResponse::successful($repo->all($paymentMethod->getModelId()));
    }
}
