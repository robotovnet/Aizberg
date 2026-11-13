<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Modules\MarketplaceModules\Services\MarketplaceModuleScreenshotService;
use PHPUnit\Framework\TestCase;

class MarketplaceModuleScreenshotServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }

    public function testItBuildsPublicScreenshotUrlFromApiHost(): void
    {
        $container = new Container();
        $container->instance('config', new Repository([
            'marketplacemodules' => [
                'screenshot_disk' => 'public',
                'screenshot_upload_dir' => 'marketplace-modules/screenshots',
                'api_host' => 'api.aizberg.ru',
            ],
        ]));
        Facade::setFacadeApplication($container);

        $service = new MarketplaceModuleScreenshotService();

        self::assertSame(
            'https://api.aizberg.ru/storage/marketplace-modules/screenshots/gallery.webp',
            $service->publicUrl('marketplace-modules/screenshots/gallery.webp')
        );
    }
}
