<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\MockInterface;
use Modules\Client\Entities\Client;
use Modules\MarketplaceModules\Http\Controllers\MarketplaceModuleActivityController;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;
use PHPUnit\Framework\TestCase;

class MarketplaceModuleActivityControllerTest extends TestCase
{
    private MockInterface $repository;

    private MarketplaceModuleActivityController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container();
        Facade::setFacadeApplication($container);
        JsonResponse::macro(
            'successful',
            static fn (?array $data = null): JsonResponse => new JsonResponse($data ?? ['message' => 'success'])
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
        $this->controller = new MarketplaceModuleActivityController($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }

    public function testItReturnsCurrentClientsReviewsPage(): void
    {
        $page = [
            'items' => [['id' => 100, 'review_text' => 'Useful module']],
            'current_page' => 2,
            'last_page' => 3,
            'per_page' => 10,
            'total' => 21,
        ];
        $this->repository->shouldReceive('paginateReviewsForClient')
            ->once()
            ->with(42, 2)
            ->andReturn($page);

        $response = $this->controller->reviews(Request::create('/my/reviews?page=2', 'GET'));

        self::assertSame(200, $response->getStatusCode());
        self::assertSame($page, $response->getData(true));
    }

    public function testItReturnsCurrentClientsQuestionsWithAnswersPage(): void
    {
        $page = [
            'items' => [[
                'id' => 200,
                'question_text' => 'How does it work?',
                'answers' => [['id' => 201, 'answer_text' => 'Like this.']],
            ]],
            'current_page' => 1,
            'last_page' => 1,
            'per_page' => 10,
            'total' => 1,
        ];
        $this->repository->shouldReceive('paginateQuestionsForClient')
            ->once()
            ->with(42, 1)
            ->andReturn($page);

        $response = $this->controller->questions(Request::create('/my/questions?page=-5', 'GET'));

        self::assertSame(200, $response->getStatusCode());
        self::assertSame($page, $response->getData(true));
    }
}
