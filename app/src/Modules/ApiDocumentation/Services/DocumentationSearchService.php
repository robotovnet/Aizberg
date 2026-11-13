<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Services;

use Illuminate\Support\Facades\Config;
use Modules\ApiDocumentation\Entities\ApiDocumentation;
use Modules\ApiDocumentation\Entities\ApiDocumentationSection;

class DocumentationSearchService
{
    public function __construct(
        private DocumentationBuilderService $builder
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function search(ApiDocumentation $documentation, string $query, ?string $requestedLocale): array
    {
        $query = trim($query);
        $pattern = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $query) . '%';
        $candidateLimit = max(1, (int) Config::get('apidocumentation.search_candidate_limit', 200));
        $resultLimit = max(1, (int) Config::get('apidocumentation.search_limit', 20));
        $locale = $this->builder->resolveLocale($documentation, $requestedLocale);

        $sections = ApiDocumentationSection::query()
            ->with('parent.parent')
            ->where('api_documentation_id', $documentation->id)
            ->where(function ($builder) use ($pattern) {
                $builder->where('title', 'like', $pattern)
                    ->orWhere('description_blocks', 'like', $pattern)
                    ->orWhere('endpoint_url', 'like', $pattern)
                    ->orWhere('request_body_schema', 'like', $pattern)
                    ->orWhere('request_headers', 'like', $pattern)
                    ->orWhere('request_parameters', 'like', $pattern)
                    ->orWhere('responses', 'like', $pattern);
            })
            ->limit($candidateLimit)
            ->get();

        $normalizedQuery = $this->lower($query);
        $results = [];

        foreach ($sections as $section) {
            $title = (string) $this->builder->localized(
                $section->title,
                $locale,
                $documentation->default_locale
            );
            $endpoint = trim((string) $section->http_method . ' ' . (string) $section->endpoint_url);
            $searchable = json_encode([
                $section->title,
                $section->description_blocks,
                $section->request_body_schema,
                $section->request_headers,
                $section->request_parameters,
                $section->responses,
            ], JSON_UNESCAPED_UNICODE) ?: '';

            $results[] = [
                'id' => (int) $section->id,
                'slug' => $section->slug,
                'level' => (int) $section->level,
                'title' => $title,
                'breadcrumb' => $this->breadcrumb($section, $locale, $documentation->default_locale),
                'endpoint' => $endpoint === '' ? null : [
                    'method' => $section->http_method,
                    'url' => $section->endpoint_url,
                ],
                'excerpt' => $this->excerpt($searchable, $query),
                'score' => $this->score($title, $endpoint, $searchable, $normalizedQuery),
            ];
        }

        usort($results, static function (array $left, array $right): int {
            return [$right['score'], -$right['level'], -$right['id']]
                <=> [$left['score'], -$left['level'], -$left['id']];
        });

        return [
            'query' => $query,
            'locale' => $locale,
            'items' => array_slice($results, 0, $resultLimit),
            'total' => count($results),
        ];
    }

    /**
     * @return string[]
     */
    private function breadcrumb(ApiDocumentationSection $section, string $locale, string $fallbackLocale): array
    {
        $breadcrumb = [];
        $current = $section;

        while ($current !== null) {
            array_unshift($breadcrumb, (string) $this->builder->localized(
                $current->title,
                $locale,
                $fallbackLocale
            ));
            $current = $current->parent;
        }

        return $breadcrumb;
    }

    private function score(string $title, string $endpoint, string $searchable, string $query): int
    {
        $title = $this->lower($title);
        $endpoint = $this->lower($endpoint);
        $searchable = $this->lower($searchable);
        $score = 0;

        if ($title === $query) {
            $score += 100;
        } elseif ($this->startsWith($title, $query)) {
            $score += 60;
        } elseif ($this->contains($title, $query)) {
            $score += 40;
        }

        if ($this->contains($endpoint, $query)) {
            $score += 30;
        }

        $score += min(20, substr_count($searchable, $query));

        return $score;
    }

    private function excerpt(string $searchable, string $query): ?string
    {
        // Переводы строк в Markdown-блоках приходят из JSON как экранированные \n.
        $plain = preg_replace('/\\\\[nrt]/u', ' ', $searchable) ?: '';
        $plain = preg_replace('/[{}\[\]"\\\\]+/u', ' ', $plain) ?: '';
        $plain = preg_replace('/\s+/u', ' ', $plain) ?: '';
        $position = $this->position($this->lower($plain), $this->lower($query));
        if ($position === false) {
            return null;
        }

        $start = max(0, $position - 80);
        $excerpt = trim($this->substring($plain, $start, 240));

        return ($start > 0 ? '…' : '') . $excerpt . ($this->length($plain) > $start + 240 ? '…' : '');
    }

    private function lower(string $value): string
    {
        return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
    }

    private function contains(string $haystack, string $needle): bool
    {
        return $needle !== '' && $this->position($haystack, $needle) !== false;
    }

    private function startsWith(string $haystack, string $needle): bool
    {
        return $needle !== '' && $this->position($haystack, $needle) === 0;
    }

    /** @return int|false */
    private function position(string $haystack, string $needle)
    {
        return function_exists('mb_strpos') ? mb_strpos($haystack, $needle, 0, 'UTF-8') : strpos($haystack, $needle);
    }

    private function substring(string $value, int $start, int $length): string
    {
        return function_exists('mb_substr')
            ? mb_substr($value, $start, $length, 'UTF-8')
            : substr($value, $start, $length);
    }

    private function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
    }
}
