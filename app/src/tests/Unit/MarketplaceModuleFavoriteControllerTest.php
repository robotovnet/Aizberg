<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\MockInterface;
use Modules\Client\Entities\Client;
use Modules\MarketplaceModules\Entities\MarketplaceModule;
use Modules\MarketplaceModules\Http\Controllers\MarketplaceModuleFavoriteController;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;
use PHPUnit\Framework\TestCase;

class MarketplaceModuleFavoriteControllerTest extends TestCase
{
    private MockInterface $repository;

    private MarketplaceModuleFavoriteController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container();
        Facade::setFacadeApplication($container);
        JsonResponse::macro('successful', static fn (?array $data = null): JsonResponse => new JsonResponse($data ?? ['message' => 'success']));
        JsonResponse::macro('failed', static fn (?array $data = null): JsonResponse => new JsonResponse($data ?? ['message' => 'error'], 400));

        $client = new Client();
        $client->id = 42;

        $auth = Mockery::mock();
        $auth->shouldReceive('guard')
            ->with('client')
            ->andReturn(new class($client) {
                public function __construct(private Client $client)
                {
                }

                public function user(): Client
                {
                    return $this->client;
                }
            });
        $container->instance('auth', $auth);

        $this->repository = Mockery::mock(MarketplaceModuleRepository::class);
        $this->controller = new MarketplaceModuleFavoriteController($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }

    public function testItReturnsCurrentClientsFavoriteModules(): void
    {
        $module = new MarketplaceModule(['install_slug' => 'seo-tools']);
        $module->id = 10;
        $paginator = new LengthAwarePaginator([$module], 1, 18, 1);

        $this->repository->shouldReceive('favoriteModulesForClient')
            ->once()
            ->with(42, 1)
            ->andReturn($paginator);
        $this->repository->shouldReceive('toListItem')
            ->once()
            ->with($module)
            ->andReturn(['install_slug' => 'seo-tools']);

        $response = $this->controller->index(Request::create('/favorites', 'GET'));

        self::assertSame(200, $response->getStatusCode());
        self::assertSame([
            'items' => [['install_slug' => 'seo-tools']],
            'current_page' => 1,
            'last_page' => 1,
            'per_page' => 18,
            'total' => 1,
        ], $response->getData(true));
    }

    public function testItAddsPublicModuleToCurrentClientsFavorites(): void
    {
        $module = new MarketplaceModule(['install_slug' => 'seo-tools']);
        $module->id = 10;

        $this->repository->shouldReceive('findPublicModuleBySlug')
            ->once()
            ->with('seo-tools')
            ->andReturn($module);
        $this->repository->shouldReceive('addModuleToFavorites')
            ->once()
            ->with(10, 42);

        $response = $this->controller->store('seo-tools');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(['is_favorite' => true], $response->getData(true));
    }

    public function testItRemovesModuleFromCurrentClientsFavorites(): void
    {
        $module = new MarketplaceModule(['install_slug' => 'seo-tools']);
        $module->id = 10;

        $this->repository->shouldReceive('findModuleBySlug')
            ->once()
            ->with('seo-tools')
            ->andReturn($module);
        $this->repository->shouldReceive('removeModuleFromFavorites')
            ->once()
            ->with(10, 42);

        $response = $this->controller->destroy('seo-tools');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(['is_favorite' => false], $response->getData(true));
    }

    public function testItRejectsAddingUnavailableModule(): void
    {
        $this->repository->shouldReceive('findPublicModuleBySlug')
            ->once()
            ->with('missing')
            ->andReturnNull();
        $this->repository->shouldNotReceive('addModuleToFavorites');

        $response = $this->controller->store('missing');

        self::assertSame(400, $response->getStatusCode());
        self::assertSame(['message' => 'Модуль не найден.'], $response->getData(true));
    }
}
