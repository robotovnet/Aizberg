<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Services;

use DomainException;
use Modules\ApiDocumentation\Entities\ApiDocumentation;
use Modules\ApiDocumentation\Entities\ApiDocumentationSection;

class SectionHierarchyService
{
    public const MAX_LEVEL = 3;

    public function resolveLevel(
        ApiDocumentation $documentation,
        ?int $parentId,
        ?ApiDocumentationSection $section = null
    ): int {
        if ($parentId === null) {
            $level = 1;
        } else {
            $parent = ApiDocumentationSection::query()
                ->where('api_documentation_id', $documentation->id)
                ->find($parentId);

            if ($parent === null) {
                throw new DomainException('Родительский раздел не найден в этой документации.');
            }

            if ($section !== null && $parent->id === $section->id) {
                throw new DomainException('Раздел не может быть родителем самому себе.');
            }

            if ($section !== null && $this->isDescendant($parent, $section)) {
                throw new DomainException('Нельзя переместить раздел внутрь его дочернего раздела.');
            }

            $level = (int) $parent->level + 1;
        }

        $subtreeDepth = $section === null ? 1 : $this->subtreeDepth($section);
        if ($level + $subtreeDepth - 1 > self::MAX_LEVEL) {
            throw new DomainException('Документация поддерживает не более трёх уровней разделов.');
        }

        return $level;
    }

    public function updateDescendantLevels(ApiDocumentationSection $section, int $oldLevel): void
    {
        $delta = (int) $section->level - $oldLevel;
        if ($delta === 0) {
            return;
        }

        $parentIds = [(int) $section->id];
        while ($parentIds !== []) {
            $children = ApiDocumentationSection::query()
                ->where('api_documentation_id', $section->api_documentation_id)
                ->whereIn('parent_id', $parentIds)
                ->get(['id', 'level']);

            if ($children->isEmpty()) {
                break;
            }

            foreach ($children as $child) {
                $child->level = (int) $child->level + $delta;
                $child->save();
            }

            $parentIds = $children->pluck('id')->map(static fn ($id) => (int) $id)->all();
        }
    }

    private function isDescendant(
        ApiDocumentationSection $candidate,
        ApiDocumentationSection $section
    ): bool {
        $parentId = $candidate->parent_id;

        while ($parentId !== null) {
            if ((int) $parentId === (int) $section->id) {
                return true;
            }

            $parentId = ApiDocumentationSection::query()->whereKey($parentId)->value('parent_id');
        }

        return false;
    }

    private function subtreeDepth(ApiDocumentationSection $section): int
    {
        $sections = ApiDocumentationSection::query()
            ->where('api_documentation_id', $section->api_documentation_id)
            ->get(['id', 'parent_id']);

        $childrenByParent = $sections->groupBy(static fn (ApiDocumentationSection $item) => (int) $item->parent_id);

        $depth = function (int $sectionId) use (&$depth, $childrenByParent): int {
            $children = $childrenByParent->get($sectionId);
            if ($children === null || $children->isEmpty()) {
                return 1;
            }

            $maxChildDepth = 0;
            foreach ($children as $child) {
                $maxChildDepth = max($maxChildDepth, $depth((int) $child->id));
            }

            return 1 + $maxChildDepth;
        };

        return $depth((int) $section->id);
    }
}
