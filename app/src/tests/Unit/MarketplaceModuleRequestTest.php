<?php

declare(strict_types=1);

namespace Tests\Unit;

use Modules\MarketplaceModules\Http\Requests\StoreMarketplaceModuleRequest;
use Modules\MarketplaceModules\Http\Requests\UpdateMarketplaceModuleRequest;
use PHPUnit\Framework\TestCase;

class MarketplaceModuleRequestTest extends TestCase
{
    public function testPaidPricingValuesAreNormalized(): void
    {
        $request = TestableStoreMarketplaceModuleRequest::create('/', 'POST', [
            'pricing_type' => 'paid',
            'price' => '1490.00',
            'currency' => ' rub ',
        ]);

        $request->prepareInput();

        self::assertSame('1490.00', $request->input('price'));
        self::assertSame('RUB', $request->input('currency'));
    }

    public function testNonPaidPricingValuesAreCleared(): void
    {
        $request = TestableStoreMarketplaceModuleRequest::create('/', 'POST', [
            'pricing_type' => 'free',
            'price' => '1490.00',
            'currency' => 'RUB',
        ]);

        $request->prepareInput();

        self::assertNull($request->input('price'));
        self::assertNull($request->input('currency'));
    }

    public function testCreateAndUpdateLimitScreenshotsAndRequirePaidPrice(): void
    {
        $storeRules = (new StoreMarketplaceModuleRequest())->rules();
        $updateRequest = new UpdateMarketplaceModuleRequest();
        $updateRequest->setRouteResolver(static fn () => null);
        $updateRules = $updateRequest->rules();

        foreach ([$storeRules, $updateRules] as $rules) {
            self::assertContains('max:10', $rules['screenshots']);
            self::assertContains('mimes:jpg,jpeg,png,webp', $rules['screenshots.*']);
            self::assertContains('mimetypes:image/jpeg,image/png,image/webp', $rules['screenshots.*']);
            self::assertContains('required_if:pricing_type,paid', $rules['price']);
            self::assertContains('required_if:pricing_type,paid', $rules['currency']);
        }
    }
}

class TestableStoreMarketplaceModuleRequest extends StoreMarketplaceModuleRequest
{
    public function prepareInput(): void
    {
        $this->prepareForValidation();
    }
}
