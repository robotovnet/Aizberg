<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Modules\Client\Services\ClientUserpicService;
use PHPUnit\Framework\TestCase;

class ClientUserpicServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }

    public function testItBuildsPublicUserpicUrlFromApiHost(): void
    {
        $container = new Container();
        $container->instance('config', new Repository([
            'client' => [
                'userpic_disk' => 'public',
                'userpic_upload_dir' => 'clients/userpics',
                'api_host' => 'api.example.test',
            ],
        ]));
        Facade::setFacadeApplication($container);

        $service = new ClientUserpicService();

        self::assertSame(
            'https://api.example.test/storage/clients/userpics/avatar.webp',
            $service->publicUrl('clients/userpics/avatar.webp')
        );
        self::assertNull($service->publicUrl(null));
    }
}
