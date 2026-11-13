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
use Modules\MarketplaceModules\Entities\MarketplaceModulePurchase;
use Modules\MarketplaceModules\Http\Controllers\MarketplaceModulePurchaseController;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;
use PHPUnit\Framework\TestCase;

class MarketplaceModulePurchaseControllerTest extends TestCase
{
    private MockInterface $repository;

    private MarketplaceModulePurchaseController $controller;

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
        $this->controller = new MarketplaceModulePurchaseController($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }

    public function testItReturnsCurrentClientsPurchasesPage(): void
    {
        $purchase = new MarketplaceModulePurchase();
        $purchase->id = 100;
        $paginator = new LengthAwarePaginator([$purchase], 19, 18, 2);

        $this->repository->shouldReceive('purchasesForClient')
            ->once()
            ->with(42, 2)
            ->andReturn($paginator);
        $this->repository->shouldReceive('toPurchaseItem')
            ->once()
            ->with($purchase)
            ->andReturn(['id' => 100, 'module' => ['install_slug' => 'seo-tools']]);

        $response = $this->controller->index(Request::create('/my/purchases?page=2', 'GET'));

        self::assertSame(200, $response->getStatusCode());
        self::assertSame([
            'items' => [['id' => 100, 'module' => ['install_slug' => 'seo-tools']]],
            'current_page' => 2,
            'last_page' => 2,
            'per_page' => 18,
            'total' => 19,
        ], $response->getData(true));
    }

    public function testDebugEndpointCreatesPurchaseForAuthenticatedClient(): void
    {
        $module = new MarketplaceModule(['install_slug' => 'seo-tools']);
        $module->id = 10;
        $purchase = new MarketplaceModulePurchase();
        $purchase->setRawAttributes([
            'marketplace_module_id' => 10,
            'client_id' => 42,
            'purchased_at' => '2026-08-21 12:00:00',
            'id' => 100,
        ], true);

        $item = [
            'id' => 100,
            'purchased_at' => '2026-08-21T12:00:00+00:00',
            'module' => ['install_slug' => 'seo-tools'],
        ];

        $this->repository->shouldReceive('findPublicModuleBySlug')->once()->with('seo-tools')->andReturn($module);
        $this->repository->shouldReceive('createPurchase')->once()->with(10, 42)->andReturn($purchase);
        $this->repository->shouldReceive('toPurchaseItem')->once()->with($purchase)->andReturn($item);

        $response = $this->controller->debugStore('seo-tools');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame($item, $response->getData(true));
        self::assertSame($module, $purchase->module);
    }

    public function testDebugEndpointRejectsRepeatedPurchase(): void
    {
        $module = new MarketplaceModule(['install_slug' => 'seo-tools']);
        $module->id = 10;

        $this->repository->shouldReceive('findPublicModuleBySlug')->once()->with('seo-tools')->andReturn($module);
        $this->repository->shouldReceive('createPurchase')->once()->with(10, 42)->andReturnNull();
        $this->repository->shouldNotReceive('toPurchaseItem');

        $response = $this->controller->debugStore('seo-tools');

        self::assertSame(400, $response->getStatusCode());
        self::assertSame(['message' => 'Модуль уже куплен.'], $response->getData(true));
    }
}
