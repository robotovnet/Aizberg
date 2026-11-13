<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Modules\Client\Services\ClientUserpicService;
use Modules\MarketplaceModules\Entities\MarketplaceModule;
use Modules\MarketplaceModules\Http\Controllers\PublicMarketplaceController;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;
use Modules\MarketplaceModules\Services\MarketplaceModuleIconService;
use Modules\MarketplaceModules\Services\MarketplaceModuleScreenshotService;
use Modules\MarketplaceModules\Services\MarketplaceRatingAggregatorService;
use PDO;
use PHPUnit\Framework\TestCase;

class MarketplaceDiscoveryTest extends TestCase
{
    private ?Manager $database = null;

    private MarketplaceModuleRepository $repository;

    private Router $router;

    protected function setUp(): void
    {
        parent::setUp();

        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            self::markTestSkipped('The pdo_sqlite extension is required.');
        }

        $container = new Container();
        Container::setInstance($container);
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($container);
        $container->instance('config', new Repository([
            'marketplacemodules' => ['api_host' => 'https://api.example.test'],
        ]));

        $this->database = new Manager($container);
        $this->database->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'foreign_key_constraints' => false,
        ]);
        $this->database->bootEloquent();
        $container->instance('db', $this->database->getDatabaseManager());
        $container->bind('db.schema', fn () => $this->database->getConnection()->getSchemaBuilder());

        $migrations = [
            '2026_03_31_120000_create_marketplace_modules_tables.php' => \CreateMarketplaceModulesTables::class,
            '2026_07_31_120000_add_module_blocking_to_marketplace_modules.php' => \AddModuleBlockingToMarketplaceModules::class,
            '2026_08_10_120000_add_pricing_and_screenshots_to_marketplace_modules.php' => \AddPricingAndScreenshotsToMarketplaceModules::class,
            '2026_09_25_120000_create_marketplace_module_search_queries_table.php' => \CreateMarketplaceModuleSearchQueriesTable::class,
        ];
        foreach ($migrations as $file => $class) {
            require_once dirname(__DIR__, 2) . '/Modules/MarketplaceModules/Database/Migrations/' . $file;
            (new $class())->up();
        }

        $this->repository = new MarketplaceModuleRepository(
            new MarketplaceModuleIconService(),
            Mockery::mock(MarketplaceModuleScreenshotService::class),
            Mockery::mock(MarketplaceRatingAggregatorService::class),
            Mockery::mock(ClientUserpicService::class)
        );
        $container->instance(PublicMarketplaceController::class, new PublicMarketplaceController($this->repository));
        $guard = Mockery::mock();
        $guard->shouldReceive('user')->andReturnNull();
        $auth = Mockery::mock();
        $auth->shouldReceive('guard')->with('client')->andReturn($guard);
        $container->instance('auth', $auth);

        JsonResponse::macro('successful', static fn (array $data): JsonResponse => new JsonResponse($data));
        $this->router = new Router(new Dispatcher($container), $container);
        $container->instance('router', $this->router);
        $container->alias('request', Request::class);
        // Exercise the real public routes and database without application/session middleware.
        $container->instance('middleware.disable', true);
        $this->router->prefix('api')->group(function () {
            require dirname(__DIR__, 2) . '/Modules/MarketplaceModules/Routes/api.php';
        });
    }

    protected function tearDown(): void
    {
        if ($this->database !== null) {
            $this->database->getConnection()->disconnect();
            Model::unsetConnectionResolver();
            Facade::clearResolvedInstances();
            Facade::setFacadeApplication(null);
            Container::setInstance(null);
        }
        Mockery::close();

        parent::tearDown();
    }

    public function testCardReturnsOnlyOtherPublicModulesInTheSameCategory(): void
    {
        $current = $this->createModule('current');
        $similar = $this->createModule('similar', ['category' => 'seo', 'icon_path' => 'icons/similar.png']);
        $withoutIcon = $this->createModule('without-icon');
        $this->createModule('different-category', ['category' => 'Payments']);
        $this->createModule('archived', ['is_archived' => true]);
        $this->createModule('blocked', ['is_blocked' => true]);
        $this->createModule('deleted')->delete();

        $card = $this->get('/modules/current');

        self::assertSame($current->id, $card['id']);
        self::assertFalse($card['is_favourite']);
        self::assertSame(1, $card['views_count']);
        self::assertEqualsCanonicalizing([
            [
                'id' => $similar->id,
                'icon_url' => 'https://api.example.test/storage/icons/similar.png',
                'display_name' => 'Module similar',
                'short_description' => 'Description similar',
                'install_slug' => 'similar',
            ],
            [
                'id' => $withoutIcon->id,
                'icon_url' => null,
                'display_name' => 'Module without-icon',
                'short_description' => 'Description without-icon',
                'install_slug' => 'without-icon',
            ],
        ], $card['similar_modules']);
        self::assertSame(0, $similar->fresh()->views_count);
    }

    public function testCardLimitsSimilarModulesToFiveDistinctResults(): void
    {
        $this->createModule('current');
        $eligibleIds = [];
        for ($i = 0; $i < 8; $i++) {
            $eligibleIds[] = $this->createModule('similar-' . $i)->id;
        }

        $ids = array_column($this->get('/modules/current')['similar_modules'], 'id');

        self::assertCount(5, $ids);
        self::assertCount(5, array_unique($ids));
        self::assertSame([], array_values(array_diff($ids, $eligibleIds)));
    }

    public function testCardReturnsAnEmptyListWhenThereAreNoSimilarModules(): void
    {
        $this->createModule('current');

        self::assertSame([], $this->get('/modules/current')['similar_modules']);
    }

    public function testSearchCountsNormalizedQueriesIncludingEmptyResultsAndPagination(): void
    {
        foreach (["  ПОИСК   Модулей  ", "поиск\tмодулей", "\u{00A0}Поиск модулей\n"] as $index => $query) {
            $response = $this->get('/search', ['q' => $query, 'page' => $index + 1]);
            self::assertSame([], $response['items']);
            self::assertSame(0, $response['total']);
            self::assertSame($index + 1, $response['current_page']);
        }

        self::assertSame([
            'items' => [['query' => 'поиск модулей', 'searches_count' => 3]],
        ], $this->get('/popular-searches'));
        self::assertSame(1, DB::table('marketplace_module_search_queries')->count());
    }

    public function testBlankSearchesDoNotCreateStatisticsAndSearchResultsArePreserved(): void
    {
        $this->createModule('visible');
        $this->createModule('hidden', ['is_blocked' => true]);

        foreach ([null, '', " \t\n\u{00A0}"] as $query) {
            $this->get('/search', $query === null ? [] : ['q' => $query]);
        }
        self::assertSame(['items' => []], $this->get('/popular-searches'));

        $response = $this->get('/search', ['q' => 'visible', 'category' => 'SEO']);
        self::assertSame(['visible'], array_column($response['items'], 'install_slug'));
        self::assertSame(1, $response['total']);
        self::assertSame([
            'items' => [['query' => 'visible', 'searches_count' => 1]],
        ], $this->get('/popular-searches'));
    }

    public function testPopularSearchesReturnTheTopTenWithStableTies(): void
    {
        $expected = [];
        foreach (['zebra', 'alpha'] as $query) {
            for ($i = 0; $i < 15; $i++) {
                $this->repository->recordSearchQuery($query);
            }
            $expected[] = ['query' => $query, 'searches_count' => 15];
        }
        for ($count = 12; $count >= 1; $count--) {
            for ($i = 0; $i < $count; $i++) {
                $this->repository->recordSearchQuery('query ' . $count);
            }
            $expected[] = ['query' => 'query ' . $count, 'searches_count' => $count];
        }

        self::assertSame(['items' => array_slice($expected, 0, 10)], $this->get('/popular-searches'));
    }

    public function testLongQueriesAreStoredWithoutTruncationOrMerging(): void
    {
        $first = str_repeat('модуль ', 100) . 'one';
        $second = str_repeat('модуль ', 100) . 'two';
        $this->get('/search', ['q' => $first]);
        $this->get('/search', ['q' => $second]);
        $this->get('/search', ['q' => $first]);

        self::assertSame(['items' => [
            ['query' => $first, 'searches_count' => 2],
            ['query' => $second, 'searches_count' => 1],
        ]], $this->get('/popular-searches'));
    }

    public function testSearchStatisticsMigrationCanBeRolledBack(): void
    {
        (new \CreateMarketplaceModuleSearchQueriesTable())->down();

        self::assertFalse(Schema::hasTable('marketplace_module_search_queries'));
        self::assertTrue(Schema::hasTable('marketplace_modules'));
    }

    private function get(string $path, array $parameters = []): array
    {
        $request = Request::create('/api/marketplace-modules' . $path, 'GET', $parameters);
        Container::getInstance()->instance('request', $request);
        $response = $this->router->dispatch($request);
        self::assertSame(200, $response->getStatusCode());

        return $response->getData(true);
    }

    private function createModule(string $slug, array $attributes = []): MarketplaceModule
    {
        return MarketplaceModule::query()->create(array_merge([
            'client_id' => 1,
            'display_name' => 'Module ' . $slug,
            'install_slug' => $slug,
            'website_url' => 'https://example.test',
            'source_code_url' => 'https://example.test/source',
            'license_type' => 'MIT',
            'pricing_type' => 'free',
            'author_display_name' => 'Author',
            'short_description' => 'Description ' . $slug,
            'category' => 'SEO',
        ], $attributes));
    }
}
