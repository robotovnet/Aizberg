<?php

declare(strict_types=1);

namespace Modules\Showcase\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Entities\Client;
use Modules\Showcase\Http\Requests\StoreShowcaseCategoryRequest;
use Modules\Showcase\Http\Requests\UpdateShowcaseCategoryRequest;
use Modules\Showcase\Repositories\ShowcaseRepository;
use Modules\Showcase\Services\ShowcasePermissionService;
use RuntimeException;

class ShowcaseCategoryController extends Controller
{
    public function __construct(
        private ShowcaseRepository $repo,
        private ShowcasePermissionService $permissions
    ) {
    }

    public function index(): JsonResponse
    {
        $categories = $this->repo->allCategories()
            ->map(fn ($category) => $this->repo->toCategoryItem($category))
            ->values()
            ->all();

        return JsonResponse::successful([
            'categories' => $categories,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->repo->findCategory($id);
        if ($category === null) {
            return JsonResponse::failed(['message' => 'Категория не найдена.']);
        }

        return JsonResponse::successful([
            'category' => $this->repo->toCategoryItem($category),
        ]);
    }

    public function store(StoreShowcaseCategoryRequest $request): JsonResponse
    {
        if (!$this->ensureModerator()) {
            return JsonResponse::failed(['message' => 'Нет прав модератора.']);
        }

        $category = $this->repo->createCategory($request->validated());

        return JsonResponse::successful([
            'id' => $category->id,
            'category' => $this->repo->toCategoryItem($category),
        ]);
    }

    public function update(UpdateShowcaseCategoryRequest $request, int $id): JsonResponse
    {
        if (!$this->ensureModerator()) {
            return JsonResponse::failed(['message' => 'Нет прав модератора.']);
        }

        $category = $this->repo->findCategory($id);
        if ($category === null) {
            return JsonResponse::failed(['message' => 'Категория не найдена.']);
        }

        $updated = $this->repo->updateCategory($category, $request->validated());

        return JsonResponse::successful([
            'category' => $this->repo->toCategoryItem($updated),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->ensureModerator()) {
            return JsonResponse::failed(['message' => 'Нет прав модератора.']);
        }

        $category = $this->repo->findCategory($id);
        if ($category === null) {
            return JsonResponse::failed(['message' => 'Категория не найдена.']);
        }

        try {
            $this->repo->deleteCategory($category);
        } catch (RuntimeException $e) {
            return JsonResponse::failed(['message' => $e->getMessage()]);
        }

        return JsonResponse::successful();
    }

    private function ensureModerator(): bool
    {
        /** @var Client|null $client */
        $client = Auth::guard('client')->user();
        if ($client === null) {
            return false;
        }

        return $this->permissions->canModerate((int) $client->id);
    }
}
