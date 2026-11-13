<?php

declare(strict_types=1);

namespace Modules\Lottery\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Lottery\Http\Requests\AddPrizeRequest;
use Modules\Lottery\Entities\LotteryPrizes;
use Modules\Lottery\Repositories\LotteryPrizesRepository;

/**
 * Class LotteryPrizesController
 * @package Modules\Lottery\Http\Controllers
 */
class LotteryPrizesController extends Controller
{
    /** @var LotteryPrizesRepository An instance of the LotteryPrizes Repository. */
    private LotteryPrizesRepository $lotteryPrizesRepo;

    public function __construct(LotteryPrizesRepository $lotteryPrizesRepo)
    {
        $this->lotteryPrizesRepo = $lotteryPrizesRepo;
    }

    /**
     * Returns all lottery prizes.
     */
    public function all(): JsonResponse
    {
        return JsonResponse::successful($this->lotteryPrizesRepo->all());
    }

    /**
     * Adds a new prize to the lottery.
     */
    public function add(AddPrizeRequest $request): JsonResponse
    {
        $productVendorCode = $request->get('product_vendor_code', null);
        $lotteryPrizesId = $this->lotteryPrizesRepo->add($productVendorCode);

        return JsonResponse::successful([
            'id'      => $lotteryPrizesId,
            'message' => 'success',
        ]);
    }

    /**
     * Deletes a prize from the lottery.
     */
    public function delete(int $prizeId): JsonResponse
    {
        $result = $this->lotteryPrizesRepo->delete($prizeId);

        return JsonResponse::successful([
            'message' => $result ? 'success' : 'error',
        ]);
    }
}
