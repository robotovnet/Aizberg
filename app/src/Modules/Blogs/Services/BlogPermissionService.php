<?php

declare(strict_types=1);

namespace Modules\Blogs\Services;

use Modules\Blogs\Entities\BlogArticle;
use Modules\Blogs\Entities\BlogComment;
use Modules\Blogs\Entities\BlogModeratorPermission;
use Modules\Blogs\Entities\BlogSectionPermission;

class BlogPermissionService
{
    public function canManageSections(int $clientId): bool
    {
        return BlogSectionPermission::query()->where('client_id', $clientId)->exists();
    }

    public function isModerator(int $clientId): bool
    {
        return BlogModeratorPermission::query()->where('client_id', $clientId)->exists();
    }

    public function canEditArticle(BlogArticle $article, int $clientId): bool
    {
        return (int) $article->client_id === $clientId || $this->isModerator($clientId);
    }

    public function canEditComment(BlogComment $comment, int $clientId): bool
    {
        return (int) $comment->client_id === $clientId || $this->isModerator($clientId);
    }
}
