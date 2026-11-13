<?php

declare(strict_types=1);

namespace Modules\Showcase\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Entities\Client;
use Modules\Showcase\Http\Requests\StoreShowcaseExampleRequest;
use Modules\Showcase\Http\Requests\UpdateShowcaseExampleRequest;
use Modules\Showcase\Repositories\ShowcaseRepository;
use Modules\Showcase\Services\ShowcasePermissionService;

class ShowcaseExampleController extends Controller
{
    public function __construct(
        private ShowcaseRepository $repo,
        private ShowcasePermissionService $permissions
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->input('page', 1));
        $categoryId = $request->input('category_id');
        $categoryId = $categoryId !== null ? (int) $categoryId : null;
        $moduleName = $request->input('module');
        $moduleName = is_string($moduleName) && $moduleName !== '' ? $moduleName : null;

        $paginator = $this->repo->paginatePublicExamples($page, $categoryId, $moduleName);

        return $this->paginatedResponse($paginator);
    }

    public function indexByCategory(Request $request, int $id): JsonResponse
    {
        $category = $this->repo->findCategory($id);
        if ($category === null) {
            return JsonResponse::failed(['message' => 'Категория не найдена.']);
        }

        $request->merge(['category_id' => $id]);

        return $this->index($request);
    }

    public function indexByModule(Request $request, string $moduleName): JsonResponse
    {
        $request->merge(['module' => urldecode($moduleName)]);

        return $this->index($request);
    }

    public function modules(): JsonResponse
    {
        return JsonResponse::successful([
            'modules' => $this->repo->usedModuleNames(true),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $example = $this->repo->findApprovedExample($id);
        if ($example === null) {
            return JsonResponse::failed(['message' => 'Пример не найден.']);
        }

        return JsonResponse::successful([
            'example' => $this->repo->toExampleFull($example),
        ]);
    }

    public function myExamples(Request $request): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();
        $page = max(1, (int) $request->input('page', 1));

        $paginator = $this->repo->paginateClientExamples((int) $client->id, $page);

        return $this->paginatedResponse($paginator);
    }

    public function store(StoreShowcaseExampleRequest $request): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $data = $request->validated();
        $modules = (array) $request->input('modules', []);
        $images = $request->file('images', []) ?? [];

        if (!is_array($images)) {
            $images = [$images];
        }

        $example = $this->repo->createExample(
            (int) $client->id,
            $data,
            $modules,
            array_values(array_filter($images))
        );

        return JsonResponse::successful([
            'id' => $example->id,
            'example' => $this->repo->toExampleFull($example),
        ]);
    }

    public function update(UpdateShowcaseExampleRequest $request, int $id): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $example = $this->repo->findExample($id);
        if ($example === null) {
            return JsonResponse::failed(['message' => 'Пример не найден.']);
        }

        if (!$this->permissions->canEditOwnExample($example, (int) $client->id)) {
            return JsonResponse::failed(['message' => 'Нет прав на редактирование примера.']);
        }

        $data = $request->validated();
        $modules = $request->has('modules') ? (array) $request->input('modules', []) : null;
        $images = $request->file('images');
        $imageFiles = null;
        if ($images !== null) {
            $imageFiles = is_array($images) ? array_values(array_filter($images)) : [$images];
        }

        $removeImageIds = $request->has('remove_image_ids')
            ? array_map('intval', (array) $request->input('remove_image_ids', []))
            : null;

        $updated = $this->repo->updateExample(
            $example,
            $data,
            $modules,
            $imageFiles,
            $removeImageIds,
            true
        );

        return JsonResponse::successful([
            'example' => $this->repo->toExampleFull($updated),
        ]);
    }

    /**
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \Modules\Showcase\Entities\ShowcaseExample> $paginator
     */
    private function paginatedResponse($paginator): JsonResponse
    {
        $items = $paginator->getCollection()
            ->map(fn ($example) => $this->repo->toExampleListItem($example))
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
}
