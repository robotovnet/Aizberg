<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Modules\Client\Entities\Client;
use Modules\Client\Http\Controllers\ClientController;
use Modules\Client\Http\Requests\ChangePasswordRequest;
use PHPUnit\Framework\TestCase;

class ClientPasswordControllerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();
        Facade::setFacadeApplication($this->container);

        JsonResponse::macro(
            'successful',
            static fn (?array $data = null): JsonResponse => new JsonResponse($data ?? ['message' => 'success'])
        );
        JsonResponse::macro(
            'failed',
            static fn (?array $data = null): JsonResponse => new JsonResponse($data ?? ['message' => 'error'], 400)
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }

    public function testItChangesAuthenticatedClientsPassword(): void
    {
        $client = Mockery::mock(Client::class)->makePartial();
        $client->setRawAttributes(['password' => 'old-hash']);
        $client->shouldReceive('save')->once()->andReturnTrue();

        $this->bindAuthenticatedClient($client);

        $hasher = Mockery::mock();
        $hasher->shouldReceive('check')->once()->with('old-password', 'old-hash')->andReturnTrue();
        $hasher->shouldReceive('make')->once()->with('new-password')->andReturn('new-hash');
        $this->container->instance('hash', $hasher);

        $request = ChangePasswordRequest::create('', 'POST', [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response = (new ClientController())->updatePassword($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(['message' => 'success'], $response->getData(true));
        self::assertSame('new-hash', $client->getAttributes()['password']);
    }

    public function testItRejectsIncorrectCurrentPassword(): void
    {
        $client = Mockery::mock(Client::class)->makePartial();
        $client->setRawAttributes(['password' => 'old-hash']);
        $client->shouldNotReceive('save');

        $this->bindAuthenticatedClient($client);

        $hasher = Mockery::mock();
        $hasher->shouldReceive('check')->once()->with('wrong-password', 'old-hash')->andReturnFalse();
        $hasher->shouldNotReceive('make');
        $this->container->instance('hash', $hasher);

        $translator = Mockery::mock();
        $translator->shouldReceive('get')
            ->once()
            ->with('client::profile.failed_change_password')
            ->andReturn('Incorrect current password.');
        $this->container->instance('translator', $translator);

        $request = ChangePasswordRequest::create('', 'POST', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response = (new ClientController())->updatePassword($request);

        self::assertSame(400, $response->getStatusCode());
        self::assertSame(['message' => 'Incorrect current password.'], $response->getData(true));
        self::assertSame('old-hash', $client->getAttributes()['password']);
    }

    public function testPasswordRequestRequiresCurrentAndConfirmedNewPassword(): void
    {
        self::assertSame([
            'current_password' => 'required|string|between:6,20',
            'password' => 'required|string|between:6,20|confirmed',
        ], (new ChangePasswordRequest())->rules());
    }

    private function bindAuthenticatedClient(Client $client): void
    {
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
        $this->container->instance('auth', $auth);
    }
}
