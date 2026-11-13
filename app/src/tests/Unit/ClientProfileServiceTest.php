<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Modules\Client\Entities\Client;
use Modules\Client\Services\ClientProfileService;
use Modules\Country\Repositories\CountryRepository;
use Modules\Timezone\Repositories\TimezoneRepository;
use PHPUnit\Framework\TestCase;

class ClientProfileServiceTest extends TestCase
{
    private Container $container;

    private CountryRepository $countryRepository;

    private TimezoneRepository $timezoneRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();
        Facade::setFacadeApplication($this->container);

        $database = Mockery::mock();
        $database->shouldReceive('transaction')
            ->andReturnUsing(static fn (callable $callback) => $callback());
        $this->container->instance('db', $database);

        $this->countryRepository = Mockery::mock(CountryRepository::class);
        $this->timezoneRepository = Mockery::mock(TimezoneRepository::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }

    public function testItUpdatesOnlyProvidedProfileFields(): void
    {
        $client = Mockery::mock(Client::class)->makePartial();
        $client->setRawAttributes([
            'first_name' => 'Old name',
            'last_name' => 'Unchanged surname',
            'email' => 'unchanged@example.test',
            'phone' => '79990000000',
        ], true);
        $client->shouldReceive('save')->once()->andReturnTrue();
        $client->shouldReceive('country')->never();
        $client->shouldReceive('city')->never();

        $timezoneRelation = Mockery::mock(BelongsToMany::class);
        $timezoneRelation->shouldReceive('sync')->once()->with([5]);
        $client->shouldReceive('timezone')->once()->andReturn($timezoneRelation);

        $this->countryRepository->shouldNotReceive('getIdByCode');
        $this->timezoneRepository->shouldReceive('getIdByName')
            ->once()
            ->with('Europe/Moscow')
            ->andReturn(5);

        $this->service()->update($client, [
            'first_name' => 'New name',
            'timezone' => 'Europe/Moscow',
        ]);

        self::assertSame('New name', $client->getAttributes()['first_name']);
        self::assertSame('Unchanged surname', $client->getAttributes()['last_name']);
        self::assertSame('unchanged@example.test', $client->getAttributes()['email']);
        self::assertSame('79990000000', $client->getAttributes()['phone']);
    }

    public function testItUpdatesExplicitlyProvidedRelations(): void
    {
        $client = Mockery::mock(Client::class)->makePartial();
        $client->shouldReceive('save')->once()->andReturnTrue();

        $countryRelation = Mockery::mock(BelongsToMany::class);
        $countryRelation->shouldReceive('sync')->once()->with([175]);
        $client->shouldReceive('country')->once()->andReturn($countryRelation);

        $cityRelation = Mockery::mock(BelongsToMany::class);
        $cityRelation->shouldReceive('sync')->once()->with([10]);
        $client->shouldReceive('city')->once()->andReturn($cityRelation);

        $timezoneRelation = Mockery::mock(BelongsToMany::class);
        $timezoneRelation->shouldReceive('sync')->once()->with([5]);
        $client->shouldReceive('timezone')->once()->andReturn($timezoneRelation);

        $this->countryRepository->shouldReceive('getIdByCode')->once()->with('ru')->andReturn(175);
        $this->timezoneRepository->shouldReceive('getIdByName')
            ->once()
            ->with('Europe/Moscow')
            ->andReturn(5);

        $this->service()->update($client, [
            'country_code' => 'ru',
            'city_id' => 10,
            'timezone' => 'Europe/Moscow',
        ]);

        self::assertSame([], $client->getAttributes());
    }

    private function service(): ClientProfileService
    {
        return new ClientProfileService(
            $this->countryRepository,
            $this->timezoneRepository
        );
    }
}
