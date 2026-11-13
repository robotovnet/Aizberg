<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Modules\MarketplaceModules\Entities\MarketplaceModule;
use Modules\MarketplaceModules\Entities\MarketplaceModuleScreenshot;
use Modules\MarketplaceModules\Entities\MarketplaceModuleTag;
use Modules\MarketplaceModules\Enums\MarketplacePricingType;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;
use Modules\MarketplaceModules\Services\MarketplaceModuleIconService;
use Modules\MarketplaceModules\Services\MarketplaceModuleScreenshotService;
use Modules\MarketplaceModules\Services\MarketplaceRatingAggregatorService;
use Modules\Client\Services\ClientUserpicService;
use PHPUnit\Framework\TestCase;

class MarketplaceModuleRepositorySerializationTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testListItemContainsCatalogFieldsAndPaidPrice(): void
    {
        $iconService = Mockery::mock(MarketplaceModuleIconService::class);
        $iconService->shouldReceive('publicUrl')->once()->with('icons/module.png')->andReturn('/icon.png');

        $repository = new MarketplaceModuleRepository(
            $iconService,
            Mockery::mock(MarketplaceModuleScreenshotService::class),
            Mockery::mock(MarketplaceRatingAggregatorService::class),
            Mockery::mock(ClientUserpicService::class)
        );
        $module = new MarketplaceModule([
            'display_name' => 'Paid module',
            'icon_path' => 'icons/module.png',
            'aggregate_rating' => 4.5,
            'author_display_name' => 'Author',
            'pricing_type' => MarketplacePricingType::PAID,
            'price' => '1490.00',
            'currency' => 'RUB',
            'short_description' => 'Short description',
            'views_count' => 42,
            'website_url' => 'https://example.test',
            'install_slug' => 'paid-module',
        ]);

        self::assertSame([
            'display_name' => 'Paid module',
            'icon_url' => '/icon.png',
            'aggregate_rating' => 4.5,
            'author_display_name' => 'Author',
            'pricing_type' => 'paid',
            'short_description' => 'Short description',
            'views_count' => 42,
            'website_url' => 'https://example.test',
            'install_slug' => 'paid-module',
            'price' => 1490.0,
            'currency' => 'RUB',
        ], $repository->toListItem($module));
    }

    public function testFreeListItemContainsNullPriceAndCurrency(): void
    {
        $iconService = Mockery::mock(MarketplaceModuleIconService::class);
        $iconService->shouldReceive('publicUrl')->once()->andReturnNull();
        $repository = new MarketplaceModuleRepository(
            $iconService,
            Mockery::mock(MarketplaceModuleScreenshotService::class),
            Mockery::mock(MarketplaceRatingAggregatorService::class),
            Mockery::mock(ClientUserpicService::class)
        );
        $module = new MarketplaceModule([
            'pricing_type' => MarketplacePricingType::FREE,
        ]);

        $item = $repository->toListItem($module);

        self::assertArrayHasKey('price', $item);
        self::assertArrayHasKey('currency', $item);
        self::assertNull($item['price']);
        self::assertNull($item['currency']);
    }

    public function testFullCardContainsOrderedPublicScreenshotUrls(): void
    {
        $iconService = Mockery::mock(MarketplaceModuleIconService::class);
        $iconService->shouldReceive('publicUrl')->once()->andReturn('/icon.png');
        $screenshotService = Mockery::mock(MarketplaceModuleScreenshotService::class);
        $screenshotService->shouldReceive('publicUrl')->once()->with('screens/first.webp')->andReturn('/first.webp');
        $screenshotService->shouldReceive('publicUrl')->once()->with('screens/second.png')->andReturn('/second.png');

        $repository = new MarketplaceModuleRepository(
            $iconService,
            $screenshotService,
            Mockery::mock(MarketplaceRatingAggregatorService::class),
            Mockery::mock(ClientUserpicService::class)
        );
        $module = new MarketplaceModule([
            'pricing_type' => MarketplacePricingType::PAID,
            'price' => '25.50',
            'currency' => 'USD',
        ]);
        $module->setRelation('tags', new Collection([
            new MarketplaceModuleTag(['tag' => 'seo']),
        ]));
        $firstScreenshot = new MarketplaceModuleScreenshot(['path' => 'screens/first.webp', 'position' => 0]);
        $firstScreenshot->id = 11;
        $secondScreenshot = new MarketplaceModuleScreenshot(['path' => 'screens/second.png', 'position' => 1]);
        $secondScreenshot->id = 12;
        $module->setRelation('screenshots', new Collection([$firstScreenshot, $secondScreenshot]));

        $card = $repository->toFullCard($module);

        self::assertSame([
            ['id' => 11, 'url' => '/first.webp', 'position' => 0],
            ['id' => 12, 'url' => '/second.png', 'position' => 1],
        ], $card['screenshots']);
        self::assertSame(25.5, $card['price']);
        self::assertSame('USD', $card['currency']);
    }

    public function testPricingTypesIncludePaid(): void
    {
        self::assertSame(['free', 'free_trial', 'paid'], MarketplacePricingType::all());
    }
}
