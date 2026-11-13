<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\MockInterface;
use Modules\Client\Entities\Client;
use Modules\MarketplaceModules\Entities\MarketplaceModule;
use Modules\MarketplaceModules\Http\Controllers\SellerMarketplaceModuleController;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;
use PHPUnit\Framework\TestCase;

class SellerMarketplaceModuleControllerTest extends TestCase
{
    private MockInterface $repository;

    private SellerMarketplaceModuleController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container();
        Facade::setFacadeApplication($container);
        JsonResponse::macro(
            'successful',
            static fn (?array $data = null): JsonResponse => new JsonResponse($data ?? ['message' => 'success'])
        );
        JsonResponse::macro(
            'failed',
            static fn (?array $data = null): JsonResponse => new JsonResponse($data ?? ['message' => 'error'], 400)
        );

        $client = new Client();
        $client->id = 42;

        $auth = Mockery::mock();
        $auth->shouldReceive('guard')->with('client')->andReturn(new class($client) {
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
        $this->controller = new SellerMarketplaceModuleController($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }

    public function testOwnerCanDeleteSpecificScreenshot(): void
    {
        $module = new MarketplaceModule();
        $module->id = 10;

        $this->repository->shouldReceive('findModuleForOwner')->once()->with(10, 42)->andReturn($module);
        $this->repository->shouldReceive('deleteScreenshot')->once()->with($module, 101)->andReturnTrue();

        $response = $this->controller->destroyScreenshot(10, 101);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(['message' => 'success'], $response->getData(true));
    }

    public function testItRejectsScreenshotThatDoesNotBelongToModule(): void
    {
        $module = new MarketplaceModule();
        $module->id = 10;

        $this->repository->shouldReceive('findModuleForOwner')->once()->with(10, 42)->andReturn($module);
        $this->repository->shouldReceive('deleteScreenshot')->once()->with($module, 999)->andReturnFalse();

        $response = $this->controller->destroyScreenshot(10, 999);

        self::assertSame(400, $response->getStatusCode());
        self::assertSame(['message' => 'Скриншот не найден.'], $response->getData(true));
    }
}
