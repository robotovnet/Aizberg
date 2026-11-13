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
use Modules\MarketplaceModules\Services\MarketplaceModuleIconService;
use PHPUnit\Framework\TestCase;

class MarketplaceModuleIconServiceTest extends TestCase
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

    public function testItBuildsPublicIconUrlFromApiHost(): void
    {
        $service = $this->service('api.aizberg.ru');

        self::assertSame(
            'https://api.aizberg.ru/storage/marketplace-modules/icons/seed-content-scheduler.png',
            $service->publicUrl('marketplace-modules/icons/seed-content-scheduler.png')
        );
    }

    public function testItPreservesConfiguredSchemeAndRemovesExtraSlashes(): void
    {
        $service = $this->service('http://api.aizberg.test/');

        self::assertSame(
            'http://api.aizberg.test/storage/marketplace-modules/icons/test.png',
            $service->publicUrl('/marketplace-modules/icons/test.png')
        );
    }

    public function testItReturnsNullForMissingIconPath(): void
    {
        $service = $this->service('api.aizberg.ru');

        self::assertNull($service->publicUrl(null));
        self::assertNull($service->publicUrl(''));
    }

    public function testItCropsAndDownscalesIconToConfiguredSize(): void
    {
        $service = $this->service('api.aizberg.ru');

        $path = $service->store($this->pngUpload(400, 200));

        self::assertStringStartsWith('marketplace-modules/icons/', $path);
        self::assertStringEndsWith('.png', $path);
        self::assertSame([112, 112], $this->storedImageSize($path));
    }

    public function testItUsesIconSizeFromConfig(): void
    {
        $service = $this->service('api.aizberg.ru', 64, 32);

        $path = $service->store($this->pngUpload(300, 300));

        self::assertSame([64, 32], $this->storedImageSize($path));
    }

    public function testItDoesNotUpscaleSmallIcon(): void
    {
        $service = $this->service('api.aizberg.ru');

        $path = $service->store($this->pngUpload(50, 50));

        self::assertSame([50, 50], $this->storedImageSize($path));
    }

    public function testItStoresSvgIconWithoutChanges(): void
    {
        $service = $this->service('api.aizberg.ru');
        $svg = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512"></svg>';
        $sourcePath = tempnam(sys_get_temp_dir(), 'icon');
        file_put_contents($sourcePath, $svg);

        $path = $service->store(new UploadedFile($sourcePath, 'icon.svg', 'image/svg+xml', null, true));

        self::assertSame($svg, file_get_contents($this->storageRoot . '/' . $path));
    }

    public function testItRecompressesStoredIconInPlace(): void
    {
        $service = $this->service('api.aizberg.ru');
        $path = 'marketplace-modules/icons/legacy.png';
        $this->putStoredPng($path, 512, 512);

        self::assertTrue($service->recompressStored($path));
        self::assertSame([112, 112], $this->storedImageSize($path));
        self::assertFalse($service->recompressStored($path));
    }

    public function testDryRunDoesNotChangeStoredIcon(): void
    {
        $service = $this->service('api.aizberg.ru');
        $path = 'marketplace-modules/icons/legacy.png';
        $this->putStoredPng($path, 512, 512);

        self::assertTrue($service->recompressStored($path, true));
        self::assertSame([512, 512], $this->storedImageSize($path));
    }

    public function testItSkipsStoredSvgIcon(): void
    {
        $service = $this->service('api.aizberg.ru');
        $path = 'marketplace-modules/icons/legacy.svg';
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512"></svg>';
        mkdir(dirname($this->storageRoot . '/' . $path), 0777, true);
        file_put_contents($this->storageRoot . '/' . $path, $svg);

        self::assertFalse($service->recompressStored($path));
        self::assertSame($svg, file_get_contents($this->storageRoot . '/' . $path));
    }

    private function service(string $apiHost, int $iconWidth = 112, int $iconHeight = 112): MarketplaceModuleIconService
    {
        $this->storageRoot = sys_get_temp_dir() . '/marketplace-icon-test-' . uniqid('', true);

        $container = new Container();
        $container->instance('config', new Repository([
            'marketplacemodules' => [
                'disk' => 'public',
                'icon_upload_dir' => 'marketplace-modules/icons',
                'icon_width' => $iconWidth,
                'icon_height' => $iconHeight,
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

        return new MarketplaceModuleIconService();
    }

    private function pngUpload(int $width, int $height): UploadedFile
    {
        $image = imagecreatetruecolor($width, $height);
        $path = tempnam(sys_get_temp_dir(), 'icon');
        imagepng($image, $path);
        imagedestroy($image);

        return new UploadedFile($path, 'icon.png', 'image/png', null, true);
    }

    private function putStoredPng(string $relativePath, int $width, int $height): void
    {
        $absolutePath = $this->storageRoot . '/' . $relativePath;
        mkdir(dirname($absolutePath), 0777, true);
        $image = imagecreatetruecolor($width, $height);
        imagepng($image, $absolutePath);
        imagedestroy($image);
    }

    /** @return array{0: int, 1: int} */
    private function storedImageSize(string $relativePath): array
    {
        $size = getimagesize($this->storageRoot . '/' . $relativePath);
        self::assertNotFalse($size);

        return [$size[0], $size[1]];
    }
}
