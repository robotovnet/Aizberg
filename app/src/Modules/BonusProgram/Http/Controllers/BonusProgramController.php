<?php

namespace Modules\BonusProgram\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\BonusProgram\Http\Requests\CreateOrUpdateBlockedSectionsRequest;
use Modules\BonusProgram\Http\Requests\CreateOrUpdateBonusLevelRequest;
use Modules\BonusProgram\Repositories\BlockedSectionsRepository;
use Modules\BonusProgram\Repositories\BonusProgramRepository;
use Illuminate\Support\Str;

/**
 * Class BonusProgramController
 * @package Modules\BonusProgram\Http\Controllers
 */
class BonusProgramController extends Controller
{
    /** @var BonusProgramRepository An instance of the Bonus Program Repository. */
    private BonusProgramRepository $bpRepo;

    /**
     * BonusProgramController constructor.
     */
    public function __construct(BonusProgramRepository $bpRepo)
    {
        $this->bpRepo = $bpRepo;
    }

    /**
     * Returns all bonus levels.
     */
    public function index(): JsonResponse
    {
        return JsonResponse::successful($this->bpRepo->all());
    }

    /**
     * Creates a new bonus level.
     */
    public function store(CreateOrUpdateBonusLevelRequest $request): JsonResponse
    {
        $request->merge(
            [
                'name' => Str::getNamesJson($request->names),
            ]
        );

        $levelId = $this->bpRepo->create($request->all());

        return JsonResponse::successful(
            [
                'id' => $levelId,
                'message' => 'success',
            ]
        );
    }

    /**
     * Returns bonus level data by id.
     */
    public function show(BonusProgramRepository $bpRepo): JsonResponse
    {
        return JsonResponse::successful($bpRepo->data());
    }

    /**
     * Updates bonus level data.
     */
    public function update(
        CreateOrUpdateBonusLevelRequest $request,
        BonusProgramRepository $bpRepo
    ): JsonResponse {
        $request->merge(
            [
                'name' => Str::getNamesJson($request->names),
            ]
        );

        $bpRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the bonus level.
     */
    public function destroy(BonusProgramRepository $bpRepo): JsonResponse
    {
        $bpRepo->delete();

        return JsonResponse::successful();
    }

    /**
     * Sets catalog sections excluded from the bonus program.
     */
    public function blockingSections(
        CreateOrUpdateBlockedSectionsRequest $request,
        BlockedSectionsRepository $repo
    ): JsonResponse {
        $repo->save($request->input('sections', []));

        return JsonResponse::successful();
    }

    /**
     * Returns catalog sections excluded from the bonus program.
     */
    public function getBlockedSections(BlockedSectionsRepository $repo): JsonResponse
    {
        return JsonResponse::successful($repo->all());
    }
}
