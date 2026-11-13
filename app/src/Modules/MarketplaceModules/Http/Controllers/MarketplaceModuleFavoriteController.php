<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Entities\Client;
use Modules\MarketplaceModules\Entities\MarketplaceModule;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;

class MarketplaceModuleFavoriteController extends Controller
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

        $paginator = $this->repo->favoriteModulesForClient((int)$client->id, $page);
        $items = $paginator->getCollection()
            ->map(fn (MarketplaceModule $module) => $this->repo->toListItem($module))
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

    public function store(string $slug): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $module = $this->repo->findPublicModuleBySlug($slug);
        if ($module === null) {
            return JsonResponse::failed(['message' => 'Модуль не найден.']);
        }

        $this->repo->addModuleToFavorites((int)$module->id, (int)$client->id);

        return JsonResponse::successful(['is_favorite' => true]);
    }

    public function destroy(string $slug): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $module = $this->repo->findModuleBySlug($slug);
        if ($module === null) {
            return JsonResponse::failed(['message' => 'Модуль не найден.']);
        }

        $this->repo->removeModuleFromFavorites((int)$module->id, (int)$client->id);

        return JsonResponse::successful(['is_favorite' => false]);
    }
}
