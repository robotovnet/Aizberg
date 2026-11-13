<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Entities\Client;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;

class MarketplaceModuleActivityController extends Controller
{
    public function __construct(
        private MarketplaceModuleRepository $repo
    ) {
    }

    public function reviews(Request $request): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();
        $page = max(1, (int)$request->input('page', 1));

        return JsonResponse::successful(
            $this->repo->paginateReviewsForClient((int)$client->id, $page)
        );
    }

    public function questions(Request $request): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();
        $page = max(1, (int)$request->input('page', 1));

        return JsonResponse::successful(
            $this->repo->paginateQuestionsForClient((int)$client->id, $page)
        );
    }
}
