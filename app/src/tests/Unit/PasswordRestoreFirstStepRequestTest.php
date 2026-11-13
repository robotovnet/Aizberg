<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Facade;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Illuminate\Validation\PresenceVerifierInterface;
use Mockery;
use Modules\Client\Http\Requests\PasswordRestoreFirstStepRequest;
use Modules\Settings\Repositories\SettingsRepository;
use PHPUnit\Framework\TestCase;

class PasswordRestoreFirstStepRequestTest extends TestCase
{
    private Container $container;

    private Factory $validatorFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();
        $this->container->instance('config', new Repository([
            'client' => require __DIR__ . '/../../Modules/Client/Config/config.php',
        ]));
        Facade::setFacadeApplication($this->container);

        $this->validatorFactory = new Factory(new Translator(new ArrayLoader(), 'en'), $this->container);
        $this->validatorFactory->extend('captcha', static fn ($attribute, $value): bool => $value === '12345');

        $presenceVerifier = Mockery::mock(PresenceVerifierInterface::class);
        $presenceVerifier->shouldReceive('getCount')->withArgs(
            static fn ($table, $column): bool => $table === 'clients' && in_array($column, ['email', 'phone'], true)
        )->andReturnUsing(
            static fn ($table, $column, $value): int => in_array($value, ['client@example.com', '79251112233'], true) ? 1 : 0
        );
        $this->validatorFactory->setPresenceVerifier($presenceVerifier);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }

    /**
     * @dataProvider validationCases
     */
    public function testCaptchaSettingControlsEmailRecoveryOnly(
        string $method,
        string $settingValue,
        array $input,
        array $expectedErrors
    ): void {
        $settings = Mockery::mock(SettingsRepository::class);

        if ($method === 'email') {
            $settings->shouldReceive('getSettingByCode')->once()
                ->with('CAPTCHA_AT_EMAIL_RECOVERY')->andReturn($settingValue);
        } else {
            $settings->shouldNotReceive('getSettingByCode');
        }

        $this->container->instance(SettingsRepository::class, $settings);

        $uri = '/clients/password-recovery/by-' . $method . '/first-step';
        $request = PasswordRestoreFirstStepRequest::create($uri, 'POST', $input);
        $route = (new Route('POST', $uri, []))->name('clients.password_restore.by_' . $method . '.first_step');
        $request->setRouteResolver(static fn (): Route => $route);

        $validator = $this->validatorFactory->make(
            $request->all(),
            $this->container->call([$request, 'rules'])
        );

        self::assertSame($expectedErrors === [], $validator->passes());
        self::assertSame($expectedErrors, $validator->errors()->keys());
    }

    public function validationCases(): array
    {
        $email = ['email' => 'client@example.com'];
        $phone = ['phone' => '79251112233'];

        return [
            'email: enabled, missing captcha' => ['email', 'yes', $email, ['captcha']],
            'email: enabled, empty captcha' => ['email', 'yes', $email + ['captcha' => ''], ['captcha']],
            'email: enabled, invalid captcha' => ['email', 'yes', $email + ['captcha' => 'wrong'], ['captcha']],
            'email: enabled, valid captcha' => ['email', 'yes', $email + ['captcha' => '12345'], []],
            'email: disabled, missing captcha' => ['email', 'no', $email, []],
            'email: disabled, empty captcha' => ['email', 'no', $email + ['captcha' => ''], []],
            'email: disabled, invalid captcha' => ['email', 'no', $email + ['captcha' => 'wrong'], []],
            'email: unknown setting keeps captcha' => ['email', '', $email, ['captcha']],
            'phone: disabled email captcha, missing captcha' => ['phone', 'no', $phone, ['captcha']],
            'phone: disabled email captcha, invalid captcha' => ['phone', 'no', $phone + ['captcha' => 'wrong'], ['captcha']],
            'phone: disabled email captcha, valid captcha' => ['phone', 'no', $phone + ['captcha' => '12345'], []],
            'email: disabled, missing contact' => ['email', 'no', [], ['phone', 'email']],
            'email: disabled, malformed email' => ['email', 'no', ['email' => 'invalid'], ['email']],
            'email: disabled, unknown email' => ['email', 'no', ['email' => 'unknown@example.com'], ['email']],
            'email: disabled, phone lookup' => ['email', 'no', $phone, []],
        ];
    }
}
