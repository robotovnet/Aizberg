<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\ApiDocumentation\Entities\ApiDocumentation;
use Modules\ApiDocumentation\Entities\ApiDocumentationSection;
use Modules\ApiDocumentation\Services\SectionHierarchyService;

class ApiDocumentationRepository
{
    public function __construct(
        private SectionHierarchyService $hierarchy
    ) {
    }

    public function findForOwner(int $id, int $clientId): ?ApiDocumentation
    {
        return ApiDocumentation::query()
            ->where('client_id', $clientId)
            ->find($id);
    }

    public function findPublicBySlug(string $slug): ?ApiDocumentation
    {
        return ApiDocumentation::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(int $clientId, array $data): ApiDocumentation
    {
        $documentation = new ApiDocumentation($data);
        $documentation->client_id = $clientId;
        $documentation->is_published = $data['is_published'] ?? true;
        $documentation->tags = array_values(array_unique($data['tags'] ?? []));
        $documentation->save();

        return $documentation->fresh();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(ApiDocumentation $documentation, array $data): ApiDocumentation
    {
        foreach (['slug', 'name', 'short_description', 'default_locale', 'is_published'] as $field) {
            if (array_key_exists($field, $data)) {
                $documentation->{$field} = $data[$field];
            }
        }

        if (array_key_exists('tags', $data)) {
            $documentation->tags = array_values(array_unique($data['tags'] ?? []));
        }

        $documentation->save();

        return $documentation->fresh();
    }

    public function delete(ApiDocumentation $documentation): void
    {
        $documentation->delete();
    }

    /**
     * @return LengthAwarePaginator<int, ApiDocumentation>
     */
    public function paginatePublic(?string $search, int $page): LengthAwarePaginator
    {
        $query = ApiDocumentation::query()
            ->where('is_published', true)
            ->orderByDesc('updated_at');

        $this->applyDocumentationSearch($query, $search);

        return $query->paginate(
            max(1, (int) Config::get('apidocumentation.per_page', 20)),
            ['*'],
            'page',
            $page
        );
    }

    /**
     * @return LengthAwarePaginator<int, ApiDocumentation>
     */
    public function paginateForOwner(int $clientId, ?string $search, int $page): LengthAwarePaginator
    {
        $query = ApiDocumentation::query()
            ->where('client_id', $clientId)
            ->orderByDesc('updated_at');

        $this->applyDocumentationSearch($query, $search);

        return $query->paginate(
            max(1, (int) Config::get('apidocumentation.per_page', 20)),
            ['*'],
            'page',
            $page
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createSection(ApiDocumentation $documentation, array $data): ApiDocumentationSection
    {
        return DB::transaction(function () use ($documentation, $data) {
            $parentId = isset($data['parent_id']) ? (int) $data['parent_id'] : null;
            $level = $this->hierarchy->resolveLevel($documentation, $parentId);

            $section = new ApiDocumentationSection(
                $this->sectionAttributes($data) + [
                    'api_documentation_id' => (int) $documentation->id,
                    'parent_id' => $parentId,
                    'level' => $level,
                ]
            );
            $section->save();

            return $section->fresh();
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateSection(
        ApiDocumentation $documentation,
        ApiDocumentationSection $section,
        array $data
    ): ApiDocumentationSection {
        return DB::transaction(function () use ($documentation, $section, $data) {
            $oldLevel = (int) $section->level;
            $parentId = array_key_exists('parent_id', $data)
                ? ($data['parent_id'] === null ? null : (int) $data['parent_id'])
                : ($section->parent_id === null ? null : (int) $section->parent_id);

            $level = $this->hierarchy->resolveLevel($documentation, $parentId, $section);
            $section->fill($this->sectionAttributes($data));
            $section->parent_id = $parentId;
            $section->level = $level;
            $section->save();

            $this->hierarchy->updateDescendantLevels($section, $oldLevel);

            return $section->fresh();
        });
    }

    public function findSection(ApiDocumentation $documentation, int $sectionId): ?ApiDocumentationSection
    {
        return ApiDocumentationSection::query()
            ->where('api_documentation_id', $documentation->id)
            ->find($sectionId);
    }

    public function deleteSection(ApiDocumentationSection $section): void
    {
        $section->delete();
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function sectionAttributes(array $data): array
    {
        $attributes = [];
        foreach (
            [
                'slug',
                'title',
                'position',
                'description_blocks',
                'request_headers',
                'request_parameters',
                'request_example',
                'responses',
            ] as $field
        ) {
            if (array_key_exists($field, $data)) {
                $attributes[$field] = $data[$field];
            }
        }

        if (array_key_exists('endpoint', $data)) {
            $attributes['http_method'] = $data['endpoint']['method'] ?? null;
            $attributes['endpoint_url'] = $data['endpoint']['url'] ?? null;
        }

        if (array_key_exists('request_body', $data)) {
            $attributes['request_body_content_type'] = $data['request_body']['content_type'] ?? null;
            $attributes['request_body_schema'] = $data['request_body']['schema'] ?? null;
        }

        return $attributes;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<ApiDocumentation> $query
     */
    private function applyDocumentationSearch($query, ?string $search): void
    {
        $search = trim((string) $search);
        if ($search === '') {
            return;
        }

        $pattern = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search) . '%';
        $query->where(function ($query) use ($pattern) {
            $query->where('name', 'like', $pattern)
                ->orWhere('short_description', 'like', $pattern)
                ->orWhere('tags', 'like', $pattern)
                ->orWhere('slug', 'like', $pattern);
        });
    }
}
