<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Services;

use Modules\MarketplaceModules\Entities\MarketplaceModuleModeratorPermission;

class MarketplaceModulePermissionService
{
    public function canModerate(int $clientId): bool
    {
        return MarketplaceModuleModeratorPermission::query()
            ->where('client_id', $clientId)
            ->exists();
    }
}
