<?php

declare(strict_types=1);

namespace Modules\Currency\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Currency\Repositories\CurrencyRepository;
use Modules\Currency\Http\Requests\CreateOrUpdateCurrencyRequest;

/**
 * Class CurrencyController
 * @package Modules\Currency\Http\Controllers
 */
class CurrencyController extends Controller
{
    /** @var CurrencyRepository An instance of the Currency Repository */
    private CurrencyRepository $currencyRepo;

    /**
     * CurrencyController constructor.
     */
    public function __construct(CurrencyRepository $currencyRepo)
    {
        $this->currencyRepo = $currencyRepo;
    }

    /**
     * Creates a new currency.
     */
    public function store(CreateOrUpdateCurrencyRequest $request): JsonResponse
    {
        $request->merge(['name' => Str::getNamesJson($request->names)]);
        $currencyId = $this->currencyRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $currencyId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns all currencies.
     */
    public function index(): JsonResponse
    {
        return JsonResponse::successful($this->currencyRepo->all());
    }

    /**
     * Returns currency data by id.
     */
    public function show(CurrencyRepository $currencyRepo): JsonResponse
    {
        return JsonResponse::successful($currencyRepo->data());
    }

    /**
     * Updates currency data.
     */
    public function update(CreateOrUpdateCurrencyRequest $request, CurrencyRepository $currencyRepo): JsonResponse
    {
        $request->merge(['name' => Str::getNamesJson($request->names)]);
        $currencyRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the currency.
     */
    public function destroy(CurrencyRepository $currencyRepo): JsonResponse
    {
        $currencyRepo->delete();

        return JsonResponse::successful();
    }
}
