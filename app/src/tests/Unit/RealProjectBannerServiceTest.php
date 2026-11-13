<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Facade;
use Modules\RealProjects\Services\RealProjectBannerService;
use PHPUnit\Framework\TestCase;

class RealProjectBannerServiceTest extends TestCase
{
    private ?string $storageRoot = null;

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);

        if ($this->storageRoot !== null) {
            (new Filesystem())->deleteDirectory($this->storageRoot);
        }

        parent::tearDown();
    }

    public function testItBuildsAbsoluteUrlFromApiHost(): void
    {
        $service = $this->service('api.aizberg.ru');

        self::assertSame(
            'https://api.aizberg.ru/storage/real-projects/example-shop.svg',
            $service->publicUrl('real-projects/example-shop.svg')
        );
    }

    public function testItPreservesConfiguredSchemeAndRemovesExtraSlashes(): void
    {
        $service = $this->service('http://api.aizberg.test/');

        self::assertSame(
            'http://api.aizberg.test/storage/real-projects/example-blog.svg',
            $service->publicUrl('/real-projects/example-blog.svg')
        );
    }

    public function testItBuildsRelativeStorageUrlWithoutApiHost(): void
    {
        $service = $this->service();

        self::assertSame('/storage/real-projects/example-shop.svg', $service->publicUrl('real-projects/example-shop.svg'));
        self::assertSame('/storage/real-projects/example-blog.svg', $service->publicUrl('/real-projects/example-blog.svg'));
    }

    public function testItReturnsNullForMissingBannerPath(): void
    {
        $service = $this->service();

        self::assertNull($service->publicUrl(null));
        self::assertNull($service->publicUrl(''));
    }

    public function testItStoresSvgUnchangedAndDeletesIt(): void
    {
        $service = $this->service();
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"><rect width="10" height="10"/></svg>';
        $source = tempnam(sys_get_temp_dir(), 'banner');
        file_put_contents($source, $svg);
        $file = new UploadedFile($source, 'example-shop.svg', 'image/svg+xml', null, true);

        $path = $service->store($file);

        self::assertStringStartsWith('real-projects/', $path);
        self::assertSame($svg, file_get_contents($this->storageRoot . '/' . $path));

        $service->deleteByStoredPath($path);

        self::assertFileDoesNotExist($this->storageRoot . '/' . $path);
    }

    private function service(?string $apiHost = null): RealProjectBannerService
    {
        $this->storageRoot = sys_get_temp_dir() . '/real-projects-banner-test-' . uniqid('', true);

        $container = new Container();
        $container->instance('config', new Repository([
            'real_projects' => [
                'disk' => 'public',
                'banner_upload_dir' => 'real-projects',
                'api_host' => $apiHost,
            ],
            'filesystems' => [
                'disks' => [
                    'public' => ['driver' => 'local', 'root' => $this->storageRoot],
                ],
            ],
        ]));
        $filesystem = new FilesystemManager($container);
        $container->instance('filesystem', $filesystem);
        $container->instance(FilesystemFactory::class, $filesystem);
        Container::setInstance($container);
        Facade::setFacadeApplication($container);

        return new RealProjectBannerService();
    }
}
