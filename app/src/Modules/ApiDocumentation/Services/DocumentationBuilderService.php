<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Services;

use Illuminate\Support\Collection;
use Modules\ApiDocumentation\Entities\ApiDocumentation;
use Modules\ApiDocumentation\Entities\ApiDocumentationSection;

class DocumentationBuilderService
{
    /**
     * @return array<string, mixed>
     */
    public function build(
        ApiDocumentation $documentation,
        ?string $requestedLocale = null,
        bool $allTranslations = false
    ): array {
        if (!$documentation->relationLoaded('sections')) {
            $documentation->load('sections');
        }

        $locale = $this->resolveLocale($documentation, $requestedLocale);
        $sections = $documentation->sections;

        return [
            'id' => (int) $documentation->id,
            'slug' => $documentation->slug,
            'name' => $this->localized($documentation->name, $locale, $documentation->default_locale, $allTranslations),
            'short_description' => $this->localized(
                $documentation->short_description,
                $locale,
                $documentation->default_locale,
                $allTranslations
            ),
            'tags' => $documentation->tags ?? [],
            'locale' => $allTranslations ? null : $locale,
            'default_locale' => $documentation->default_locale,
            'available_locales' => $this->availableLocales($documentation, $sections),
            'is_published' => (bool) $documentation->is_published,
            'sections' => $this->sectionTree(
                $sections,
                $locale,
                $documentation->default_locale,
                $allTranslations
            ),
            'created_at' => optional($documentation->created_at)->toAtomString(),
            'updated_at' => optional($documentation->updated_at)->toAtomString(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function summary(
        ApiDocumentation $documentation,
        ?string $requestedLocale = null,
        bool $allTranslations = false
    ): array {
        $locale = $this->resolveLocale($documentation, $requestedLocale);

        return [
            'id' => (int) $documentation->id,
            'slug' => $documentation->slug,
            'name' => $this->localized($documentation->name, $locale, $documentation->default_locale, $allTranslations),
            'short_description' => $this->localized(
                $documentation->short_description,
                $locale,
                $documentation->default_locale,
                $allTranslations
            ),
            'tags' => $documentation->tags ?? [],
            'locale' => $allTranslations ? null : $locale,
            'default_locale' => $documentation->default_locale,
            'is_published' => (bool) $documentation->is_published,
            'updated_at' => optional($documentation->updated_at)->toAtomString(),
        ];
    }

    public function resolveLocale(ApiDocumentation $documentation, ?string $requestedLocale): string
    {
        $available = array_keys(is_array($documentation->name) ? $documentation->name : []);
        $requestedLocale = $requestedLocale ?: (string) app()->getLocale();

        if (in_array($requestedLocale, $available, true)) {
            return $requestedLocale;
        }

        if (in_array($documentation->default_locale, $available, true)) {
            return $documentation->default_locale;
        }

        return (string) ($available[0] ?? $documentation->default_locale ?: 'en');
    }

    /**
     * @return array<string, mixed>
     */
    public function formatSection(
        ApiDocumentationSection $section,
        string $locale,
        string $fallbackLocale,
        bool $allTranslations = false
    ): array {
        $endpoint = null;
        if ($section->http_method !== null || $section->endpoint_url !== null) {
            $endpoint = [
                'method' => $section->http_method,
                'url' => $section->endpoint_url,
            ];
        }

        $requestBody = null;
        if ($section->request_body_content_type !== null || $section->request_body_schema !== null) {
            $requestBody = [
                'content_type' => $section->request_body_content_type,
                'schema' => $section->request_body_schema,
            ];
        }

        return [
            'id' => (int) $section->id,
            'parent_id' => $section->parent_id === null ? null : (int) $section->parent_id,
            'slug' => $section->slug,
            'level' => (int) $section->level,
            'position' => (int) $section->position,
            'title' => $this->localized($section->title, $locale, $fallbackLocale, $allTranslations),
            'description_blocks' => $this->localizeDescriptionBlocks(
                $section->description_blocks ?? [],
                $locale,
                $fallbackLocale,
                $allTranslations
            ),
            'endpoint' => $endpoint,
            'request_body' => $requestBody,
            'request_headers' => $this->localizeParameters(
                $section->request_headers ?? [],
                $locale,
                $fallbackLocale,
                $allTranslations
            ),
            'request_parameters' => $this->localizeParameters(
                $section->request_parameters ?? [],
                $locale,
                $fallbackLocale,
                $allTranslations
            ),
            'request_example' => $section->request_example,
            'responses' => $this->localizeResponses(
                $section->responses ?? [],
                $locale,
                $fallbackLocale,
                $allTranslations
            ),
            'children' => [],
        ];
    }

    /**
     * @param mixed $translations
     * @return mixed
     */
    public function localized($translations, string $locale, string $fallbackLocale, bool $allTranslations = false)
    {
        if ($allTranslations || !is_array($translations)) {
            return $translations;
        }

        if (array_key_exists($locale, $translations)) {
            return $translations[$locale];
        }

        if (array_key_exists($fallbackLocale, $translations)) {
            return $translations[$fallbackLocale];
        }

        return reset($translations) ?: null;
    }

    /**
     * @param Collection<int, ApiDocumentationSection> $sections
     * @return array<int, array<string, mixed>>
     */
    private function sectionTree(
        Collection $sections,
        string $locale,
        string $fallbackLocale,
        bool $allTranslations
    ): array {
        $nodes = [];
        foreach ($sections as $section) {
            $nodes[(int) $section->id] = $this->formatSection(
                $section,
                $locale,
                $fallbackLocale,
                $allTranslations
            );
        }

        $tree = [];
        foreach ($sections as $section) {
            $id = (int) $section->id;
            $parentId = $section->parent_id === null ? null : (int) $section->parent_id;

            if ($parentId !== null && isset($nodes[$parentId])) {
                $nodes[$parentId]['children'][] = &$nodes[$id];
            } else {
                $tree[] = &$nodes[$id];
            }
        }
        return $tree;
    }

    /**
     * @param Collection<int, ApiDocumentationSection> $sections
     * @return string[]
     */
    private function availableLocales(ApiDocumentation $documentation, Collection $sections): array
    {
        $locales = array_keys(is_array($documentation->name) ? $documentation->name : []);
        foreach ($sections as $section) {
            $locales = array_merge($locales, array_keys(is_array($section->title) ? $section->title : []));
        }

        $locales = array_values(array_unique($locales));
        sort($locales);

        return $locales;
    }

    /**
     * @param array<int, mixed> $blocks
     * @return array<int, mixed>
     */
    private function localizeDescriptionBlocks(
        array $blocks,
        string $locale,
        string $fallbackLocale,
        bool $allTranslations
    ): array {
        foreach ($blocks as &$block) {
            if (!is_array($block)) {
                continue;
            }

            foreach (['title', 'content', 'label', 'description'] as $field) {
                if (array_key_exists($field, $block)) {
                    $block[$field] = $this->localized(
                        $block[$field],
                        $locale,
                        $fallbackLocale,
                        $allTranslations
                    );
                }
            }

            if (isset($block['columns']) && is_array($block['columns'])) {
                foreach ($block['columns'] as &$column) {
                    if (is_array($column) && array_key_exists('title', $column)) {
                        $column['title'] = $this->localized(
                            $column['title'],
                            $locale,
                            $fallbackLocale,
                            $allTranslations
                        );
                    }
                }
                unset($column);
            }

            if (isset($block['rows']) && is_array($block['rows'])) {
                foreach ($block['rows'] as &$row) {
                    if (!is_array($row) || !isset($row['cells']) || !is_array($row['cells'])) {
                        continue;
                    }
                    foreach ($row['cells'] as &$cell) {
                        $cell = $this->localized($cell, $locale, $fallbackLocale, $allTranslations);
                    }
                    unset($cell);
                }
                unset($row);
            }
        }
        unset($block);

        return $blocks;
    }

    /**
     * @param array<int, mixed> $parameters
     * @return array<int, mixed>
     */
    private function localizeParameters(
        array $parameters,
        string $locale,
        string $fallbackLocale,
        bool $allTranslations
    ): array {
        foreach ($parameters as &$parameter) {
            if (is_array($parameter) && array_key_exists('description', $parameter)) {
                $parameter['description'] = $this->localized(
                    $parameter['description'],
                    $locale,
                    $fallbackLocale,
                    $allTranslations
                );
            }
        }
        unset($parameter);

        return $parameters;
    }

    /**
     * @param array<int, mixed> $responses
     * @return array<int, mixed>
     */
    private function localizeResponses(
        array $responses,
        string $locale,
        string $fallbackLocale,
        bool $allTranslations
    ): array {
        foreach ($responses as &$response) {
            if (!is_array($response)) {
                continue;
            }
            foreach (['name', 'description'] as $field) {
                if (array_key_exists($field, $response)) {
                    $response[$field] = $this->localized(
                        $response[$field],
                        $locale,
                        $fallbackLocale,
                        $allTranslations
                    );
                }
            }
        }
        unset($response);

        return $responses;
    }
}
