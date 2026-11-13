<?php

declare(strict_types=1);

namespace Modules\Lottery\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Lottery\Repositories\LotteryDrawsRepository;

class LotteryDrawsController extends Controller
{
    private LotteryDrawsRepository $lotteryDrawsRepo;

    public function __construct(LotteryDrawsRepository $lotteryDrawsRepo)
    {
        $this->lotteryDrawsRepo = $lotteryDrawsRepo;
    }

    public function all(): JsonResponse
    {
        return JsonResponse::successful($this->lotteryDrawsRepo->all());
    }
}
