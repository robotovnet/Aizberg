<?php

declare(strict_types=1);

namespace Modules\RealProjects\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\RealProjects\Entities\RealProject;
use Modules\RealProjects\Repositories\RealProjectRepository;
use Modules\RealProjects\Services\RealProjectBannerService;

/**
 * Публичные данные для блока «В реальных проектах» на сайте.
 */
class PublicRealProjectController extends Controller
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
            ->map(fn (RealProject $project): array => [
                'name'       => $project->name,
                'banner_url' => $this->bannerService->publicUrl($project->banner_path),
                'url'        => $project->url,
            ])
            ->values()
            ->all();

        return JsonResponse::successful(['items' => $items]);
    }
}
