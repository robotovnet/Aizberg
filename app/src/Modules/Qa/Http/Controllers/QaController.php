<?php

declare(strict_types=1);

namespace Modules\Qa\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Qa\Http\Requests\CreateOrUpdateQaRequest;
use Modules\Qa\Repositories\QaRepository;
use Illuminate\Http\Request;

/**
 * Class QaController
 * @package Modules\Qa\Http\Controllers
 */
class QaController extends Controller
{
    /** @var QaRepository An instance of the Qa Repository. */
    private QaRepository $qaRepo;

    /**
     * QaController constructor.
     */
    public function __construct(QaRepository $qaRepo)
    {
        $this->qaRepo = $qaRepo;
    }

    /**
     * Returns all qa.
     */
    public function index(Request $request): JsonResponse
    {
        $onlyCount = $request->input('onlyCount', null);
        $isReplied = $request->input('isReplied', null);
        $page      = (int)$request->input('page', 1);

        return JsonResponse::successful($this->qaRepo->all($isReplied, $onlyCount, $page));
    }

    /**
     * Creates a new qa.
     */
    public function store(CreateOrUpdateQaRequest $request): JsonResponse
    {
        $qaId = $this->qaRepo->create($request->all());

        return JsonResponse::successful([
            'id'      => $qaId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns qa data by id.
     */
    public function show(QaRepository $qaRepo): JsonResponse
    {
        return JsonResponse::successful($qaRepo->data());
    }

    /**
     * Updates qa data.
     */
    public function update(
        CreateOrUpdateQaRequest $request,
        QaRepository $qaRepo
    ): JsonResponse {
        $qaRepo->update($request->all());

        return JsonResponse::successful();
    }

    public function answer(Request $request, QaRepository $qaRepo): JsonResponse
    {
        $answer = $request->get('answer', null);
        $qaRepo->answer($qaRepo->model, $answer);

        return JsonResponse::successful();
    }

    /**
     * Deletes the qa.
     */
    public function destroy(QaRepository $qaRepo): JsonResponse
    {
        $qaRepo->delete();

        return JsonResponse::successful();
    }
}
