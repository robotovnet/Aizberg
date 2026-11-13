<?php

declare(strict_types=1);

namespace Modules\Client\Repositories\Eloquent;

use Modules\City\Entities\City;
use Modules\Client\Entities\Client;
use Modules\Client\Repositories\ClientRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Modules\ClientType\Entities\ClientType;
use Modules\Country\Entities\Country;
use Modules\Currency\Entities\Currency;
use Modules\Order\Entities\Order;
use Modules\Settings\Entities\Settings;
use Modules\Staff\Entities\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class EloquentClientRepository
 * @package Modules\Client\Repositories\Eloquent
 */
class EloquentClientRepository implements ClientRepository
{
    private const DEFAULT_COUNTRY_ID = 175;

    /**
     * @var Model An instance of the Eloquent Model
     */
    protected Model $model;

    /**
     * EloquentClientRepository constructor.
     * @param  Model  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function setModel(int $id): self
    {
        try {
            $this->model = $this->model->withTrashed()->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ModelNotFoundException('Client not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        $this->model = $this->model->create($data);

        return $this->model->getAttribute('id');
    }

    /**
     * @inheritDoc
     */
    public function attach(string $entityName, int $entityId): void
    {
        $this->model->$entityName()->attach($entityId);
    }

    /**
     * @inheritDoc
     */
    public function getClientById(int $clientId): array
    {
        return $this->model->find($clientId)->toArray();
    }

    /**
     * @inheritDoc
     */
    public function setNewPassword(int $clientId, string $newPassword): void
    {
        $client = $this->model->find($clientId);
        $client->password = $newPassword;
        $client->save();
    }

    /**
     * @inheritDoc
     */
    public function findClientByPhone(string $phone): array
    {
        return $this->model->where('phone', $phone)->withTrashed()->first()->toArray();
    }

    /**
     * @inheritDoc
     */
    public function findClientByEmail(string $email): array
    {
        return $this->model->where('email', $email)->withTrashed()->first()->toArray();
    }

    /**
     * @inheritDoc
     */
    public function updateClientProfile(array $attributes): self
    {
        if (isset($attributes['country_code'])) {
            $country = Country::where('code', $attributes['country_code'])->first();
        } else {
            $country = Country::find(self::DEFAULT_COUNTRY_ID);
        }

        $this->model->first_name = $attributes['first_name'];
        $this->model->last_name  = $attributes['last_name'];
        $this->model->email      = $attributes['email'];
        $this->model->phone      = preg_replace('/[^0-9]+/', '', $attributes['phone']);
        $this->model->permanent_type = $attributes['permanent_type'];

        $this->model->country()->detach();
        $this->model->city()->detach();
        $this->model->clientType()->detach();
        $this->model->staff()->detach();

        $this->model->country()->attach($country->id);
        $this->model->city()->attach($attributes['city_id']);
        $this->model->clientType()->attach($attributes['client_type_id']);

        if (isset($attributes['staff_id'])) {
            $this->model->staff()->attach($attributes['staff_id']);

            // Setting up the new customer order manager
            Order::where('client_id', $this->model->id)
                ->update(['staff_id' => $attributes['staff_id']]);
        }

        $this->model->save();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getClientProfile(): array
    {
        $client = $this->model
            ->withTrashed()
            ->where('id', $this->model->id)
            ->with('country')
            ->with('currency')
            ->with('language')
            ->with('city')
            ->with('clientType')
            ->with('staff')
            ->first();

        $client = collect($client)
            ->except(Client::EXCEPT_ATTRIBUTES)
            ->toArray();

        // Get client_type_id
        $clientTypeData = DB::table('client_client_type')
            ->select(DB::raw('client_type_id'))
            ->where('client_id', $this->model->id)
            ->first();

        $client['city'] = Arr::onlyActiveLang(
            ['name'],
            App::getLocale(),
            $client['city']
        );

        $client['country'] = Arr::onlyActiveLang(
            ['name'],
            App::getLocale(),
            $client['country']
        );

        $client['currency'] = Arr::onlyActiveLang(
            ['name'],
            App::getLocale(),
            $client['currency']
        );

        $client['client_type'] = Arr::onlyActiveLang(
            ['name'],
            App::getLocale(),
            $client['client_type']
        );

        $client['language'] = Arr::onlyActiveLang(
            ['name'],
            App::getLocale(),
            $client['language']
        );

        if (isset($client['country'][0])) {
            $client['country'] = collect($client['country'][0])->only(Country::SELECT_ATTRIBUTES);
        }

        if (isset($client['currency'][0])) {
            $client['currency'] = collect($client['currency'][0])->only(Currency::SELECT_ATTRIBUTES);
        }

        if (isset($client['language'][0])) {
            $client['language'] = collect($client['language'][0])->except(['pivot']);
        }

        if (isset($client['city'][0])) {
            $client['city'] = collect($client['city'][0])->only(City::SELECT_ATTRIBUTES);
        }

        if (isset($client['client_type'][0])) {
            $client['client_type'] = collect($client['client_type'][0])->only(ClientType::SELECT_ATTRIBUTES);
        }

        // add 'client_type_id' to 'client_type'
        $client['client_type']['client_type_id'] = $clientTypeData->client_type_id;

        if (isset($client['staff'][0])) {
            $client['staff'] = collect($client['staff'][0])->only(Staff::SELECT_ATTRIBUTES);
        }

        $client['created_at'] = Carbon::parse($client['created_at'])->setTimezone('Europe/Moscow')->format('d.m.Y');

        $latest = Order::where('client_id', $client['id'])->latest()->first(['created_at']);
        $client['last_order_date'] = (isset($latest['created_at'])) ?
            Carbon::parse($latest['created_at'])->setTimezone('Europe/Moscow')->format('d.m.Y H:i')
            :
            null;

        return $client;
    }

    /**
     * @inheritDoc
     * NOTICE: При поиске по $search другие параметры не учитываются
     */
    public function all(
        int $cityId,
        int $staffId,
        string $regDateFrom,
        string $regDateTo,
        int $clientTypeId,
        string $search,
        int $page,
        ?string $onlyCount,
        string $sortBy = '',
        string $sortOrder = 'desc'
    ): array {
        $query = Client::with('country')
            ->when($cityId > 0, function ($query) use ($cityId) {
                return $query->whereHas('city', function ($query) use ($cityId) {
                    $query->where('cities.id', $cityId);
                });
            })
            ->when($clientTypeId > 0, function ($query) use ($clientTypeId) {
                return $query->whereHas('clientType', function ($query) use ($clientTypeId) {
                    $query->where('client_types.id', $clientTypeId);
                });
            })
            ->when($staffId > 0, function ($query) use ($staffId) {
                return $query->whereHas('staff', function ($query) use ($staffId) {
                    $query->where('staff.id', $staffId);
                });
            })
            ->when($regDateFrom != '' && $regDateTo != '', function ($query) use ($regDateFrom, $regDateTo) {
                return $query->whereRaw(
                    '(created_at >= ? AND created_at <= ?)',
                    [
                        $regDateFrom . ' 00:00:00',
                        $regDateTo . ' 23:59:59'
                    ]
                );
            })
            ->when(strlen($search) >= 3, function ($query) use ($search) {
                return $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('phone', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%');
                });
            })
            ->with('city')
            ->with('clientType')
            ->with('staff')
            ->withTrashed();

        if (!$onlyCount) {
            // Добавляем подзапросы для сортировки
            $query->addSelect([
                '*',
                DB::raw("CONCAT(last_name, ' ', first_name) as full_name"),
                DB::raw("(SELECT COUNT(*) FROM orders WHERE orders.client_id = clients.id) as total_orders"),
                DB::raw("(SELECT COALESCE(SUM(total_order_cost), 0) FROM orders WHERE orders.client_id = clients.id AND order_status IN ('complete', 'delivered')) as ransom_amount")
            ]);

            // Применяем сортировку
            if ($sortBy === 'ransom_amount' || $sortBy === 'total_orders') {
                $query->orderBy($sortBy, $sortOrder);
            }

            $clients = $query
                ->skip(($page - 1) * (int)Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'])
                ->take((int)Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'])
                ->get()
                ->toArray();

            foreach ($clients as &$client) {
                foreach (Client::EXCEPT_ATTRIBUTES as $exceptKey) {
                    unset($client[$exceptKey]);
                }

                $client['created_at'] = Carbon::parse($client['created_at'])->setTimezone('Europe/Moscow')->format('d.m.Y');
                // Теперь эти значения уже есть в результате запроса
                $client['total_orders'] = (int)$client['total_orders'];
                $client['ransom_amount'] = (int)$client['ransom_amount'];

                $client['city'] = Arr::onlyActiveLang(
                    ['name'],
                    App::getLocale(),
                    $client['city']
                );

                $client['client_type'] = Arr::onlyActiveLang(
                    ['name'],
                    App::getLocale(),
                    $client['client_type']
                );

                $client['country'] = Arr::onlyActiveLang(
                    ['name'],
                    App::getLocale(),
                    $client['country']
                );
            }

            return $clients;
        } else {
            return ['total' => $query->count()];
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        $this->model->delete();

        return $this;
    }
}
