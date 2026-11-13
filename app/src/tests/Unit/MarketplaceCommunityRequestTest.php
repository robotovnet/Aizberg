<?php

declare(strict_types=1);

namespace Tests\Unit;

use Modules\Client\Http\Requests\UploadClientUserpicRequest;
use Modules\MarketplaceModules\Enums\MarketplaceRatingValue;
use Modules\MarketplaceModules\Http\Requests\StoreModuleChangelogRequest;
use Modules\MarketplaceModules\Http\Requests\StoreRatingRequest;
use Modules\MarketplaceModules\Http\Requests\StoreReviewHelpfulnessRequest;
use PHPUnit\Framework\TestCase;

class MarketplaceCommunityRequestTest extends TestCase
{
    public function testRatingUsesFivePointIntegerScale(): void
    {
        self::assertSame([1, 2, 3, 4, 5], MarketplaceRatingValue::all());
        self::assertContains('integer', (new StoreRatingRequest())->rules()['value']);
    }

    public function testHelpfulnessRequiresBooleanValue(): void
    {
        self::assertSame(
            ['required', 'boolean'],
            (new StoreReviewHelpfulnessRequest())->rules()['is_helpful']
        );
    }

    public function testChangelogRequiresVersionAndListOfChanges(): void
    {
        $rules = (new StoreModuleChangelogRequest())->rules();

        self::assertContains('max:64', $rules['version']);
        self::assertContains('array', $rules['changes']);
        self::assertContains('min:1', $rules['changes']);
        self::assertContains('max:2000', $rules['changes.*']);
    }

    public function testUserpicIsLimitedToTenMegabytesAndSupportedFormats(): void
    {
        $rules = (new UploadClientUserpicRequest())->rules()['userpic'];

        self::assertContains('max:10240', $rules);
        self::assertContains('mimes:jpg,jpeg,png,gif,webp', $rules);
        self::assertContains('mimetypes:image/jpeg,image/png,image/gif,image/webp', $rules);
    }
}
