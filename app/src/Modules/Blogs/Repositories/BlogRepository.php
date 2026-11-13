<?php

declare(strict_types=1);

namespace Modules\Blogs\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Blogs\Entities\BlogArticle;
use Modules\Blogs\Entities\BlogArticleTag;
use Modules\Blogs\Entities\BlogComment;
use Modules\Blogs\Entities\BlogSection;
use Modules\Blogs\Services\BlogPreviewImageService;
use RuntimeException;

class BlogRepository
{
    public function __construct(
        private BlogPreviewImageService $previewImageService
    ) {
    }

    /**
     * @return Collection<int, BlogSection>
     */
    public function sectionTree(): Collection
    {
        $sections = BlogSection::query()
            ->with(['children' => fn ($q) => $q->orderBy('sort_order')])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return $sections;
    }

    public function findSection(int $id): ?BlogSection
    {
        return BlogSection::query()
            ->with(['parent', 'children' => fn ($q) => $q->orderBy('sort_order')])
            ->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createSection(array $data): BlogSection
    {
        $section = new BlogSection([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
        $section->save();

        return $section->fresh(['parent', 'children']);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateSection(BlogSection $section, array $data): BlogSection
    {
        if (array_key_exists('name', $data)) {
            $section->name = $data['name'];
        }
        if (array_key_exists('slug', $data)) {
            $section->slug = $data['slug'];
        }
        if (array_key_exists('parent_id', $data)) {
            $section->parent_id = $data['parent_id'];
        }
        if (array_key_exists('sort_order', $data)) {
            $section->sort_order = (int) $data['sort_order'];
        }

        $section->save();

        return $section->fresh(['parent', 'children']);
    }

    public function deleteSection(BlogSection $section): void
    {
        if ($section->children()->exists()) {
            throw new RuntimeException('Нельзя удалить раздел, у которого есть подразделы.');
        }

        if ($section->articles()->exists()) {
            throw new RuntimeException('Нельзя удалить раздел, в котором есть статьи.');
        }

        $section->delete();
    }

    /**
     * @return LengthAwarePaginator<int, BlogArticle>
     */
    public function paginateArticles(?int $sectionId, int $page, bool $includeArchived = false): LengthAwarePaginator
    {
        $perPage = (int) Config::get('blogs.articles_per_page', 20);

        $query = BlogArticle::query()
            ->with(['tags', 'author:id,first_name,last_name', 'section'])
            ->orderByDesc('created_at');

        if (!$includeArchived) {
            $query->where('is_archived', false);
        }

        if ($sectionId !== null) {
            $sectionIds = $this->resolveSectionIds($sectionId);
            $query->whereIn('section_id', $sectionIds);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function findArticle(int $id, bool $includeArchived = false): ?BlogArticle
    {
        $query = BlogArticle::query()
            ->with(['tags', 'author:id,first_name,last_name', 'section.parent']);

        if (!$includeArchived) {
            $query->where('is_archived', false);
        }

        return $query->find($id);
    }

    public function findArticleForEdit(int $id): ?BlogArticle
    {
        return BlogArticle::query()
            ->with(['tags', 'author:id,first_name,last_name', 'section'])
            ->find($id);
    }

    /**
     * @param string[] $tags
     * @param array<string, mixed> $data
     */
    public function createArticle(int $clientId, array $data, array $tags, ?UploadedFile $previewImage): BlogArticle
    {
        return DB::transaction(function () use ($clientId, $data, $tags, $previewImage) {
            $previewPath = null;
            if ($previewImage !== null) {
                $previewPath = $this->previewImageService->store($previewImage);
            }

            $article = new BlogArticle([
                'client_id' => $clientId,
                'section_id' => (int) $data['section_id'],
                'title' => $data['title'],
                'preview_image_path' => $previewPath,
                'short_description' => $data['short_description'],
                'content' => $data['content'],
                'is_archived' => false,
            ]);
            $article->save();

            $this->syncTags($article->id, $tags);

            return $article->fresh(['tags', 'author:id,first_name,last_name', 'section']);
        });
    }

    /**
     * @param string[] $tags
     * @param array<string, mixed> $data
     */
    public function updateArticle(BlogArticle $article, array $data, ?array $tags, ?UploadedFile $previewImage): BlogArticle
    {
        return DB::transaction(function () use ($article, $data, $tags, $previewImage) {
            if ($previewImage !== null) {
                $this->previewImageService->deleteByStoredPath($article->preview_image_path);
                $article->preview_image_path = $this->previewImageService->store($previewImage);
            }

            foreach (['title', 'section_id', 'short_description', 'content'] as $field) {
                if (array_key_exists($field, $data)) {
                    $article->{$field} = $data[$field];
                }
            }

            $article->save();

            if ($tags !== null) {
                $this->syncTags($article->id, $tags);
            }

            return $article->fresh(['tags', 'author:id,first_name,last_name', 'section']);
        });
    }

    public function deleteArticle(BlogArticle $article): void
    {
        DB::transaction(function () use ($article) {
            $this->previewImageService->deleteByStoredPath($article->preview_image_path);
            $article->delete();
        });
    }

    public function archiveArticle(BlogArticle $article): BlogArticle
    {
        $article->is_archived = true;
        $article->save();

        return $article->fresh(['tags', 'author:id,first_name,last_name', 'section']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function articleCommentsTree(int $articleId): array
    {
        $comments = BlogComment::query()
            ->with(['author:id,first_name,last_name', 'replies.author:id,first_name,last_name'])
            ->where('blog_article_id', $articleId)
            ->whereNull('parent_id')
            ->orderBy('created_at')
            ->get();

        return $comments->map(fn (BlogComment $c) => $this->toCommentNode($c))->values()->all();
    }

    public function createComment(int $articleId, int $clientId, string $body, ?int $parentId = null): BlogComment
    {
        if ($parentId !== null) {
            $parent = BlogComment::query()
                ->where('blog_article_id', $articleId)
                ->find($parentId);

            if ($parent === null) {
                throw new RuntimeException('Родительский комментарий не найден.');
            }
        }

        $comment = new BlogComment([
            'blog_article_id' => $articleId,
            'client_id' => $clientId,
            'parent_id' => $parentId,
            'body' => $body,
        ]);
        $comment->save();

        return $comment->fresh(['author:id,first_name,last_name']);
    }

    public function updateComment(BlogComment $comment, string $body): BlogComment
    {
        $comment->body = $body;
        $comment->save();

        return $comment->fresh(['author:id,first_name,last_name']);
    }

    public function deleteComment(BlogComment $comment): void
    {
        $comment->delete();
    }

    public function findComment(int $id): ?BlogComment
    {
        return BlogComment::query()->find($id);
    }

    /**
     * @return array<string, mixed>
     */
    public function toSectionItem(BlogSection $section, bool $withChildren = true): array
    {
        $item = [
            'id' => $section->id,
            'name' => $section->name,
            'slug' => $section->slug,
            'parent_id' => $section->parent_id,
            'sort_order' => $section->sort_order,
            'created_at' => $section->created_at?->toIso8601String(),
            'updated_at' => $section->updated_at?->toIso8601String(),
        ];

        if ($withChildren && $section->relationLoaded('children')) {
            $item['children'] = $section->children
                ->map(fn (BlogSection $child) => $this->toSectionItem($child, false))
                ->values()
                ->all();
        }

        return $item;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArticleListItem(BlogArticle $article): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'short_description' => $article->short_description,
            'preview_image_url' => $this->previewImageService->publicUrl($article->preview_image_path),
            'section_id' => $article->section_id,
            'section' => $article->relationLoaded('section') && $article->section !== null
                ? ['id' => $article->section->id, 'name' => $article->section->name]
                : null,
            'tags' => $article->relationLoaded('tags')
                ? $article->tags->pluck('tag')->values()->all()
                : [],
            'author' => $this->toAuthor($article),
            'is_archived' => $article->is_archived,
            'created_at' => $article->created_at?->toIso8601String(),
            'updated_at' => $article->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArticleFull(BlogArticle $article): array
    {
        return array_merge($this->toArticleListItem($article), [
            'content' => $article->content,
            'section' => $article->relationLoaded('section') && $article->section !== null
                ? [
                    'id' => $article->section->id,
                    'name' => $article->section->name,
                    'parent' => $article->section->relationLoaded('parent') && $article->section->parent !== null
                        ? ['id' => $article->section->parent->id, 'name' => $article->section->parent->name]
                        : null,
                ]
                : null,
        ]);
    }

    /**
     * @param string[] $tags
     */
    private function syncTags(int $articleId, array $tags): void
    {
        BlogArticleTag::query()->where('blog_article_id', $articleId)->delete();

        $normalized = [];
        foreach ($tags as $tag) {
            $tag = trim((string) $tag);
            if ($tag !== '' && !in_array($tag, $normalized, true)) {
                $normalized[] = $tag;
            }
        }

        foreach ($normalized as $tag) {
            BlogArticleTag::query()->create([
                'blog_article_id' => $articleId,
                'tag' => $tag,
            ]);
        }
    }

    /**
     * @return int[]
     */
    private function resolveSectionIds(int $sectionId): array
    {
        $section = BlogSection::query()->find($sectionId);
        if ($section === null) {
            return [$sectionId];
        }

        if ($section->parent_id === null) {
            $childIds = BlogSection::query()
                ->where('parent_id', $sectionId)
                ->pluck('id')
                ->all();

            return array_merge([$sectionId], $childIds);
        }

        return [$sectionId];
    }

    /**
     * @return array<string, mixed>
     */
    private function toCommentNode(BlogComment $comment): array
    {
        return [
            'id' => $comment->id,
            'body' => $comment->body,
            'author' => $this->toCommentAuthor($comment),
            'created_at' => $comment->created_at?->toIso8601String(),
            'updated_at' => $comment->updated_at?->toIso8601String(),
            'replies' => $comment->relationLoaded('replies')
                ? $comment->replies->map(fn (BlogComment $reply) => [
                    'id' => $reply->id,
                    'body' => $reply->body,
                    'author' => $this->toCommentAuthor($reply),
                    'parent_id' => $reply->parent_id,
                    'created_at' => $reply->created_at?->toIso8601String(),
                    'updated_at' => $reply->updated_at?->toIso8601String(),
                ])->values()->all()
                : [],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function toAuthor(BlogArticle $article): ?array
    {
        if (!$article->relationLoaded('author') || $article->author === null) {
            return null;
        }

        return [
            'id' => $article->author->id,
            'first_name' => $article->author->first_name,
            'last_name' => $article->author->last_name,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function toCommentAuthor(BlogComment $comment): ?array
    {
        if (!$comment->relationLoaded('author') || $comment->author === null) {
            return null;
        }

        return [
            'id' => $comment->author->id,
            'first_name' => $comment->author->first_name,
            'last_name' => $comment->author->last_name,
        ];
    }
}
