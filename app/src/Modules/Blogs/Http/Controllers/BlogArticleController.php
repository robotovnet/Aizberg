<?php

declare(strict_types=1);

namespace Modules\Blogs\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Blogs\Http\Requests\StoreBlogArticleRequest;
use Modules\Blogs\Http\Requests\UpdateBlogArticleRequest;
use Modules\Blogs\Repositories\BlogRepository;
use Modules\Blogs\Services\BlogPermissionService;
use Modules\Client\Entities\Client;

class BlogArticleController extends Controller
{
    public function __construct(
        private BlogRepository $repo,
        private BlogPermissionService $permissions
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->input('page', 1));
        $sectionId = $request->input('section_id');
        $sectionId = $sectionId !== null ? (int) $sectionId : null;

        $paginator = $this->repo->paginateArticles($sectionId, $page);
        $items = $paginator->getCollection()
            ->map(fn ($article) => $this->repo->toArticleListItem($article))
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

    public function indexBySection(Request $request, int $id): JsonResponse
    {
        $request->merge(['section_id' => $id]);

        return $this->index($request);
    }

    public function show(int $id): JsonResponse
    {
        $article = $this->repo->findArticle($id);
        if ($article === null) {
            return JsonResponse::failed(['message' => 'Статья не найдена.']);
        }

        return JsonResponse::successful([
            'article' => $this->repo->toArticleFull($article),
        ]);
    }

    public function store(StoreBlogArticleRequest $request): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $data = $request->validated();
        $tags = (array) $request->input('tags', []);

        $article = $this->repo->createArticle(
            (int) $client->id,
            $data,
            $tags,
            $request->file('preview_image')
        );

        return JsonResponse::successful([
            'id' => $article->id,
            'article' => $this->repo->toArticleFull($article),
        ]);
    }

    public function update(UpdateBlogArticleRequest $request, int $id): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $article = $this->repo->findArticleForEdit($id);
        if ($article === null) {
            return JsonResponse::failed(['message' => 'Статья не найдена.']);
        }

        if (!$this->permissions->canEditArticle($article, (int) $client->id)) {
            return JsonResponse::failed(['message' => 'Нет прав на редактирование статьи.']);
        }

        $data = $request->validated();
        $tags = $request->has('tags') ? (array) $request->input('tags', []) : null;

        $updated = $this->repo->updateArticle(
            $article,
            $data,
            $tags,
            $request->file('preview_image')
        );

        return JsonResponse::successful([
            'article' => $this->repo->toArticleFull($updated),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $article = $this->repo->findArticleForEdit($id);
        if ($article === null) {
            return JsonResponse::failed(['message' => 'Статья не найдена.']);
        }

        if (!$this->permissions->canEditArticle($article, (int) $client->id)) {
            return JsonResponse::failed(['message' => 'Нет прав на удаление статьи.']);
        }

        $this->repo->deleteArticle($article);

        return JsonResponse::successful();
    }

    public function archive(int $id): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $article = $this->repo->findArticleForEdit($id);
        if ($article === null) {
            return JsonResponse::failed(['message' => 'Статья не найдена.']);
        }

        if (!$this->permissions->canEditArticle($article, (int) $client->id)) {
            return JsonResponse::failed(['message' => 'Нет прав на архивирование статьи.']);
        }

        $archived = $this->repo->archiveArticle($article);

        return JsonResponse::successful([
            'is_archived' => true,
            'article' => $this->repo->toArticleFull($archived),
        ]);
    }
}
