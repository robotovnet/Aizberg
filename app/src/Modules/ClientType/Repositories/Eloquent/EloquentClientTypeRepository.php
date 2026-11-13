<?php

declare(strict_types=1);

namespace Modules\ClientType\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Modules\ClientType\Entities\ClientType;
use Modules\ClientType\Repositories\ClientTypeRepository;
use Modules\PriceType\Entities\PriceType;
use Modules\BonusProgram\Entities\BonusLevel;
use Illuminate\Validation\ValidationException;
use App\Helpers\Helper;

/**
 * Class EloquentClientTypeRepository
 * @package Modules\ClientType\Repositories\Eloquent
 */
class EloquentClientTypeRepository implements ClientTypeRepository
{
    /**
     * @var Model An instance of the Eloquent Model
     */
    protected Model $model;

    /**
     * EloquentLanguageRepository constructor.
     * @param  Model  $model
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
        Helper::havePhraseOnDefaultLang($attributes['name'], 'name');

        if ($attributes['is_default'] === 'Y') {
            $this->resetDefault($this->model->id);
        }

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
            throw new ModelNotFoundException('Client type not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        Helper::havePhraseOnDefaultLang($data['name'], 'name');

        if ($data['is_default'] === 'Y') {
            $this->resetDefault();
        }

        $this->model = $this->model->create($data);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        $bl = BonusLevel::where('client_type_id', $this->model->id)->first();

        if ($bl) {
            return throw ValidationException::withMessages(
                ['delete' => 'Нельзя удалить данный тип клиентов, так как он используется в уровнях бонусной программы (уровень: ' . json_decode($bl->name)->ru . ').']
            );
        }

        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $data = $this->model
            ->with(['priceType' => function ($query) {
                $query->select(PriceType::SELECT_ATTRIBUTES);
            }])
            ->where('id', $this->model->id)
            ->first(ClientType::SELECT_ATTRIBUTES)
            ->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), [$data])[0];
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $all = $this->model
            ->with(['priceType' => function ($query) {
                $query->select(PriceType::SELECT_ATTRIBUTES);
            }])
            ->get(ClientType::SELECT_ATTRIBUTES)
            ->toArray();

        foreach ($all as &$clientType) {
            $clientType = Arr::onlyActiveLang(['name'], App::getLocale(), $clientType);
        }

        return Arr::onlyActiveLang(['name'], App::getLocale(), $all);
    }

    /**
     * Resets default client type.
     */
    private function resetDefault(int $withoutId = 0): void
    {
        $this->model->where('id', '!=', $withoutId)
            ->where('is_default', 'Y')
            ->update(['is_default' => 'N']);
    }
}
