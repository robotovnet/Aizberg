<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Entities\Client;
use Modules\MarketplaceModules\Entities\MarketplaceModulePurchase;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;

class MarketplaceModulePurchaseController extends Controller
{
    public function __construct(
        private MarketplaceModuleRepository $repo
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();
        $page = max(1, (int)$request->input('page', 1));

        $paginator = $this->repo->purchasesForClient((int)$client->id, $page);
        $items = $paginator->getCollection()
            ->map(fn (MarketplaceModulePurchase $purchase) => $this->repo->toPurchaseItem($purchase))
            ->values()
            ->all();

        return JsonResponse::successful([
            'items' => $items,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ]);
    }

    public function debugStore(string $slug): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $module = $this->repo->findPublicModuleBySlug($slug);
        if ($module === null) {
            return JsonResponse::failed(['message' => 'Модуль не найден.']);
        }

        $purchase = $this->repo->createPurchase((int)$module->id, (int)$client->id);
        if ($purchase === null) {
            return JsonResponse::failed(['message' => 'Модуль уже куплен.']);
        }

        $purchase->setRelation('module', $module);

        return JsonResponse::successful($this->repo->toPurchaseItem($purchase));
    }
}
