<?php

declare(strict_types=1);

namespace Modules\Ad\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Ad\Http\Requests\CreateOrUpdateSectionYmlRequest;
use Modules\Ad\Http\Requests\CreateOrUpdateYmlRequest;
use Modules\Ad\Repositories\SectionYmlRepository;
use Modules\Ad\Repositories\YmlRepository;

/**
 * Class YmlController
 * @package Modules\Ad\Http\Controllers
 */
class YmlController extends Controller
{
    /** @var YmlRepository An instance of the Yml Repository. */
    private YmlRepository $ymlRepo;

    /**
     * YmlController constructor.
     */
    public function __construct(YmlRepository $ymlRepo)
    {
        $this->ymlRepo = $ymlRepo;
    }

    /**
     * Returns all ymls.
     */
    public function index(): JsonResponse
    {
        return JsonResponse::successful($this->ymlRepo->all());
    }

    /**
     * Creates a new yml.
     */
    public function store(CreateOrUpdateYmlRequest $request): JsonResponse
    {
        $ymlId = $this->ymlRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $ymlId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns yml by id.
     */
    public function show(YmlRepository $ymlRepo): JsonResponse
    {
        return JsonResponse::successful($ymlRepo->data());
    }

    /**
     * Updates yml.
     */
    public function update(
        CreateOrUpdateYmlRequest $request,
        YmlRepository $ymlRepo
    ): JsonResponse {
        $ymlRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the yml.
     */
    public function destroy(YmlRepository $ymlRepo): JsonResponse
    {
        $ymlRepo->delete();

        return JsonResponse::successful();
    }

    /**
     * Sections to upload to YML.
     */
    public function sections(
        CreateOrUpdateSectionYmlRequest $request,
        SectionYmlRepository $sectionYmlRepo
    ): JsonResponse {
        $sectionYmlRepo->save($request->input('sections', []));

        return JsonResponse::successful();
    }

    /**
     * Returns sections list for yml.
     */
    public function sectionsAll(SectionYmlRepository $sectionYmlRepo): JsonResponse
    {
        return JsonResponse::successful($sectionYmlRepo->all());
    }
}

