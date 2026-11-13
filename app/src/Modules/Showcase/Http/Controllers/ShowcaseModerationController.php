<?php

declare(strict_types=1);

namespace Modules\Showcase\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Entities\Client;
use Modules\Showcase\Http\Requests\ModerateShowcaseExampleRequest;
use Modules\Showcase\Repositories\ShowcaseRepository;
use Modules\Showcase\Services\ShowcasePermissionService;

class ShowcaseModerationController extends Controller
{
    public function __construct(
        private ShowcaseRepository $repo,
        private ShowcasePermissionService $permissions
    ) {
    }

    public function pending(Request $request): JsonResponse
    {
        if (!$this->ensureModerator()) {
            return JsonResponse::failed(['message' => 'Нет прав модератора.']);
        }

        $page = max(1, (int) $request->input('page', 1));
        $paginator = $this->repo->paginatePendingExamples($page);

        $items = $paginator->getCollection()
            ->map(fn ($example) => $this->repo->toExampleFull($example))
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

    public function show(int $id): JsonResponse
    {
        if (!$this->ensureModerator()) {
            return JsonResponse::failed(['message' => 'Нет прав модератора.']);
        }

        $example = $this->repo->findExample($id);
        if ($example === null) {
            return JsonResponse::failed(['message' => 'Пример не найден.']);
        }

        return JsonResponse::successful([
            'example' => $this->repo->toExampleFull($example),
        ]);
    }

    public function update(ModerateShowcaseExampleRequest $request, int $id): JsonResponse
    {
        if (!$this->ensureModerator()) {
            return JsonResponse::failed(['message' => 'Нет прав модератора.']);
        }

        $example = $this->repo->findExample($id);
        if ($example === null) {
            return JsonResponse::failed(['message' => 'Пример не найден.']);
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
            false
        );

        return JsonResponse::successful([
            'example' => $this->repo->toExampleFull($updated),
        ]);
    }

    public function approve(int $id): JsonResponse
    {
        if (!$this->ensureModerator()) {
            return JsonResponse::failed(['message' => 'Нет прав модератора.']);
        }

        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $example = $this->repo->findExample($id);
        if ($example === null) {
            return JsonResponse::failed(['message' => 'Пример не найден.']);
        }

        $approved = $this->repo->approveExample($example, (int) $client->id);

        return JsonResponse::successful([
            'example' => $this->repo->toExampleFull($approved),
        ]);
    }

    public function reject(int $id): JsonResponse
    {
        if (!$this->ensureModerator()) {
            return JsonResponse::failed(['message' => 'Нет прав модератора.']);
        }

        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $example = $this->repo->findExample($id);
        if ($example === null) {
            return JsonResponse::failed(['message' => 'Пример не найден.']);
        }

        $rejected = $this->repo->rejectExample($example, (int) $client->id);

        return JsonResponse::successful([
            'example' => $this->repo->toExampleFull($rejected),
        ]);
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
