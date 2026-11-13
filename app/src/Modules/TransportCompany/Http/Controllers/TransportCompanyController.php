<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\TransportCompany\Http\Requests\CreateOrUpdateTransportCompanyRequest;
use Modules\TransportCompany\Http\Requests\IconUploadRequest;
use Modules\TransportCompany\Http\Requests\OrderTransportCompanyRequest;
use Modules\TransportCompany\Repositories\TransportCompanyRepository;
use Modules\TransportCompany\Services\IconService;

/**
 * Class TransportCompanyController
 * @package Modules\TransportCompany\Http\Controllers
 */
class TransportCompanyController extends Controller
{
    /** @var TransportCompanyRepository An instance of the Transport Company Repository. */
    private TransportCompanyRepository $transportCompanyRepo;

    /**
     * TransportCompanyController constructor.
     */
    public function __construct(TransportCompanyRepository $transportCompanyRepo)
    {
        $this->transportCompanyRepo = $transportCompanyRepo;
    }

    /**
     * Creates a new transport company.
     */
    public function store(CreateOrUpdateTransportCompanyRequest $request): JsonResponse
    {
        $request->merge(['name'               => Str::getNamesJson($request->names)]);
        $request->merge(['additional_comment' => Str::getNamesJson($request->additional_comments)]);

        $this->transportCompanyRepo->create($request->all());

        return JsonResponse::successful([
            'message' => 'success',
            'transport_company_id' => $this->transportCompanyRepo->getId()
          ]);
    }

    /**
     * Returns all transport companies.
     */
    public function index(): JsonResponse
    {
        return JsonResponse::successful($this->transportCompanyRepo->all());
    }

    /**
     * Returns transport company data by id.
     */
    public function show(TransportCompanyRepository $transportCompanyRepo): JsonResponse
    {
        return JsonResponse::successful($transportCompanyRepo->data());
    }

    /**
     * Updates transport company data.
     */
    public function update(
        CreateOrUpdateTransportCompanyRequest $request,
        TransportCompanyRepository $transportCompanyRepo
    ): JsonResponse {
        $request->merge(['name'               => Str::getNamesJson($request->names)]);
        $request->merge(['additional_comment' => Str::getNamesJson($request->additional_comments)]);

        $transportCompanyRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the transport company.
     */
    public function destroy(TransportCompanyRepository $transportCompanyRepo): JsonResponse
    {
        $transportCompanyRepo->delete();

        return JsonResponse::successful();
    }

    /**
     * Uploading transport company icon.
     */
    public function iconUpload(
        IconUploadRequest $request,
        TransportCompanyRepository $transportCompanyRepo,
        IconService $icon
    ): JsonResponse {

        $icon->save();
        $iconPath = $icon->getPathToFile();

        $transportCompanyRepo->update([ 'icon_path' => Str::of($iconPath)->basename()]);

        return JsonResponse::successful([ 'icon_path' => $iconPath ]);
    }

    /**
     * Deletes transport company icon.
     */
    public function iconDelete(TransportCompanyRepository $transportCompanyRepo): JsonResponse
    {
        $transportCompanyRepo->update([ 'icon_path' => null]);

        return JsonResponse::successful();
    }

    /**
     * Sets the order of transport companies.
     */
    public function order(
        OrderTransportCompanyRequest $request,
        TransportCompanyRepository $transportCompanyRepo
    ): JsonResponse {
        $transportCompanyRepo->order($request->orders);

        return JsonResponse::successful();
    }
}

