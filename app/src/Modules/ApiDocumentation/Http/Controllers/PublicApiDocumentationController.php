<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ApiDocumentation\Entities\ApiDocumentation;
use Modules\ApiDocumentation\Repositories\ApiDocumentationRepository;
use Modules\ApiDocumentation\Services\DocumentationBuilderService;
use Modules\ApiDocumentation\Services\DocumentationSearchService;

class PublicApiDocumentationController extends Controller
{
    public function __construct(
        private ApiDocumentationRepository $repository,
        private DocumentationBuilderService $builder,
        private DocumentationSearchService $searchService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'locale' => ['nullable', 'string', 'max:16', 'regex:/^[A-Za-z]{2,3}(?:[-_][A-Za-z0-9]{2,8})*$/'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $paginator = $this->repository->paginatePublic(
            $validated['q'] ?? null,
            (int) ($validated['page'] ?? 1)
        );
        $locale = $validated['locale'] ?? null;
        $items = $paginator->getCollection()
            ->map(fn (ApiDocumentation $documentation) => $this->builder->summary($documentation, $locale))
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

    public function searchDocumentations(Request $request): JsonResponse
    {
        $request->validate(['q' => ['required', 'string', 'min:2', 'max:255']]);

        return $this->index($request);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $validated = $request->validate([
            'locale' => ['nullable', 'string', 'max:16', 'regex:/^[A-Za-z]{2,3}(?:[-_][A-Za-z0-9]{2,8})*$/'],
        ]);
        $documentation = $this->repository->findPublicBySlug($slug);
        if ($documentation === null) {
            return response()->json(
                ['message' => 'Документация не найдена.'],
                404,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }

        $documentation->load('sections');

        return JsonResponse::successful(
            $this->builder->build($documentation, $validated['locale'] ?? null)
        );
    }

    public function search(Request $request, string $slug): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:255'],
            'locale' => ['nullable', 'string', 'max:16', 'regex:/^[A-Za-z]{2,3}(?:[-_][A-Za-z0-9]{2,8})*$/'],
        ]);
        $documentation = $this->repository->findPublicBySlug($slug);
        if ($documentation === null) {
            return response()->json(
                ['message' => 'Документация не найдена.'],
                404,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }

        return JsonResponse::successful(
            $this->searchService->search(
                $documentation,
                $validated['q'],
                $validated['locale'] ?? null
            )
        );
    }
}
