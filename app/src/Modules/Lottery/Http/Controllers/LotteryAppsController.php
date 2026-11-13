<?php

declare(strict_types=1);

namespace Modules\Lottery\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Lottery\Repositories\LotteryAppsRepository;
use Illuminate\Http\Request;
use Modules\Lottery\Http\Requests\AppRegRequest;

class LotteryAppsController extends Controller
{
    private LotteryAppsRepository $lotteryAppsRepo;

    public function __construct(LotteryAppsRepository $lotteryAppsRepo)
    {
        $this->lotteryAppsRepo = $lotteryAppsRepo;
    }

    public function all(Request $request): JsonResponse
    {
        $allowedFields = ['id', 'created_at', 'player_name', 'player_phone', 'player_email', 'status'];
        $orderBy = $request->input('orderBy', 'created_at');
        
        // Валидация поля для сортировки
        if (!in_array($orderBy, $allowedFields)) {
            $orderBy = 'created_at';
        }

        return JsonResponse::successful($this->lotteryAppsRepo->all($orderBy));
    }

    public function detail(int $id): JsonResponse
    {
        return JsonResponse::successful($this->lotteryAppsRepo->detail($id));
    }

    public function accept(int $id): JsonResponse
    {
        $this->lotteryAppsRepo->accept($id);

        return JsonResponse::successful();
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $this->lotteryAppsRepo->reject($id, $request->input('rejectDesc', ''));

        return JsonResponse::successful();
    }

    public function total(): JsonResponse
    {
        return JsonResponse::successful(['total' => $this->lotteryAppsRepo->total()]);
    }

    public function register(AppRegRequest $request): JsonResponse
    {
        $result = $this->lotteryAppsRepo->register($request->validated());

        return ($result) ? JsonResponse::successful() : JsonResponse::error('Ошибка регистрации билета!');
    }
}
