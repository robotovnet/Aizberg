<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\MarketplaceModules\Entities\MarketplaceModule;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;

class PublicMarketplaceController extends Controller
{
    public function __construct(
        private MarketplaceModuleRepository $repo
    ) {
    }

    public function search(Request $request): JsonResponse
    {
        $q = (string)$request->input('q', '');
        $page = max(1, (int)$request->input('page', 1));
        $category = (string)$request->input('category', '');
        $licenseType = (string)$request->input('license_type', '');
        $pricingType = (string)$request->input('pricing_type', '');
        $sort = (string)$request->input('sort', 'date_added');
        $order = (string)$request->input('order', 'desc');

        $paginator = $this->repo->searchPublic($q, $page, $category, $licenseType, $sort, $order, $pricingType);
        $this->repo->recordSearchQuery($q);
        $items = $paginator->getCollection()->map(fn (MarketplaceModule $m) => $this->repo->toListItem($m))->values()->all();

        return JsonResponse::successful([
            'items' => $items,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    public function popularSearches(): JsonResponse
    {
        return JsonResponse::successful([
            'items' => $this->repo->popularSearchQueries(),
        ]);
    }

    public function byCategory(Request $request, string $category): JsonResponse
    {
        $page = max(1, (int)$request->input('page', 1));
        $sort = (string)$request->input('sort', 'date_added');
        $order = (string)$request->input('order', 'desc');

        $paginator = $this->repo->listByCategory(rawurldecode($category), $page, $sort, $order);
        $items = $paginator->getCollection()->map(fn (MarketplaceModule $m) => $this->repo->toListItem($m))->values()->all();

        return JsonResponse::successful([
            'items' => $items,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    public function featured(): JsonResponse
    {
        return JsonResponse::successful($this->repo->featuredBundles());
    }

    public function categories(): JsonResponse
    {
        return JsonResponse::successful([
            'items' => $this->repo->categories(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $module = $this->repo->getBySlugPublic($slug);
        if ($module === null) {
            return response()->json(['message' => 'Module not found.'], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $client = Auth::guard('client')->user();
        $card = $this->repo->toFullCard($module);
        $card['is_favourite'] = $client !== null
            && $this->repo->isFavoriteForClient((int)$module->id, (int)$client->id);
        $card['similar_modules'] = $this->repo->similarModules($module);

        return JsonResponse::successful($card);
    }

    public function questions(Request $request, string $slug): JsonResponse
    {
        $page = max(1, (int)$request->input('page', 1));

        return JsonResponse::successful($this->repo->paginateQuestionsWithAnswers($slug, $page));
    }

    public function reviews(Request $request, string $slug): JsonResponse
    {
        $page = max(1, (int)$request->input('page', 1));

        return JsonResponse::successful($this->repo->paginateReviewsWithRatings($slug, $page));
    }

    public function versions(string $slug): JsonResponse
    {
        return JsonResponse::successful([
            'items' => $this->repo->versionHistory($slug),
        ]);
    }
}
