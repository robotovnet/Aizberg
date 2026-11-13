<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Catalog\Http\Requests\CreateFilterRequest;
use Modules\Catalog\Http\Requests\UpdateFilterRequest;
use Modules\Catalog\Http\Requests\UpdateManyFilterRequest;
use Modules\Catalog\Repositories\FilterRepository;
use Illuminate\Http\Request;
use Modules\Settings\Entities\Settings;

/**
 * Class FilterController
 * @package Modules\Catalog\Http\Controllers
 */
class FilterController extends Controller
{
    /** @var FilterRepository An instance of the Filter Repository. */
    private FilterRepository $filterRepo;

    /**
     * FilterController constructor.
     */
    public function __construct(FilterRepository $filterRepo)
    {
        $this->filterRepo = $filterRepo;
    }

    /**
     * Returns all filters.
     */
    public function index(Request $request): JsonResponse
    {
        return JsonResponse::successful($this->filterRepo->all());
    }

    /**
     * Creates a new filter.
     */
    public function store(CreateFilterRequest $request): JsonResponse
    {
        $filterId = $this->filterRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $filterId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns filter data by id.
     */
    public function show(FilterRepository $filterRepo): JsonResponse
    {
        return JsonResponse::successful($filterRepo->data());
    }

    /**
     * Updates filter data.
     */
    public function update(
        UpdateFilterRequest $request,
        FilterRepository $filterRepo
    ): JsonResponse {
        $filterRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Updates many filter data.
     */
    public function updateMany(
        UpdateManyFilterRequest $request,
        FilterRepository $filterRepo
    ): JsonResponse {
        $filterRepo->updateMany($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the filter.
     */
    public function destroy(FilterRepository $filterRepo): JsonResponse
    {
        $filterRepo->delete();

        return JsonResponse::successful();
    }

    /**
     * Returns the number of filters for each product group.
     */
    public function totalByProductGroup(FilterRepository $filterRepo): JsonResponse
    {
        return JsonResponse::successful($filterRepo->totalByProductGroup());
    }

    /**
     * Deletes all filters by product group.
     */
    public function deleteAllFiltersByGroup(FilterRepository $filterRepo, $groupId): JsonResponse
    {
        $filterRepo->deleteAllFiltersByGroup((int)$groupId);

        return JsonResponse::successful();
    }
}
