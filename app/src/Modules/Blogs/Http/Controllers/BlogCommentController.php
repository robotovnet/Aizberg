<?php

declare(strict_types=1);

namespace Modules\Blogs\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Blogs\Http\Requests\StoreBlogCommentRequest;
use Modules\Blogs\Http\Requests\UpdateBlogCommentRequest;
use Modules\Blogs\Repositories\BlogRepository;
use Modules\Blogs\Services\BlogPermissionService;
use Modules\Client\Entities\Client;
use RuntimeException;

class BlogCommentController extends Controller
{
    public function __construct(
        private BlogRepository $repo,
        private BlogPermissionService $permissions
    ) {
    }

    public function index(int $id): JsonResponse
    {
        $article = $this->repo->findArticle($id);
        if ($article === null) {
            return JsonResponse::failed(['message' => 'Статья не найдена.']);
        }

        return JsonResponse::successful([
            'comments' => $this->repo->articleCommentsTree($id),
        ]);
    }

    public function store(StoreBlogCommentRequest $request, int $id): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $article = $this->repo->findArticle($id);
        if ($article === null) {
            return JsonResponse::failed(['message' => 'Статья не найдена.']);
        }

        try {
            $comment = $this->repo->createComment($id, (int) $client->id, (string) $request->input('body'));
        } catch (RuntimeException $e) {
            return JsonResponse::failed(['message' => $e->getMessage()]);
        }

        return JsonResponse::successful([
            'id' => $comment->id,
            'created_at' => $comment->created_at?->toIso8601String(),
        ]);
    }

    public function reply(StoreBlogCommentRequest $request, int $commentId): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $parent = $this->repo->findComment($commentId);
        if ($parent === null) {
            return JsonResponse::failed(['message' => 'Комментарий не найден.']);
        }

        $article = $this->repo->findArticle((int) $parent->blog_article_id);
        if ($article === null) {
            return JsonResponse::failed(['message' => 'Статья не найдена.']);
        }

        try {
            $comment = $this->repo->createComment(
                (int) $parent->blog_article_id,
                (int) $client->id,
                (string) $request->input('body'),
                $commentId
            );
        } catch (RuntimeException $e) {
            return JsonResponse::failed(['message' => $e->getMessage()]);
        }

        return JsonResponse::successful([
            'id' => $comment->id,
            'parent_id' => $comment->parent_id,
            'created_at' => $comment->created_at?->toIso8601String(),
        ]);
    }

    public function update(UpdateBlogCommentRequest $request, int $commentId): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $comment = $this->repo->findComment($commentId);
        if ($comment === null) {
            return JsonResponse::failed(['message' => 'Комментарий не найден.']);
        }

        if (!$this->permissions->canEditComment($comment, (int) $client->id)) {
            return JsonResponse::failed(['message' => 'Нет прав на редактирование комментария.']);
        }

        $updated = $this->repo->updateComment($comment, (string) $request->input('body'));

        return JsonResponse::successful([
            'comment' => [
                'id' => $updated->id,
                'body' => $updated->body,
                'updated_at' => $updated->updated_at?->toIso8601String(),
            ],
        ]);
    }

    public function destroy(int $commentId): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $comment = $this->repo->findComment($commentId);
        if ($comment === null) {
            return JsonResponse::failed(['message' => 'Комментарий не найден.']);
        }

        if (!$this->permissions->canEditComment($comment, (int) $client->id)) {
            return JsonResponse::failed(['message' => 'Нет прав на удаление комментария.']);
        }

        $this->repo->deleteComment($comment);

        return JsonResponse::successful();
    }
}
