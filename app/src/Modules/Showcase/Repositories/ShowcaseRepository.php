<?php

declare(strict_types=1);

namespace Modules\Showcase\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Showcase\Entities\ShowcaseCategory;
use Modules\Showcase\Entities\ShowcaseExample;
use Modules\Showcase\Entities\ShowcaseExampleImage;
use Modules\Showcase\Entities\ShowcaseExampleModule;
use Modules\Showcase\Services\ShowcaseImageService;
use RuntimeException;

class ShowcaseRepository
{
    public function __construct(
        private ShowcaseImageService $imageService
    ) {
    }

    /**
     * @return Collection<int, ShowcaseCategory>
     */
    public function allCategories(): Collection
    {
        return ShowcaseCategory::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function findCategory(int $id): ?ShowcaseCategory
    {
        return ShowcaseCategory::query()->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createCategory(array $data): ShowcaseCategory
    {
        $slug = isset($data['slug']) && $data['slug'] !== ''
            ? (string) $data['slug']
            : $this->generateUniqueSlug((string) $data['name']);

        $category = new ShowcaseCategory([
            'name' => $data['name'],
            'slug' => $slug,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
        $category->save();

        return $category->fresh();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateCategory(ShowcaseCategory $category, array $data): ShowcaseCategory
    {
        if (array_key_exists('name', $data)) {
            $category->name = $data['name'];
        }
        if (array_key_exists('slug', $data)) {
            $category->slug = $data['slug'] !== null && $data['slug'] !== ''
                ? (string) $data['slug']
                : $this->generateUniqueSlug($category->name, $category->id);
        }
        if (array_key_exists('sort_order', $data)) {
            $category->sort_order = (int) $data['sort_order'];
        }

        $category->save();

        return $category->fresh();
    }

    public function deleteCategory(ShowcaseCategory $category): void
    {
        if ($category->examples()->exists()) {
            throw new RuntimeException('Нельзя удалить категорию, в которой есть примеры сайтов.');
        }

        $category->delete();
    }

    /**
     * @return string[]
     */
    public function usedModuleNames(bool $approvedOnly = true): array
    {
        $query = ShowcaseExampleModule::query()
            ->select('showcase_example_modules.module_name')
            ->distinct()
            ->orderBy('showcase_example_modules.module_name');

        if ($approvedOnly) {
            $query->join('showcase_examples', 'showcase_examples.id', '=', 'showcase_example_modules.showcase_example_id')
                ->where('showcase_examples.moderation_status', ShowcaseExample::STATUS_APPROVED)
                ->whereNull('showcase_examples.deleted_at');
        }

        return $query->pluck('module_name')->values()->all();
    }

    /**
     * @return LengthAwarePaginator<int, ShowcaseExample>
     */
    public function paginatePublicExamples(int $page, ?int $categoryId = null, ?string $moduleName = null): LengthAwarePaginator
    {
        $perPage = (int) Config::get('showcase.examples_per_page', 20);

        $query = $this->baseExampleQuery()
            ->where('moderation_status', ShowcaseExample::STATUS_APPROVED);

        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }

        if ($moduleName !== null && $moduleName !== '') {
            $query->whereHas('modules', fn ($q) => $q->where('module_name', $moduleName));
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @return LengthAwarePaginator<int, ShowcaseExample>
     */
    public function paginatePendingExamples(int $page): LengthAwarePaginator
    {
        $perPage = (int) Config::get('showcase.examples_per_page', 20);

        return $this->baseExampleQuery()
            ->where('moderation_status', ShowcaseExample::STATUS_PENDING)
            ->orderBy('created_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @return LengthAwarePaginator<int, ShowcaseExample>
     */
    public function paginateClientExamples(int $clientId, int $page): LengthAwarePaginator
    {
        $perPage = (int) Config::get('showcase.examples_per_page', 20);

        return $this->baseExampleQuery()
            ->where('client_id', $clientId)
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function findApprovedExample(int $id): ?ShowcaseExample
    {
        return $this->baseExampleQuery()
            ->where('moderation_status', ShowcaseExample::STATUS_APPROVED)
            ->find($id);
    }

    public function findExample(int $id): ?ShowcaseExample
    {
        return $this->baseExampleQuery()->find($id);
    }

    /**
     * @param string[] $modules
     * @param UploadedFile[] $images
     * @param array<string, mixed> $data
     */
    public function createExample(int $clientId, array $data, array $modules, array $images): ShowcaseExample
    {
        return DB::transaction(function () use ($clientId, $data, $modules, $images) {
            $example = new ShowcaseExample([
                'client_id' => $clientId,
                'category_id' => (int) $data['category_id'],
                'website_url' => $data['website_url'],
                'name' => $data['name'],
                'short_description' => $data['short_description'],
                'moderation_status' => ShowcaseExample::STATUS_PENDING,
            ]);
            $example->save();

            $this->syncModules($example->id, $modules);
            $this->storeImages($example->id, $images);

            return $example->fresh([
                'category',
                'author:id,first_name,last_name',
                'images',
                'modules',
                'approver:id,first_name,last_name',
            ]);
        });
    }

    /**
     * @param string[]|null $modules
     * @param UploadedFile[]|null $images
     * @param int[]|null $removeImageIds
     * @param array<string, mixed> $data
     */
    public function updateExample(
        ShowcaseExample $example,
        array $data,
        ?array $modules = null,
        ?array $images = null,
        ?array $removeImageIds = null,
        bool $resetToPending = false
    ): ShowcaseExample {
        return DB::transaction(function () use ($example, $data, $modules, $images, $removeImageIds, $resetToPending) {
            foreach (['category_id', 'website_url', 'name', 'short_description'] as $field) {
                if (array_key_exists($field, $data)) {
                    $example->{$field} = $data[$field];
                }
            }

            if ($resetToPending) {
                $example->moderation_status = ShowcaseExample::STATUS_PENDING;
                $example->approved_at = null;
                $example->approved_by_client_id = null;
            }

            $example->save();

            if ($modules !== null) {
                $this->syncModules($example->id, $modules);
            }

            if ($removeImageIds !== null && $removeImageIds !== []) {
                $this->removeImagesByIds($example->id, $removeImageIds);
            }

            if ($images !== null && $images !== []) {
                $this->storeImages($example->id, $images);
            }

            return $example->fresh([
                'category',
                'author:id,first_name,last_name',
                'images',
                'modules',
                'approver:id,first_name,last_name',
            ]);
        });
    }

    public function approveExample(ShowcaseExample $example, int $moderatorClientId): ShowcaseExample
    {
        $example->moderation_status = ShowcaseExample::STATUS_APPROVED;
        $example->approved_at = now();
        $example->approved_by_client_id = $moderatorClientId;
        $example->save();

        return $example->fresh([
            'category',
            'author:id,first_name,last_name',
            'images',
            'modules',
            'approver:id,first_name,last_name',
        ]);
    }

    public function rejectExample(ShowcaseExample $example, int $moderatorClientId): ShowcaseExample
    {
        $example->moderation_status = ShowcaseExample::STATUS_REJECTED;
        $example->approved_at = null;
        $example->approved_by_client_id = $moderatorClientId;
        $example->save();

        return $example->fresh([
            'category',
            'author:id,first_name,last_name',
            'images',
            'modules',
            'approver:id,first_name,last_name',
        ]);
    }

    public function deleteExample(ShowcaseExample $example): void
    {
        DB::transaction(function () use ($example) {
            foreach ($example->images as $image) {
                $this->imageService->deleteByStoredPath($image->image_path);
            }

            $example->delete();
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function toCategoryItem(ShowcaseCategory $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'sort_order' => $category->sort_order,
            'created_at' => $category->created_at?->toIso8601String(),
            'updated_at' => $category->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toExampleListItem(ShowcaseExample $example): array
    {
        $images = $example->relationLoaded('images')
            ? $example->images->map(fn (ShowcaseExampleImage $img) => $this->toImageItem($img))->values()->all()
            : [];

        return [
            'id' => $example->id,
            'website_url' => $example->website_url,
            'name' => $example->name,
            'short_description' => $example->short_description,
            'category_id' => $example->category_id,
            'category' => $example->relationLoaded('category') && $example->category !== null
                ? ['id' => $example->category->id, 'name' => $example->category->name, 'slug' => $example->category->slug]
                : null,
            'modules' => $example->relationLoaded('modules')
                ? $example->modules->pluck('module_name')->values()->all()
                : [],
            'images' => $images,
            'preview_image_url' => $images[0]['url'] ?? null,
            'author' => $this->toAuthor($example),
            'moderation_status' => $example->moderation_status,
            'approved_at' => $example->approved_at?->toIso8601String(),
            'created_at' => $example->created_at?->toIso8601String(),
            'updated_at' => $example->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toExampleFull(ShowcaseExample $example): array
    {
        return array_merge($this->toExampleListItem($example), [
            'approver' => $this->toApprover($example),
        ]);
    }

    /**
     * @param UploadedFile[] $images
     */
    private function storeImages(int $exampleId, array $images): void
    {
        if ($images === []) {
            return;
        }

        $existingCount = ShowcaseExampleImage::query()
            ->where('showcase_example_id', $exampleId)
            ->count();

        $maxImages = (int) Config::get('showcase.max_images_per_example', 10);
        $available = max(0, $maxImages - $existingCount);

        if ($available === 0) {
            throw new RuntimeException('Достигнут лимит изображений для примера.');
        }

        $sortOrder = (int) ShowcaseExampleImage::query()
            ->where('showcase_example_id', $exampleId)
            ->max('sort_order');

        foreach (array_slice($images, 0, $available) as $file) {
            $sortOrder++;
            ShowcaseExampleImage::query()->create([
                'showcase_example_id' => $exampleId,
                'image_path' => $this->imageService->store($file),
                'sort_order' => $sortOrder,
            ]);
        }
    }

    /**
     * @param int[] $imageIds
     */
    private function removeImagesByIds(int $exampleId, array $imageIds): void
    {
        $images = ShowcaseExampleImage::query()
            ->where('showcase_example_id', $exampleId)
            ->whereIn('id', $imageIds)
            ->get();

        foreach ($images as $image) {
            $this->imageService->deleteByStoredPath($image->image_path);
            $image->delete();
        }
    }

    /**
     * @param string[] $modules
     */
    private function syncModules(int $exampleId, array $modules): void
    {
        ShowcaseExampleModule::query()->where('showcase_example_id', $exampleId)->delete();

        $normalized = [];
        foreach ($modules as $module) {
            $module = trim((string) $module);
            if ($module !== '' && !in_array($module, $normalized, true)) {
                $normalized[] = $module;
            }
        }

        foreach ($normalized as $module) {
            ShowcaseExampleModule::query()->create([
                'showcase_example_id' => $exampleId,
                'module_name' => $module,
            ]);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<ShowcaseExample>
     */
    private function baseExampleQuery()
    {
        return ShowcaseExample::query()
            ->with([
                'category',
                'author:id,first_name,last_name',
                'images',
                'modules',
                'approver:id,first_name,last_name',
            ])
            ->orderByDesc('created_at');
    }

    /**
     * @return array<string, mixed>
     */
    private function toImageItem(ShowcaseExampleImage $image): array
    {
        return [
            'id' => $image->id,
            'url' => $this->imageService->publicUrl($image->image_path),
            'sort_order' => $image->sort_order,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function toAuthor(ShowcaseExample $example): ?array
    {
        if (!$example->relationLoaded('author') || $example->author === null) {
            return null;
        }

        return [
            'id' => $example->author->id,
            'first_name' => $example->author->first_name,
            'last_name' => $example->author->last_name,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function toApprover(ShowcaseExample $example): ?array
    {
        if (!$example->relationLoaded('approver') || $example->approver === null) {
            return null;
        }

        return [
            'id' => $example->approver->id,
            'first_name' => $example->approver->first_name,
            'last_name' => $example->approver->last_name,
        ];
    }

    private function generateUniqueSlug(string $name, ?int $excludeCategoryId = null): string
    {
        $baseSlug = Str::slug($name);
        if ($baseSlug === '') {
            $baseSlug = 'category';
        }

        $slug = $baseSlug;
        $suffix = 2;

        while ($this->slugExists($slug, $excludeCategoryId)) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $excludeCategoryId = null): bool
    {
        $query = ShowcaseCategory::query()->where('slug', $slug);

        if ($excludeCategoryId !== null) {
            $query->where('id', '!=', $excludeCategoryId);
        }

        return $query->exists();
    }
}
