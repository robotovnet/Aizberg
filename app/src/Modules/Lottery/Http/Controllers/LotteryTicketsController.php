<?php

declare(strict_types=1);

namespace Modules\Lottery\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Lottery\Repositories\LotteryTicketsRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LotteryTicketsController extends Controller
{
    protected LotteryTicketsRepository $repository;

    public function __construct(LotteryTicketsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getTickets(int $generationId): StreamedResponse
    {
        return $this->repository->all($generationId);
    }

    public function generator(Request $request): JsonResponse
    {
        $totalTickets = (int)$request->get('total_tickets', 1000);
        $generationId = $this->repository->generator($totalTickets);

        return JsonResponse::successful([
            'generation_id' => $generationId,
        ]);
    }

    public function getGenerations(): JsonResponse
    {
        $generations = $this->repository->getGenerations();

        return JsonResponse::successful($generations);
    }

    public function getTotalRegisteredTickets(): JsonResponse
    {
        $totalRegisteredTickets = $this->repository->getTotalRegisteredTickets();

        return JsonResponse::successful([
            'total_registered_tickets' => $totalRegisteredTickets,
        ]);
    }

    public function getActiveParticipantsCount(): JsonResponse
    {
        return JsonResponse::successful([
            'active_participants' => $this->repository->getTotalRegisteredTickets(),
        ]);
    }
}
