<?php

declare(strict_types=1);

namespace Modules\ServiceCenter\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\City\Entities\City;
use Modules\ServiceCenter\Entities\ServiceCenter;
use Modules\ServiceCenter\Repositories\ServiceCenterRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class EloquentServiceCenterRepository
 * @package Modules\ServiceCenter\Repositories\Eloquent
 */
class EloquentServiceCenterRepository implements ServiceCenterRepository
{
    /** @var Model An instance of the Eloquent Model. */
    protected Model $model;

    /**
     * Repository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        $this->checkTime($attributes['break_work_time']);
        $this->checkTime($attributes['work_time']);

        $this->model->update($attributes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setModel(int $id): self
    {
        try {
            $this->model = $this->model->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ModelNotFoundException('Service Center not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        $this->checkTime($data['break_work_time']);
        $this->checkTime($data['work_time']);

        $this->model = $this->model->create($data);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function all(int $page, int $totalPerPage, int $city): array
    {
        $all = ServiceCenter::with('city')->with('country')->skip($this->getSkipPos($page, $totalPerPage))
            ->take($totalPerPage)
            ->when($city > 0, function ($query) use ($city) {
                return $query->where('city_id', $city);
            })
            ->get(ServiceCenter::SELECT_ATTRIBUTES)
            ->toArray();

        foreach ($all as &$sc) {
            $sc['city']['name']    = (json_decode($sc['city']['name'], true))[App::getLocale()];
            $sc['country']['name'] = (json_decode($sc['country']['name'], true))[App::getLocale()];
        }

        return $all;
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $data = ServiceCenter::find($this->model->id, ServiceCenter::SELECT_ATTRIBUTES)->toArray();
        $data['phone'] = preg_replace("/^8/", "7", $data['phone']);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function total(int $city): int
    {
        return ServiceCenter::when($city > 0, function ($query) use ($city) {
            return $query->where('city_id', $city);
        })->count();
    }

    /**
     * @inheritDoc
     */
    public function cities(): array
    {
        $citiesIds = DB::select("select `city_id`
            from `service_centers`
            where `service_centers`.`deleted_at` is null
        ");

        $ids = [];
        foreach ($citiesIds as $item) {
            $ids[] = $item->city_id;
        }

        $citiesData = City::whereIn('id', $ids)
            ->orderBy('name')
            ->get(City::SELECT_ATTRIBUTES)
            ->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), $citiesData);
    }

    /**
     * Returns skip position.
     */
    private function getSkipPos(int $page, int $totalPerPage): int
    {
        return (--$page * $totalPerPage);
    }

    /**
     * Checks times into json.
     * @throws ValidationException
     */
    private function checkTime(string $jsonData): void
    {
        $data = json_decode($jsonData, true);

        if (is_null($data)) {
            throw ValidationException::withMessages(
                ['break_work_time' => 'Некорректно заполнен график работы']
            );
        }

        $rules = [
            'Mn' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
            'Tu' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
            'Wd' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
            'Th' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
            'Fr' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
            'Sa' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
            'Sn' => 'regex:/^[0-9]{2}:[0-9]{2}\-[0-9]{2}:[0-9]{2}$/i',
        ];

        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            throw ValidationException::withMessages(
                ['break_work_time' => 'Некорректно заполнен график работы']
            );
        }
    }
}
