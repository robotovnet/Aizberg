<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Http\Controllers;

use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\ApiDocumentation\Http\Requests\SaveDocumentationSectionRequest;
use Modules\ApiDocumentation\Repositories\ApiDocumentationRepository;
use Modules\ApiDocumentation\Services\DocumentationBuilderService;
use Modules\Client\Entities\Client;

class DocumentationSectionController extends Controller
{
    public function __construct(
        private ApiDocumentationRepository $repository,
        private DocumentationBuilderService $builder
    ) {
    }

    public function store(SaveDocumentationSectionRequest $request, int $documentationId): JsonResponse
    {
        $documentation = $this->repository->findForOwner($documentationId, $this->clientId());
        if ($documentation === null) {
            return JsonResponse::failed(['message' => 'Документация не найдена или нет прав.']);
        }

        try {
            $section = $this->repository->createSection($documentation, $request->validated());
        } catch (DomainException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422, [], JSON_UNESCAPED_UNICODE);
        }

        return JsonResponse::successful([
            'section' => $this->builder->formatSection(
                $section,
                $documentation->default_locale,
                $documentation->default_locale,
                true
            ),
        ]);
    }

    public function update(
        SaveDocumentationSectionRequest $request,
        int $documentationId,
        int $sectionId
    ): JsonResponse {
        $documentation = $this->repository->findForOwner($documentationId, $this->clientId());
        if ($documentation === null) {
            return JsonResponse::failed(['message' => 'Документация не найдена или нет прав.']);
        }

        $section = $this->repository->findSection($documentation, $sectionId);
        if ($section === null) {
            return JsonResponse::failed(['message' => 'Раздел не найден в документации.']);
        }

        try {
            $section = $this->repository->updateSection($documentation, $section, $request->validated());
        } catch (DomainException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422, [], JSON_UNESCAPED_UNICODE);
        }

        return JsonResponse::successful([
            'section' => $this->builder->formatSection(
                $section,
                $documentation->default_locale,
                $documentation->default_locale,
                true
            ),
        ]);
    }

    public function destroy(int $documentationId, int $sectionId): JsonResponse
    {
        $documentation = $this->repository->findForOwner($documentationId, $this->clientId());
        if ($documentation === null) {
            return JsonResponse::failed(['message' => 'Документация не найдена или нет прав.']);
        }

        $section = $this->repository->findSection($documentation, $sectionId);
        if ($section === null) {
            return JsonResponse::failed(['message' => 'Раздел не найден в документации.']);
        }

        $this->repository->deleteSection($section);

        return JsonResponse::successful();
    }

    private function clientId(): int
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        return (int) $client->id;
    }
}
