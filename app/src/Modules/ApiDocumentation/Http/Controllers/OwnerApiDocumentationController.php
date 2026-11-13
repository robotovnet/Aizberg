<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\ApiDocumentation\Entities\ApiDocumentation;
use Modules\ApiDocumentation\Http\Requests\SaveApiDocumentationRequest;
use Modules\ApiDocumentation\Repositories\ApiDocumentationRepository;
use Modules\ApiDocumentation\Services\DocumentationBuilderService;
use Modules\Client\Entities\Client;

class OwnerApiDocumentationController extends Controller
{
    public function __construct(
        private ApiDocumentationRepository $repository,
        private DocumentationBuilderService $builder
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $paginator = $this->repository->paginateForOwner(
            $this->clientId(),
            $validated['q'] ?? null,
            (int) ($validated['page'] ?? 1)
        );
        $items = $paginator->getCollection()
            ->map(fn (ApiDocumentation $documentation) => $this->builder->summary($documentation, null, true))
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

    public function show(int $id): JsonResponse
    {
        $documentation = $this->repository->findForOwner($id, $this->clientId());
        if ($documentation === null) {
            return JsonResponse::failed(['message' => 'Документация не найдена или нет прав.']);
        }

        $documentation->load('sections');

        return JsonResponse::successful($this->builder->build($documentation, null, true));
    }

    public function store(SaveApiDocumentationRequest $request): JsonResponse
    {
        $documentation = $this->repository->create($this->clientId(), $request->validated());
        $documentation->setRelation('sections', collect());

        return JsonResponse::successful([
            'id' => (int) $documentation->id,
            'slug' => $documentation->slug,
            'documentation' => $this->builder->build($documentation, null, true),
        ]);
    }

    public function update(SaveApiDocumentationRequest $request, int $id): JsonResponse
    {
        $documentation = $this->repository->findForOwner($id, $this->clientId());
        if ($documentation === null) {
            return JsonResponse::failed(['message' => 'Документация не найдена или нет прав.']);
        }

        $documentation = $this->repository->update($documentation, $request->validated());
        $documentation->load('sections');

        return JsonResponse::successful([
            'documentation' => $this->builder->build($documentation, null, true),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $documentation = $this->repository->findForOwner($id, $this->clientId());
        if ($documentation === null) {
            return JsonResponse::failed(['message' => 'Документация не найдена или нет прав.']);
        }

        $this->repository->delete($documentation);

        return JsonResponse::successful();
    }

    private function clientId(): int
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        return (int) $client->id;
    }
}
