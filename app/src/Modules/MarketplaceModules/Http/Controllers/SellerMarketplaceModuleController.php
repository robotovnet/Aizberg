<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Entities\Client;
use Modules\MarketplaceModules\Http\Requests\StoreMarketplaceModuleRequest;
use Modules\MarketplaceModules\Http\Requests\StoreModuleChangelogRequest;
use Modules\MarketplaceModules\Http\Requests\UpdateMarketplaceModuleRequest;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;

class SellerMarketplaceModuleController extends Controller
{
    public function __construct(
        private MarketplaceModuleRepository $repo
    ) {
    }

    public function store(StoreMarketplaceModuleRequest $request): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $data = $this->payload($request->validated());
        $tags = (array)$request->input('tags', []);

        $version = (string)$request->input('version');
        $release = Carbon::parse((string)$request->input('version_release_date'));
        $screenshots = $request->file('screenshots', []);

        $module = $this->repo->create(
            (int)$client->id,
            $data,
            $tags,
            $version,
            $release,
            $request->file('icon'),
            is_array($screenshots) ? $screenshots : []
        );

        return JsonResponse::successful([
            'id' => $module->id,
            'install_slug' => $module->install_slug,
            'module' => $this->repo->toFullCard($module->load('tags')),
        ]);
    }

    public function update(UpdateMarketplaceModuleRequest $request, int $id): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $module = $this->repo->findModuleForOwner($id, (int)$client->id);
        if ($module === null) {
            return JsonResponse::failed(['message' => 'Модуль не найден или нет прав.']);
        }

        $data = $this->payload($request->validated());
        $tags = (array)$request->input('tags', []);

        $version = (string)$request->input('version');
        $release = Carbon::parse((string)$request->input('version_release_date'));
        $screenshots = $request->file('screenshots', []);

        $updated = $this->repo->update(
            $module,
            $data,
            $tags,
            $version,
            $release,
            $request->file('icon'),
            is_array($screenshots) ? $screenshots : []
        );

        return JsonResponse::successful([
            'module' => $this->repo->toFullCard($updated->load('tags')),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $module = $this->repo->findModuleForOwner($id, (int)$client->id);
        if ($module === null) {
            return JsonResponse::failed(['message' => 'Модуль не найден или нет прав.']);
        }

        $this->repo->delete($module);

        return JsonResponse::successful();
    }

    public function archive(int $id): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $module = $this->repo->findModuleForOwner($id, (int)$client->id);
        if ($module === null) {
            return JsonResponse::failed(['message' => 'Модуль не найден или нет прав.']);
        }

        $this->repo->archive($module);

        return JsonResponse::successful(['is_archived' => true]);
    }

    public function destroyScreenshot(int $id, int $screenshotId): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $module = $this->repo->findModuleForOwner($id, (int)$client->id);
        if ($module === null) {
            return JsonResponse::failed(['message' => 'Модуль не найден или нет прав.']);
        }

        if (!$this->repo->deleteScreenshot($module, $screenshotId)) {
            return JsonResponse::failed(['message' => 'Скриншот не найден.']);
        }

        return JsonResponse::successful();
    }

    public function storeChangelog(StoreModuleChangelogRequest $request, int $id): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $module = $this->repo->findModuleForOwner($id, (int)$client->id);
        if ($module === null) {
            return JsonResponse::failed(['message' => 'Модуль не найден или нет прав.']);
        }

        $releaseDate = $request->filled('release_date')
            ? Carbon::parse((string)$request->input('release_date'))
            : null;
        $entry = $this->repo->upsertVersionChangelog(
            (int)$module->id,
            (string)$request->input('version'),
            (array)$request->input('changes'),
            $releaseDate
        );

        return JsonResponse::successful([
            'version' => $entry->version,
            'release_date' => $entry->release_date->toIso8601String(),
            'changes' => $entry->changes,
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function payload(array $validated): array
    {
        foreach (['tags', 'version', 'version_release_date', 'icon', 'screenshots'] as $key) {
            unset($validated[$key]);
        }

        return $validated;
    }
}
