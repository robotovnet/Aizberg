<?php

declare(strict_types=1);

namespace Modules\Showcase\Services;

use Modules\Showcase\Entities\ShowcaseExample;
use Modules\Showcase\Entities\ShowcaseModeratorPermission;

class ShowcasePermissionService
{
    public function isModerator(int $clientId): bool
    {
        return ShowcaseModeratorPermission::query()->where('client_id', $clientId)->exists();
    }

    public function canEditOwnExample(ShowcaseExample $example, int $clientId): bool
    {
        if ((int) $example->client_id !== $clientId) {
            return false;
        }

        return $example->isPending() || $example->moderation_status === ShowcaseExample::STATUS_REJECTED;
    }

    public function canModerate(int $clientId): bool
    {
        return $this->isModerator($clientId);
    }
}
