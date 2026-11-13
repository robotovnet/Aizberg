<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidatorFactory;
use Modules\RealProjects\Http\Requests\CreateOrUpdateRealProjectRequest;
use PHPUnit\Framework\TestCase;

class RealProjectRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container();
        $container->instance('config', new Repository([
            'real_projects' => ['banner_max_size' => 4096],
        ]));
        Container::setInstance($container);
        Facade::setFacadeApplication($container);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);

        parent::tearDown();
    }

    public function testBannerIsRequiredOnlyWhenCreating(): void
    {
        $createRequest = new CreateOrUpdateRealProjectRequest();
        $createRequest->setRouteResolver(static fn () => null);

        $updateRequest = new CreateOrUpdateRealProjectRequest();
        $updateRequest->setRouteResolver(static fn () => new class {
            public function parameter(string $name, $default = null)
            {
                return $name === 'id' ? '5' : $default;
            }
        });

        $createRules = $createRequest->rules();
        $updateRules = $updateRequest->rules();

        self::assertContains('required', $createRules['banner']);
        self::assertContains('nullable', $updateRules['banner']);
        self::assertNotContains('required', $updateRules['banner']);

        foreach ([$createRules, $updateRules] as $rules) {
            self::assertContains('mimes:svg,png,jpg,jpeg,webp', $rules['banner']);
            self::assertContains('max:4096', $rules['banner']);
        }
    }

    /**
     * @dataProvider urlProvider
     */
    public function testUrlMustUseHttpOrHttps(string $url, bool $isValid): void
    {
        $request = new CreateOrUpdateRealProjectRequest();
        $request->setRouteResolver(static fn () => null);

        $validator = (new ValidatorFactory(new Translator(new ArrayLoader(), 'ru')))
            ->make(['url' => $url], ['url' => $request->rules()['url']]);

        self::assertSame($isValid, $validator->passes(), $url);
    }

    public function urlProvider(): array
    {
        return [
            'https'      => ['https://example-shop.com', true],
            'http path'  => ['http://example-blog.com/catalog?x=1', true],
            'ftp'        => ['ftp://example-blog.com', false],
            'javascript' => ['javascript://example-blog.com/%0Aalert(1)', false],
            'no scheme'  => ['example-blog.com', false],
        ];
    }
}
