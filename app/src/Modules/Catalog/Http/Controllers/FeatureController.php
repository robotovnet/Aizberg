<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Catalog\Http\Requests\CreateOrUpdateFeatureRequest;
use Modules\Catalog\Repositories\FeatureRepository;

/**
 * Class FeatureController
 * @package Modules\Catalog\Http\Controllers
 */
class FeatureController extends Controller
{
    /** @var FeatureRepository An instance of the Feature Repository. */
    private FeatureRepository $featureRepo;

    /**
     * FeatureController constructor.
     */
    public function __construct(FeatureRepository $featureRepo)
    {
        $this->featureRepo = $featureRepo;
    }

    /**
     * Returns all features.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search', null);

        return JsonResponse::successful($this->featureRepo->all($search));
    }

    /**
     * Creates a new feature.
     */
    public function store(CreateOrUpdateFeatureRequest $request): JsonResponse
    {
        $request->merge([
            'name' => Str::getNamesJson($request->names),
            'unit' => Str::getNamesJson($request->units),
        ]);

        $featureId = $this->featureRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $featureId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns feature data by id.
     */
    public function show(FeatureRepository $featureRepo): JsonResponse
    {
        return JsonResponse::successful($featureRepo->data());
    }

    /**
     * Updates feature data.
     */
    public function update(
        CreateOrUpdateFeatureRequest $request,
        FeatureRepository $featureRepo
    ): JsonResponse {
        $request->merge([
            'name' => Str::getNamesJson($request->names),
            'unit' => Str::getNamesJson($request->units),
        ]);

        $featureRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the feature.
     */
    public function destroy(FeatureRepository $featureRepo): JsonResponse
    {
        $featureRepo->delete();

        return JsonResponse::successful();
    }

    /**
     * Returns all unique values of the feature.
     */
    public function getValues(FeatureRepository $featureRepo): JsonResponse
    {
        return JsonResponse::successful($featureRepo->getValues());
    }

    /**
     * Migrates feature values from one feature to another.
     */
    public function migrateValues(Request $request, FeatureRepository $featureRepo): JsonResponse
    {
        return JsonResponse::successful($featureRepo->migrateValues(
            (int) $request->source_feature_id,
            (int) $request->target_feature_id,
            $request->current_value ?? null,
            $request->new_value ?? null
        ));
    }

    /**
     * Returns products by feature value.
     */
    public function getProductsByFeatureValue(Request $request, FeatureRepository $featureRepo): JsonResponse
    {
        $featureId = (int) $featureRepo->data()['id'];

        return JsonResponse::successful($featureRepo->getProductsByFeatureValue(
            $featureId,
            $request->value ?? ''
        ));
    }
}
