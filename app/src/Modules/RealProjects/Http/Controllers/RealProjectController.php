<?php

declare(strict_types=1);

namespace Modules\RealProjects\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Modules\RealProjects\Entities\RealProject;
use Modules\RealProjects\Http\Requests\CreateOrUpdateRealProjectRequest;
use Modules\RealProjects\Repositories\RealProjectRepository;
use Modules\RealProjects\Services\RealProjectBannerService;
use Throwable;

/**
 * Управление проектами «В реальных проектах» из админки.
 */
class RealProjectController extends Controller
{
    private RealProjectRepository $repository;

    private RealProjectBannerService $bannerService;

    public function __construct(RealProjectRepository $repository, RealProjectBannerService $bannerService)
    {
        $this->repository = $repository;
        $this->bannerService = $bannerService;
    }

    public function index(): JsonResponse
    {
        $items = $this->repository->all()
            ->map(fn (RealProject $project): array => $this->toArray($project))
            ->values()
            ->all();

        return JsonResponse::successful($items);
    }

    public function show(int $id): JsonResponse
    {
        return JsonResponse::successful($this->toArray($this->repository->find($id)));
    }

    public function store(CreateOrUpdateRealProjectRequest $request): JsonResponse
    {
        $data = $this->attributesFromRequest($request);
        $data['banner_path'] = $this->bannerService->store($request->file('banner'));

        try {
            $this->repository->create($data);
        } catch (Throwable $exception) {
            $this->bannerService->deleteByStoredPath($data['banner_path']);
            throw $exception;
        }

        return JsonResponse::successful();
    }

    /**
     * Обновляет проект. Для загрузки нового баннера используйте POST (multipart/form-data);
     * без поля banner текущий баннер сохраняется.
     */
    public function update(CreateOrUpdateRealProjectRequest $request, int $id): JsonResponse
    {
        $oldBannerPath = $this->repository->find($id)->banner_path;
        $data = $this->attributesFromRequest($request);

        if ($request->hasFile('banner')) {
            $data['banner_path'] = $this->bannerService->store($request->file('banner'));
        }

        try {
            $this->repository->update($id, $data);
        } catch (Throwable $exception) {
            if (isset($data['banner_path'])) {
                $this->bannerService->deleteByStoredPath($data['banner_path']);
            }
            throw $exception;
        }

        if (isset($data['banner_path'])) {
            $this->bannerService->deleteByStoredPath($oldBannerPath);
        }

        return JsonResponse::successful();
    }

    public function destroy(int $id): JsonResponse
    {
        $bannerPath = $this->repository->find($id)->banner_path;

        $this->repository->delete($id);
        $this->bannerService->deleteByStoredPath($bannerPath);

        return JsonResponse::successful();
    }

    /**
     * Name, url and sort from the validated request; empty sort falls back to the default.
     */
    private function attributesFromRequest(CreateOrUpdateRealProjectRequest $request): array
    {
        $data = Arr::only($request->validated(), ['name', 'url', 'sort']);
        $data['sort'] = isset($data['sort']) ? (int)$data['sort'] : RealProject::DEFAULT_SORT;

        return $data;
    }

    private function toArray(RealProject $project): array
    {
        return [
            'id'         => $project->id,
            'name'       => $project->name,
            'url'        => $project->url,
            'sort'       => $project->sort,
            'banner_url' => $this->bannerService->publicUrl($project->banner_path),
        ];
    }
}
