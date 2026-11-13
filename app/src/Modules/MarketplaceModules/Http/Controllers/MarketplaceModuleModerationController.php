<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Entities\Client;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;
use Modules\MarketplaceModules\Services\MarketplaceModulePermissionService;

class MarketplaceModuleModerationController extends Controller
{
    public function __construct(
        private MarketplaceModuleRepository $repo,
        private MarketplaceModulePermissionService $permissions
    ) {
    }

    public function block(int $id): JsonResponse
    {
        $moderator = $this->moderator();
        if ($moderator === null) {
            return JsonResponse::failed(['message' => 'Нет прав модератора.']);
        }

        $module = $this->repo->findModule($id);
        if ($module === null) {
            return JsonResponse::failed(['message' => 'Модуль не найден.']);
        }

        $blocked = $this->repo->block($module, (int) $moderator->id);

        return JsonResponse::successful([
            'module' => $this->repo->toFullCard($blocked),
        ]);
    }

    public function unblock(int $id): JsonResponse
    {
        if ($this->moderator() === null) {
            return JsonResponse::failed(['message' => 'Нет прав модератора.']);
        }

        $module = $this->repo->findModule($id);
        if ($module === null) {
            return JsonResponse::failed(['message' => 'Модуль не найден.']);
        }

        $unblocked = $this->repo->unblock($module);

        return JsonResponse::successful([
            'module' => $this->repo->toFullCard($unblocked),
        ]);
    }

    private function moderator(): ?Client
    {
        /** @var Client|null $client */
        $client = Auth::guard('client')->user();
        if ($client === null || !$this->permissions->canModerate((int) $client->id)) {
            return null;
        }

        return $client;
    }
}
