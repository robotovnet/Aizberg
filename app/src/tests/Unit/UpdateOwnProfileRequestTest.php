<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Mockery;
use Modules\Client\Http\Requests\UpdateOwnProfileRequest;
use PHPUnit\Framework\TestCase;

class UpdateOwnProfileRequestTest extends TestCase
{
    private Container $container;

    private Factory $validatorFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();
        Facade::setFacadeApplication($this->container);

        $auth = Mockery::mock();
        $guard = Mockery::mock();
        $guard->shouldReceive('id')->andReturn(42);
        $auth->shouldReceive('guard')->with('client')->andReturn($guard);
        $this->container->instance('auth', $auth);

        $translator = new Translator(new ArrayLoader(), 'en');
        $this->container->instance('translator', $translator);
        $this->validatorFactory = new Factory($translator, $this->container);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }

    public function testItAcceptsAnySingleSupportedField(): void
    {
        $request = UpdateOwnProfileRequest::create('', 'PATCH', [
            'first_name' => 'New name',
        ]);

        $validator = $this->validatorFactory->make($request->all(), $request->rules());
        $request->withValidator($validator);

        self::assertFalse($validator->fails());
    }

    public function testItRejectsRequestWithoutSupportedFields(): void
    {
        $request = UpdateOwnProfileRequest::create('', 'PATCH', [
            'unsupported_field' => 'value',
        ]);

        $validator = $this->validatorFactory->make($request->all(), $request->rules());
        $request->withValidator($validator);

        self::assertTrue($validator->fails());
        self::assertTrue($validator->errors()->has('profile'));
    }
}
