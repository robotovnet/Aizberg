<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Repositories;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Client\Entities\Client;
use Modules\Client\Services\ClientUserpicService;
use Modules\MarketplaceModules\Entities\MarketplaceModule;
use Modules\MarketplaceModules\Entities\MarketplaceModuleAnswer;
use Modules\MarketplaceModules\Entities\MarketplaceModuleCategoryIcon;
use Modules\MarketplaceModules\Entities\MarketplaceModuleFavorite;
use Modules\MarketplaceModules\Entities\MarketplaceModuleQuestion;
use Modules\MarketplaceModules\Entities\MarketplaceModulePurchase;
use Modules\MarketplaceModules\Entities\MarketplaceModuleRating;
use Modules\MarketplaceModules\Entities\MarketplaceModuleReview;
use Modules\MarketplaceModules\Entities\MarketplaceModuleReviewVote;
use Modules\MarketplaceModules\Entities\MarketplaceModuleScreenshot;
use Modules\MarketplaceModules\Entities\MarketplaceModuleTag;
use Modules\MarketplaceModules\Entities\MarketplaceModuleVersion;
use Modules\MarketplaceModules\Enums\MarketplacePricingType;
use Modules\MarketplaceModules\Services\MarketplaceModuleIconService;
use Modules\MarketplaceModules\Services\MarketplaceModuleScreenshotService;
use Modules\MarketplaceModules\Services\MarketplaceRatingAggregatorService;
use Throwable;

class MarketplaceModuleRepository
{
    public const PER_PAGE_LIST = 18;

    public const PER_PAGE_QA = 10;

    public const PER_PAGE_REVIEWS = 10;

    public const FEATURED_LIMIT = 10;

    public const SIMILAR_LIMIT = 5;

    public const POPULAR_SEARCHES_LIMIT = 10;

    public function __construct(
        private MarketplaceModuleIconService $iconService,
        private MarketplaceModuleScreenshotService $screenshotService,
        private MarketplaceRatingAggregatorService $ratingAggregator,
        private ClientUserpicService $userpicService
    ) {
    }

    public function findModuleForOwner(int $id, int $clientId): ?MarketplaceModule
    {
        return MarketplaceModule::query()
            ->whereKey($id)
            ->where('client_id', $clientId)
            ->first();
    }

    public function findModule(int $id): ?MarketplaceModule
    {
        return MarketplaceModule::query()
            ->with(['tags', 'screenshots'])
            ->find($id);
    }

    public function findPublicModuleBySlug(string $slug): ?MarketplaceModule
    {
        return MarketplaceModule::query()
            ->where('install_slug', $slug)
            ->where('is_archived', false)
            ->where('is_blocked', false)
            ->with(['tags', 'screenshots'])
            ->first();
    }

    public function findModuleBySlug(string $slug): ?MarketplaceModule
    {
        return MarketplaceModule::query()
            ->where('install_slug', $slug)
            ->first();
    }

    /**
     * @return LengthAwarePaginator<int, MarketplaceModule>
     */
    public function favoriteModulesForClient(int $clientId, int $page): LengthAwarePaginator
    {
        return MarketplaceModule::query()
            ->where('is_archived', false)
            ->where('is_blocked', false)
            ->whereHas('favorites', function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->withMax([
                'favorites as favorited_at' => function ($query) use ($clientId) {
                    $query->where('client_id', $clientId);
                },
            ], 'created_at')
            ->orderByDesc('favorited_at')
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE_LIST, ['*'], 'page', $page);
    }

    public function addModuleToFavorites(int $moduleId, int $clientId): void
    {
        $now = Carbon::now();

        MarketplaceModuleFavorite::query()->insertOrIgnore([
            'marketplace_module_id' => $moduleId,
            'client_id' => $clientId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function removeModuleFromFavorites(int $moduleId, int $clientId): void
    {
        MarketplaceModuleFavorite::query()
            ->where('marketplace_module_id', $moduleId)
            ->where('client_id', $clientId)
            ->delete();
    }

    public function isFavoriteForClient(int $moduleId, int $clientId): bool
    {
        return MarketplaceModuleFavorite::query()
            ->where('marketplace_module_id', $moduleId)
            ->where('client_id', $clientId)
            ->exists();
    }

    /**
     * @return LengthAwarePaginator<int, MarketplaceModulePurchase>
     */
    public function purchasesForClient(int $clientId, int $page): LengthAwarePaginator
    {
        return MarketplaceModulePurchase::query()
            ->where('client_id', $clientId)
            ->with([
                'module' => static fn ($query) => $query->withTrashed(),
            ])
            ->orderByDesc('purchased_at')
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE_LIST, ['*'], 'page', $page);
    }

    public function createPurchase(int $moduleId, int $clientId): ?MarketplaceModulePurchase
    {
        $now = Carbon::now();
        $inserted = MarketplaceModulePurchase::query()->insertOrIgnore([
            'marketplace_module_id' => $moduleId,
            'client_id' => $clientId,
            'purchased_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        if ($inserted === 0) {
            return null;
        }

        return MarketplaceModulePurchase::query()
            ->where('marketplace_module_id', $moduleId)
            ->where('client_id', $clientId)
            ->firstOrFail();
    }

    /**
     * @param string[] $tags
     * @param UploadedFile[] $screenshots
     * @param array<string, mixed> $data
     */
    public function create(
        int $clientId,
        array $data,
        array $tags,
        string $version,
        Carbon $versionReleaseDate,
        ?UploadedFile $icon,
        array $screenshots
    ): MarketplaceModule {
        $screenshotPaths = $this->screenshotService->storeMany($screenshots);

        try {
            return DB::transaction(function () use (
                $clientId,
                $data,
                $tags,
                $version,
                $versionReleaseDate,
                $icon,
                $screenshotPaths
            ) {
                $iconPath = null;
                if ($icon !== null) {
                    $iconPath = $this->iconService->store($icon);
                }

                $module = new MarketplaceModule(array_merge($data, [
                    'client_id' => $clientId,
                    'icon_path' => $iconPath,
                    'ratings_count' => 0,
                    'aggregate_rating' => null,
                    'views_count' => 0,
                    'is_archived' => false,
                    'is_favorite' => false,
                ]));
                $module->save();

                $this->syncTags($module->id, $tags);
                $this->syncScreenshotPaths($module->id, $screenshotPaths);
                $this->addVersionIfNew($module->id, $version, $versionReleaseDate, null);

                return $module->fresh(['tags', 'screenshots']);
            });
        } catch (Throwable $exception) {
            $this->screenshotService->deleteMany($screenshotPaths);
            throw $exception;
        }
    }

    /**
     * @param string[] $tags
     * @param UploadedFile[] $screenshots
     * @param array<string, mixed> $data
     */
    public function update(
        MarketplaceModule $module,
        array $data,
        array $tags,
        string $version,
        Carbon $versionReleaseDate,
        ?UploadedFile $icon,
        array $screenshots
    ): MarketplaceModule {
        $newScreenshotPaths = $this->screenshotService->storeMany($screenshots);

        try {
            $updated = DB::transaction(function () use (
                $module,
                $data,
                $tags,
                $version,
                $versionReleaseDate,
                $icon,
                $newScreenshotPaths
            ) {
                $existingScreenshotsCount = 0;
                if ($newScreenshotPaths !== []) {
                    MarketplaceModule::query()
                        ->whereKey($module->id)
                        ->lockForUpdate()
                        ->first();

                    $existingScreenshotsCount = $this->normalizeScreenshotPositions($module->id);
                    if ($existingScreenshotsCount + count($newScreenshotPaths)
                        > MarketplaceModuleScreenshotService::MAX_FILES) {
                        throw ValidationException::withMessages([
                            'screenshots' => [
                                'Общее количество скриншотов модуля не может превышать '
                                . MarketplaceModuleScreenshotService::MAX_FILES . '.',
                            ],
                        ]);
                    }
                }

                if ($icon !== null) {
                    $this->iconService->deleteByStoredPath($module->icon_path);
                    $data['icon_path'] = $this->iconService->store($icon);
                }

                $module->fill($data);
                $module->save();

                $this->syncTags($module->id, $tags);
                if ($newScreenshotPaths !== []) {
                    $this->appendScreenshotPaths(
                        $module->id,
                        $newScreenshotPaths,
                        $existingScreenshotsCount
                    );
                }

                $lastVersion = MarketplaceModuleVersion::query()
                    ->where('marketplace_module_id', $module->id)
                    ->orderByDesc('release_date')
                    ->orderByDesc('id')
                    ->first();

                $this->addVersionIfNew($module->id, $version, $versionReleaseDate, $lastVersion?->version);

                return $module->fresh(['tags', 'screenshots']);
            });
        } catch (Throwable $exception) {
            $this->screenshotService->deleteMany($newScreenshotPaths);
            throw $exception;
        }

        return $updated;
    }

    public function delete(MarketplaceModule $module): void
    {
        DB::transaction(function () use ($module) {
            $this->iconService->deleteByStoredPath($module->icon_path);
            $screenshotPaths = MarketplaceModuleScreenshot::query()
                ->where('marketplace_module_id', $module->id)
                ->pluck('path')
                ->all();
            $this->screenshotService->deleteMany($screenshotPaths);
            $module->delete();
        });
    }

    public function archive(MarketplaceModule $module): void
    {
        $module->is_archived = true;
        $module->save();
    }

    public function deleteScreenshot(MarketplaceModule $module, int $screenshotId): bool
    {
        $path = DB::transaction(function () use ($module, $screenshotId) {
            MarketplaceModule::query()
                ->whereKey($module->id)
                ->lockForUpdate()
                ->first();

            $screenshot = MarketplaceModuleScreenshot::query()
                ->whereKey($screenshotId)
                ->where('marketplace_module_id', $module->id)
                ->first();
            if ($screenshot === null) {
                return null;
            }

            $path = $screenshot->path;
            $screenshot->delete();
            $this->normalizeScreenshotPositions($module->id);

            return $path;
        });

        if ($path === null) {
            return false;
        }

        $this->screenshotService->deleteMany([$path]);

        return true;
    }

    public function block(MarketplaceModule $module, int $moderatorClientId): MarketplaceModule
    {
        MarketplaceModule::query()
            ->whereKey($module->id)
            ->where('is_blocked', false)
            ->update([
                'is_blocked' => true,
                'blocked_at' => Carbon::now(),
                'blocked_by_client_id' => $moderatorClientId,
            ]);

        return $module->fresh(['tags']);
    }

    public function unblock(MarketplaceModule $module): MarketplaceModule
    {
        MarketplaceModule::query()
            ->whereKey($module->id)
            ->where('is_blocked', true)
            ->update([
                'is_blocked' => false,
                'blocked_at' => null,
                'blocked_by_client_id' => null,
            ]);

        return $module->fresh(['tags']);
    }

    /**
     * @return LengthAwarePaginator<int, MarketplaceModule>
     */
    public function searchPublic(
        string $query,
        int $page,
        string $category = '',
        string $licenseType = '',
        string $sort = 'date_added',
        string $order = 'desc',
        string $pricingType = ''
    ): LengthAwarePaginator {
        $term = trim($query);

        $q = MarketplaceModule::query()
            ->where('is_archived', false)
            ->where('is_blocked', false)
            ->where(function ($q) use ($term) {
                $q->where('display_name', 'like', '%' . $term . '%')
                    ->orWhere('install_slug', 'like', '%' . $term . '%')
                    ->orWhere('short_description', 'like', '%' . $term . '%')
                    ->orWhere('detailed_description', 'like', '%' . $term . '%')
                    ->orWhere('author_display_name', 'like', '%' . $term . '%')
                    ->orWhereHas('tags', function ($tq) use ($term) {
                        $tq->where('tag', 'like', '%' . $term . '%');
                    });
            });

        if (trim($category) !== '') {
            $q->whereRaw('LOWER(category) = ?', [mb_strtolower(trim($category))]);
        }

        if (trim($licenseType) !== '') {
            $q->where('license_type', trim($licenseType));
        }

        if (trim($pricingType) !== '') {
            $q->where('pricing_type', trim($pricingType));
        }

        $this->applySort($q, $sort, $order);

        return $q->paginate(self::PER_PAGE_LIST, ['*'], 'page', $page);
    }

    /**
     * @return LengthAwarePaginator<int, MarketplaceModule>
     */
    public function listByCategory(string $category, int $page, string $sort, string $order): LengthAwarePaginator
    {
        $q = MarketplaceModule::query()
            ->where('is_archived', false)
            ->where('is_blocked', false)
            ->whereRaw('LOWER(category) = ?', [mb_strtolower($category)]);

        $this->applySort($q, $sort, $order);

        return $q->paginate(self::PER_PAGE_LIST, ['*'], 'page', $page);
    }

    public function recordSearchQuery(string $query): void
    {
        $query = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $query) ?? $query), 'UTF-8');
        if ($query === '') {
            return;
        }

        $now = Carbon::now();

        // A hash keeps the unique index small without truncating long search terms.
        DB::table('marketplace_module_search_queries')->upsert([
            'query_hash' => hash('sha256', $query),
            'query' => $query,
            'searches_count' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ], ['query_hash'], [
            'searches_count' => DB::raw('searches_count + 1'),
            'updated_at' => $now,
        ]);
    }

    /** @return array<int, array{query: string, searches_count: int}> */
    public function popularSearchQueries(): array
    {
        return DB::table('marketplace_module_search_queries')
            ->orderByDesc('searches_count')
            ->orderBy('id')
            ->limit(self::POPULAR_SEARCHES_LIMIT)
            ->get(['query', 'searches_count'])
            ->map(static fn ($row): array => [
                'query' => $row->query,
                'searches_count' => (int)$row->searches_count,
            ])
            ->all();
    }

    /** @return array<int, array{id: int, icon_url: string|null, display_name: string, short_description: string, install_slug: string}> */
    public function similarModules(MarketplaceModule $module): array
    {
        return MarketplaceModule::query()
            ->where('id', '<>', $module->id)
            ->where('is_archived', false)
            ->where('is_blocked', false)
            ->whereRaw('LOWER(TRIM(category)) = ?', [mb_strtolower(trim($module->category))])
            ->inRandomOrder()
            ->limit(self::SIMILAR_LIMIT)
            ->get(['id', 'icon_path', 'display_name', 'short_description', 'install_slug'])
            ->map(fn (MarketplaceModule $similar): array => [
                'id' => (int)$similar->id,
                'icon_url' => $this->iconService->publicUrl($similar->icon_path),
                'display_name' => $similar->display_name,
                'short_description' => $similar->short_description,
                'install_slug' => $similar->install_slug,
            ])
            ->all();
    }

    /** @return array<int, array{name: string, slug: string, icon: string|null}> */
    public function categories(): array
    {
        $categories = MarketplaceModule::query()
            ->where('is_archived', false)
            ->where('is_blocked', false)
            ->whereNotNull('category')
            ->pluck('category')
            ->filter(static fn (?string $category) => $category !== null && trim($category) !== '')
            ->unique(static fn (string $category) => mb_strtolower(trim($category)))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $categorySlugs = $categories
            ->map(static fn (string $category): string => Str::slug(trim($category)))
            ->filter()
            ->unique()
            ->values();

        $iconPaths = MarketplaceModuleCategoryIcon::query()
            ->whereIn('slug', $categorySlugs->all())
            ->pluck('icon_path', 'slug');

        return $categories
            ->map(function (string $category) use ($iconPaths): array {
                $name = trim($category);
                $slug = Str::slug($name);
                $iconPath = $iconPaths->get($slug);

                return [
                    'name' => $name,
                    'slug' => $slug,
                    'icon' => $this->iconService->publicUrl(
                        is_string($iconPath) && $iconPath !== '' ? $iconPath : null
                    ),
                ];
            })
            ->all();
    }

    /**
     * @return array{popular: array<int, array<string, mixed>>, featured: array<int, array<string, mixed>>, recent: array<int, array<string, mixed>>}
     */
    public function featuredBundles(): array
    {
        $base = fn () => MarketplaceModule::query()
            ->where('is_archived', false)
            ->where('is_blocked', false);

        $popular = (clone $base())
            ->orderByDesc('views_count')
            ->orderByDesc('id')
            ->limit(self::FEATURED_LIMIT)
            ->get();

        $featured = (clone $base())
            ->where('is_favorite', true)
            ->inRandomOrder()
            ->limit(self::FEATURED_LIMIT)
            ->get();

        $recent = (clone $base())
            ->orderByDesc('created_at')
            ->limit(self::FEATURED_LIMIT)
            ->get();

        return [
            'popular' => $popular->map(fn (MarketplaceModule $m) => $this->toListItem($m))->all(),
            'featured' => $featured->map(fn (MarketplaceModule $m) => $this->toListItem($m))->all(),
            'recent' => $recent->map(fn (MarketplaceModule $m) => $this->toListItem($m))->all(),
        ];
    }

    public function getBySlugPublic(string $slug): ?MarketplaceModule
    {
        $module = $this->findPublicModuleBySlug($slug);

        if ($module === null) {
            return null;
        }

        $module->increment('views_count');

        return $module->fresh(['tags']);
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, current_page: int, last_page: int, per_page: int, total: int}
     */
    public function paginateQuestionsWithAnswers(string $slug, int $page): array
    {
        $moduleId = MarketplaceModule::query()
            ->where('install_slug', $slug)
            ->where('is_archived', false)
            ->where('is_blocked', false)
            ->value('id');
        if ($moduleId === null) {
            return $this->emptyQaPage($page);
        }

        $paginator = MarketplaceModuleQuestion::query()
            ->where('marketplace_module_id', $moduleId)
            ->with(['author.userpic', 'answers.author.userpic'])
            ->orderByDesc('created_at')
            ->paginate(self::PER_PAGE_QA, ['*'], 'page', $page);

        $items = [];
        foreach ($paginator->items() as $question) {
            /** @var MarketplaceModuleQuestion $question */
            $answers = [];
            foreach ($question->answers as $answer) {
                $answers[] = array_merge([
                    'id' => $answer->id,
                    'answer_text' => $answer->answer_text,
                    'author_client_id' => $answer->client_id,
                    'created_at' => $answer->created_at?->toIso8601String(),
                ], $this->authorFields($answer->author));
            }
            $items[] = array_merge([
                'id' => $question->id,
                'question_text' => $question->question_text,
                'author_client_id' => $question->client_id,
                'created_at' => $question->created_at?->toIso8601String(),
                'answers' => $answers,
            ], $this->authorFields($question->author));
        }

        return [
            'items' => $items,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, current_page: int, last_page: int, per_page: int, total: int}
     */
    public function paginateReviewsWithRatings(string $slug, int $page): array
    {
        $module = $this->findPublicModuleBySlug($slug);
        if ($module === null) {
            return $this->emptyReviewsPage($page);
        }

        $paginator = MarketplaceModuleReview::query()
            ->where('marketplace_module_id', $module->id)
            ->with('author.userpic')
            ->withCount([
                'votes as helpful_count' => static fn ($query) => $query->where('is_helpful', true),
                'votes as unhelpful_count' => static fn ($query) => $query->where('is_helpful', false),
            ])
            ->orderByDesc('updated_at')
            ->paginate(self::PER_PAGE_REVIEWS, ['*'], 'page', $page);

        $clientIds = $paginator->getCollection()->pluck('client_id')->all();
        $ratings = [];
        if ($clientIds !== []) {
            $ratings = MarketplaceModuleRating::query()
                ->where('marketplace_module_id', $module->id)
                ->whereIn('client_id', $clientIds)
                ->get()
                ->keyBy('client_id');
        }

        $items = [];
        foreach ($paginator->items() as $review) {
            /** @var MarketplaceModuleReview $review */
            $r = $ratings[$review->client_id] ?? null;
            $items[] = array_merge([
                'id' => $review->id,
                'review_text' => $review->review_text,
                'author_client_id' => $review->client_id,
                'created_at' => $review->created_at?->toIso8601String(),
                'updated_at' => $review->updated_at?->toIso8601String(),
                'author_rating' => $r !== null ? $r->value : null,
                'helpful_count' => (int)$review->helpful_count,
                'unhelpful_count' => (int)$review->unhelpful_count,
            ], $this->authorFields($review->author));
        }

        return [
            'items' => $items,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, current_page: int, last_page: int, per_page: int, total: int}
     */
    public function paginateReviewsForClient(int $clientId, int $page): array
    {
        $paginator = MarketplaceModuleReview::query()
            ->where('client_id', $clientId)
            ->with([
                'module' => static fn ($query) => $query->withTrashed(),
                'author.userpic',
            ])
            ->withCount([
                'votes as helpful_count' => static fn ($query) => $query->where('is_helpful', true),
                'votes as unhelpful_count' => static fn ($query) => $query->where('is_helpful', false),
            ])
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE_REVIEWS, ['*'], 'page', $page);

        $moduleIds = $paginator->getCollection()->pluck('marketplace_module_id')->all();
        $ratings = [];
        if ($moduleIds !== []) {
            $ratings = MarketplaceModuleRating::query()
                ->where('client_id', $clientId)
                ->whereIn('marketplace_module_id', $moduleIds)
                ->get()
                ->keyBy('marketplace_module_id');
        }

        $items = [];
        foreach ($paginator->items() as $review) {
            /** @var MarketplaceModuleReview $review */
            $rating = $ratings[$review->marketplace_module_id] ?? null;
            $items[] = array_merge([
                'id' => $review->id,
                'review_text' => $review->review_text,
                'author_client_id' => $review->client_id,
                'created_at' => $review->created_at?->toIso8601String(),
                'updated_at' => $review->updated_at?->toIso8601String(),
                'author_rating' => $rating !== null ? $rating->value : null,
                'helpful_count' => (int)$review->helpful_count,
                'unhelpful_count' => (int)$review->unhelpful_count,
                'module' => $this->toListItem($review->module),
            ], $this->authorFields($review->author));
        }

        return [
            'items' => $items,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, current_page: int, last_page: int, per_page: int, total: int}
     */
    public function paginateQuestionsForClient(int $clientId, int $page): array
    {
        $paginator = MarketplaceModuleQuestion::query()
            ->where('client_id', $clientId)
            ->with([
                'module' => static fn ($query) => $query->withTrashed(),
                'author.userpic',
                'answers.author.userpic',
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE_QA, ['*'], 'page', $page);

        $items = [];
        foreach ($paginator->items() as $question) {
            /** @var MarketplaceModuleQuestion $question */
            $answers = [];
            foreach ($question->answers as $answer) {
                $answers[] = array_merge([
                    'id' => $answer->id,
                    'answer_text' => $answer->answer_text,
                    'author_client_id' => $answer->client_id,
                    'created_at' => $answer->created_at?->toIso8601String(),
                ], $this->authorFields($answer->author));
            }

            $items[] = array_merge([
                'id' => $question->id,
                'question_text' => $question->question_text,
                'author_client_id' => $question->client_id,
                'created_at' => $question->created_at?->toIso8601String(),
                'answers' => $answers,
                'module' => $this->toListItem($question->module),
            ], $this->authorFields($question->author));
        }

        return [
            'items' => $items,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function versionHistory(string $slug): array
    {
        $moduleId = MarketplaceModule::query()
            ->where('install_slug', $slug)
            ->where('is_archived', false)
            ->where('is_blocked', false)
            ->value('id');
        if ($moduleId === null) {
            return [];
        }

        return MarketplaceModuleVersion::query()
            ->where('marketplace_module_id', $moduleId)
            ->orderByDesc('release_date')
            ->orderByDesc('id')
            ->get()
            ->map(static function (MarketplaceModuleVersion $v) {
                return [
                    'version' => $v->version,
                    'release_date' => $v->release_date->toIso8601String(),
                    'changes' => $v->changes ?? [],
                ];
            })
            ->all();
    }

    public function toListItem(MarketplaceModule $m): array
    {
        $item = [
            'display_name' => $m->display_name,
            'icon_url' => $this->iconService->publicUrl($m->icon_path),
            'aggregate_rating' => $m->aggregate_rating,
            'author_display_name' => $m->author_display_name,
            'pricing_type' => $m->pricing_type,
            'short_description' => $m->short_description,
            'views_count' => $m->views_count,
            'website_url' => $m->website_url,
            'install_slug' => $m->install_slug,
        ];

        return $this->withPrice($item, $m);
    }

    /**
     * @return array<string, mixed>
     */
    public function toPurchaseItem(MarketplaceModulePurchase $purchase): array
    {
        return [
            'id' => $purchase->id,
            'purchased_at' => $purchase->purchased_at?->toIso8601String(),
            'module' => $this->toListItem($purchase->module),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toFullCard(MarketplaceModule $m): array
    {
        $card = [
            'id' => $m->id,
            'display_name' => $m->display_name,
            'install_slug' => $m->install_slug,
            'website_url' => $m->website_url,
            'source_code_url' => $m->source_code_url,
            'license_type' => $m->license_type,
            'license_url' => $m->license_url,
            'changelog_url' => $m->changelog_url,
            'pricing_type' => $m->pricing_type,
            'author_display_name' => $m->author_display_name,
            'short_description' => $m->short_description,
            'category' => $m->category,
            'tags' => $m->tags->pluck('tag')->all(),
            'detailed_description' => $m->detailed_description,
            'icon_url' => $this->iconService->publicUrl($m->icon_path),
            'screenshots' => $m->screenshots
                ->map(fn (MarketplaceModuleScreenshot $screenshot) => [
                    'id' => $screenshot->id,
                    'url' => $this->screenshotService->publicUrl($screenshot->path),
                    'position' => $screenshot->position,
                ])
                ->values()
                ->all(),
            'catalog_release_at' => $m->created_at?->toIso8601String(),
            'updated_at' => $m->updated_at?->toIso8601String(),
            'ratings_count' => $m->ratings_count,
            'aggregate_rating' => $m->aggregate_rating,
            'views_count' => $m->views_count,
            'is_archived' => $m->is_archived,
            'is_blocked' => $m->is_blocked,
            'blocked_at' => $m->blocked_at?->toIso8601String(),
            'blocked_by_client_id' => $m->blocked_by_client_id,
            'owner_client_id' => $m->client_id,
        ];

        return $this->withPrice($card, $m);
    }

    public function askQuestion(int $moduleId, int $clientId, string $text): MarketplaceModuleQuestion
    {
        $q = new MarketplaceModuleQuestion([
            'marketplace_module_id' => $moduleId,
            'client_id' => $clientId,
            'question_text' => $text,
        ]);
        $q->save();

        return $q;
    }

    public function answerQuestion(int $questionId, int $clientId, string $text): MarketplaceModuleAnswer
    {
        $a = new MarketplaceModuleAnswer([
            'marketplace_module_question_id' => $questionId,
            'client_id' => $clientId,
            'answer_text' => $text,
        ]);
        $a->save();

        return $a;
    }

    public function upsertReview(int $moduleId, int $clientId, string $text): MarketplaceModuleReview
    {
        $review = MarketplaceModuleReview::query()
            ->where('marketplace_module_id', $moduleId)
            ->where('client_id', $clientId)
            ->first();

        $stamp = Carbon::now()->format('Y-m-d H:i:s');
        if ($review === null) {
            $review = new MarketplaceModuleReview([
                'marketplace_module_id' => $moduleId,
                'client_id' => $clientId,
                'review_text' => $text,
            ]);
            $review->save();

            return $review;
        }

        $review->review_text = rtrim((string)$review->review_text) . "\n\nОт " . $stamp . "\n" . $text;
        $review->save();

        return $review->fresh();
    }

    public function upsertRating(int $moduleId, int $clientId, int $value): MarketplaceModuleRating
    {
        return DB::transaction(function () use ($moduleId, $clientId, $value) {
            // Serialize ratings for the same module so concurrent requests from
            // one client cannot create duplicate rows.
            MarketplaceModule::query()
                ->whereKey($moduleId)
                ->lockForUpdate()
                ->first();

            $rating = MarketplaceModuleRating::query()
                ->where('marketplace_module_id', $moduleId)
                ->where('client_id', $clientId)
                ->first();

            if ($rating === null) {
                $rating = new MarketplaceModuleRating([
                    'marketplace_module_id' => $moduleId,
                    'client_id' => $clientId,
                    'value' => $value,
                ]);
            } else {
                $rating->value = $value;
            }
            $rating->save();

            $this->ratingAggregator->recalculateForModule($moduleId);

            return $rating->fresh();
        });
    }

    public function findPublicReview(string $slug, int $reviewId): ?MarketplaceModuleReview
    {
        return MarketplaceModuleReview::query()
            ->whereKey($reviewId)
            ->whereHas('module', static function ($query) use ($slug): void {
                $query->where('install_slug', $slug)
                    ->where('is_archived', false)
                    ->where('is_blocked', false);
            })
            ->first();
    }

    /** @return array{helpful_count: int, unhelpful_count: int} */
    public function upsertReviewVote(int $reviewId, int $clientId, bool $isHelpful): array
    {
        return DB::transaction(function () use ($reviewId, $clientId, $isHelpful): array {
            MarketplaceModuleReview::query()->whereKey($reviewId)->lockForUpdate()->firstOrFail();

            $vote = MarketplaceModuleReviewVote::query()
                ->where('marketplace_module_review_id', $reviewId)
                ->where('client_id', $clientId)
                ->first();
            if ($vote === null) {
                $vote = new MarketplaceModuleReviewVote([
                    'marketplace_module_review_id' => $reviewId,
                    'client_id' => $clientId,
                ]);
            }
            $vote->is_helpful = $isHelpful;
            $vote->save();

            $votes = MarketplaceModuleReviewVote::query()
                ->where('marketplace_module_review_id', $reviewId)
                ->get(['is_helpful']);

            return [
                'helpful_count' => $votes->where('is_helpful', true)->count(),
                'unhelpful_count' => $votes->where('is_helpful', false)->count(),
            ];
        });
    }

    /** @param string[] $changes */
    public function upsertVersionChangelog(
        int $moduleId,
        string $version,
        array $changes,
        ?Carbon $releaseDate
    ): MarketplaceModuleVersion {
        return DB::transaction(function () use ($moduleId, $version, $changes, $releaseDate) {
            MarketplaceModule::query()->whereKey($moduleId)->lockForUpdate()->firstOrFail();

            $entry = MarketplaceModuleVersion::query()
                ->where('marketplace_module_id', $moduleId)
                ->where('version', $version)
                ->first();
            if ($entry === null) {
                $entry = new MarketplaceModuleVersion([
                    'marketplace_module_id' => $moduleId,
                    'version' => $version,
                    'release_date' => $releaseDate ?? Carbon::now(),
                ]);
            } elseif ($releaseDate !== null) {
                $entry->release_date = $releaseDate;
            }

            $entry->changes = array_values(array_unique(array_map('trim', $changes)));
            $entry->save();

            return $entry->fresh();
        });
    }

    /** @return array{first_name: ?string, last_name: ?string, avatar_url: ?string} */
    private function authorFields(?Client $author): array
    {
        return [
            'first_name' => $author?->first_name,
            'last_name' => $author?->last_name,
            'avatar_url' => $this->userpicService->publicUrl($author?->userpic?->path),
        ];
    }

    /**
     * @param string[] $tags
     */
    private function syncTags(int $moduleId, array $tags): void
    {
        MarketplaceModuleTag::query()->where('marketplace_module_id', $moduleId)->delete();
        $tags = array_values(array_unique(array_filter(array_map('trim', $tags))));

        foreach ($tags as $tag) {
            MarketplaceModuleTag::query()->create([
                'marketplace_module_id' => $moduleId,
                'tag' => $tag,
            ]);
        }
    }

    /** @param string[] $paths */
    private function syncScreenshotPaths(int $moduleId, array $paths): void
    {
        MarketplaceModuleScreenshot::query()
            ->where('marketplace_module_id', $moduleId)
            ->delete();

        foreach (array_values($paths) as $position => $path) {
            MarketplaceModuleScreenshot::query()->create([
                'marketplace_module_id' => $moduleId,
                'path' => $path,
                'position' => $position,
            ]);
        }
    }

    /**
     * @param string[] $paths
     */
    private function appendScreenshotPaths(int $moduleId, array $paths, int $startPosition): void
    {
        foreach (array_values($paths) as $offset => $path) {
            MarketplaceModuleScreenshot::query()->create([
                'marketplace_module_id' => $moduleId,
                'path' => $path,
                'position' => $startPosition + $offset,
            ]);
        }
    }

    private function normalizeScreenshotPositions(int $moduleId): int
    {
        $screenshots = MarketplaceModuleScreenshot::query()
            ->where('marketplace_module_id', $moduleId)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        foreach ($screenshots as $position => $screenshot) {
            if ((int)$screenshot->position !== $position) {
                MarketplaceModuleScreenshot::query()
                    ->whereKey($screenshot->id)
                    ->update(['position' => $position]);
            }
        }

        return $screenshots->count();
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function withPrice(array $data, MarketplaceModule $module): array
    {
        $data['price'] = $module->price !== null ? (float)$module->price : null;
        $data['currency'] = $module->currency;

        return $data;
    }

    private function addVersionIfNew(int $moduleId, string $version, Carbon $releaseDate, ?string $previousVersion): void
    {
        if ($previousVersion !== null && $previousVersion === $version) {
            return;
        }

        $exists = MarketplaceModuleVersion::query()
            ->where('marketplace_module_id', $moduleId)
            ->where('version', $version)
            ->exists();

        if ($exists) {
            return;
        }

        MarketplaceModuleVersion::query()->create([
            'marketplace_module_id' => $moduleId,
            'version' => $version,
            'release_date' => $releaseDate,
        ]);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<MarketplaceModule> $q
     */
    private function applySort($q, string $sort, string $order): void
    {
        $dir = strtolower($order) === 'asc' ? 'asc' : 'desc';

        if ($sort === 'rating') {
            $q->orderByRaw('(aggregate_rating IS NULL) ASC');
        }

        $column = match ($sort) {
            'popularity' => 'views_count',
            'rating' => 'aggregate_rating',
            'date_added' => 'created_at',
            'date_updated' => 'updated_at',
            'name' => 'display_name',
            'pricing' => 'pricing_type',
            default => 'created_at',
        };

        $q->orderBy($column, $dir)->orderBy('id', 'asc');
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, current_page: int, last_page: int, per_page: int, total: int}
     */
    private function emptyQaPage(int $page): array
    {
        return [
            'items' => [],
            'current_page' => $page,
            'last_page' => 1,
            'per_page' => self::PER_PAGE_QA,
            'total' => 0,
        ];
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, current_page: int, last_page: int, per_page: int, total: int}
     */
    private function emptyReviewsPage(int $page): array
    {
        return [
            'items' => [],
            'current_page' => $page,
            'last_page' => 1,
            'per_page' => self::PER_PAGE_REVIEWS,
            'total' => 0,
        ];
    }
}
