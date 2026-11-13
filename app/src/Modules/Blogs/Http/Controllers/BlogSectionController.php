<?php

declare(strict_types=1);

namespace Modules\Blogs\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Blogs\Http\Requests\StoreBlogSectionRequest;
use Modules\Blogs\Http\Requests\UpdateBlogSectionRequest;
use Modules\Blogs\Repositories\BlogRepository;
use Modules\Blogs\Services\BlogPermissionService;
use Modules\Client\Entities\Client;
use RuntimeException;

class BlogSectionController extends Controller
{
    public function __construct(
        private BlogRepository $repo,
        private BlogPermissionService $permissions
    ) {
    }

    public function index(): JsonResponse
    {
        $sections = $this->repo->sectionTree()
            ->map(fn ($section) => $this->repo->toSectionItem($section))
            ->values()
            ->all();

        return JsonResponse::successful(['sections' => $sections]);
    }

    public function show(int $id): JsonResponse
    {
        $section = $this->repo->findSection($id);
        if ($section === null) {
            return JsonResponse::failed(['message' => 'Раздел не найден.']);
        }

        return JsonResponse::successful([
            'section' => $this->repo->toSectionItem($section),
        ]);
    }

    public function store(StoreBlogSectionRequest $request): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        if (!$this->permissions->canManageSections((int) $client->id)) {
            return JsonResponse::failed(['message' => 'Нет прав на создание разделов.']);
        }

        $section = $this->repo->createSection($request->validated());

        return JsonResponse::successful([
            'id' => $section->id,
            'section' => $this->repo->toSectionItem($section),
        ]);
    }

    public function update(UpdateBlogSectionRequest $request, int $id): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        if (!$this->permissions->canManageSections((int) $client->id)) {
            return JsonResponse::failed(['message' => 'Нет прав на редактирование разделов.']);
        }

        $section = $this->repo->findSection($id);
        if ($section === null) {
            return JsonResponse::failed(['message' => 'Раздел не найден.']);
        }

        $updated = $this->repo->updateSection($section, $request->validated());

        return JsonResponse::successful([
            'section' => $this->repo->toSectionItem($updated),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        if (!$this->permissions->canManageSections((int) $client->id)) {
            return JsonResponse::failed(['message' => 'Нет прав на удаление разделов.']);
        }

        $section = $this->repo->findSection($id);
        if ($section === null) {
            return JsonResponse::failed(['message' => 'Раздел не найден.']);
        }

        try {
            $this->repo->deleteSection($section);
        } catch (RuntimeException $e) {
            return JsonResponse::failed(['message' => $e->getMessage()]);
        }

        return JsonResponse::successful();
    }
}
