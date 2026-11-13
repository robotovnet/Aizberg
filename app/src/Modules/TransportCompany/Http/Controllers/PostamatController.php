<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Modules\Settings\Entities\Settings;
use Modules\TransportCompany\Http\Requests\CreateOrUpdatePostamatRequest;
use Modules\TransportCompany\Repositories\PostamatRepository;
use Illuminate\Http\Request;

/**
 * Class PostamatController
 * @package Modules\TransportCompany\Http\Controllers
 */
class PostamatController extends Controller
{
    /** @var PostamatRepository An instance of the Postamat Repository. */
    private PostamatRepository $postamatRepo;

    /** @var int|null Cached ENTITY_PER_PAGE; loaded on first use (not in constructor — avoids DB during e.g. route:list). */
    private ?int $totalPerPage = null;

    /**
     * PostamatController constructor.
     */
    public function __construct(PostamatRepository $postamatRepo)
    {
        $this->postamatRepo = $postamatRepo;
    }

    private function totalPerPage(): int
    {
        if ($this->totalPerPage === null) {
            $this->totalPerPage = (int) Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'];
        }

        return $this->totalPerPage;
    }

    /**
     * Returns all postamats.
     */
    public function index(Request $request): JsonResponse
    {
        $addingMethod = $request->input('adding_method', 'all');
        $page         = $request->input('page', 1);
        $cityId       = $request->input('city_id', 0);
        $tcId         = $request->input('tc_id', 0);

        return JsonResponse::successful(
            $this->postamatRepo->all((int)$page, $this->totalPerPage(), $addingMethod, (int)$cityId, (int)$tcId)
        );
    }

    /**
     * Creates a new postamat.
     */
    public function store(CreateOrUpdatePostamatRequest $request): JsonResponse
    {
        $postamatId = $this->postamatRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $postamatId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns postamat data by id.
     */
    public function show(PostamatRepository $postamatRepo): JsonResponse
    {
        return JsonResponse::successful($postamatRepo->data());
    }

    /**
     * Updates postamat data.
     */
    public function update(
        CreateOrUpdatePostamatRequest $request,
        PostamatRepository $postamatRepo
    ): JsonResponse {
        $postamatRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the postamat.
     */
    public function destroy(PostamatRepository $postamatRepo): JsonResponse
    {
        $postamatRepo->delete();

        return JsonResponse::successful();
    }

    /**
     * Returns the total number of postamats.
     */
    public function total(PostamatRepository $postamatRepo, Request $request): JsonResponse
    {
        $addingMethod = $request->input('adding_method', 'all');
        $cityId       = $request->input('city_id', 0);
        $tcId         = $request->input('tc_id', 0);

        return JsonResponse::successful([
            'total_amount'   => $postamatRepo->total($addingMethod, (int)$cityId, (int)$tcId),
            'total_per_page' => $this->totalPerPage(),
        ]);
    }

    /**
     * Returns a list of cities that have postamates.
     */
    public function cities(PostamatRepository $postamatRepo): JsonResponse
    {
        return JsonResponse::successful($postamatRepo->cities());
    }
}
