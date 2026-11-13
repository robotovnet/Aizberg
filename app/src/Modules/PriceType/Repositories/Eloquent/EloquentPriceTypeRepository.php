<?php

declare(strict_types=1);

namespace Modules\PriceType\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Modules\ClientType\Entities\ClientType;
use Modules\PriceType\Entities\PriceType;
use Modules\PriceType\Repositories\PriceTypeRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\Helper;

/**
 * Class EloquentStaffRepository
 * @package Modules\PriceType\Repositories\Eloquent
 */
class EloquentPriceTypeRepository implements PriceTypeRepository
{
    /** @var Model An instance of the Eloquent Model */
    protected Model $model;

    /**
     * EloquentPriceTypeRepository constructor.
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
            throw new ModelNotFoundException('Price type not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        Helper::havePhraseOnDefaultLang($data['name'], 'name');
        $this->model = $this->model->create($data);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        $total = ClientType::where('price_type_id', $this->model->id)->count();

        if ($total > 0) {
            throw ValidationException::withMessages(
                ['delete' => 'Данный тип цены нельзя удалить, пока он используется в типе клиентов']
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
            ->only(PriceType::SELECT_ATTRIBUTES);

        return Arr::onlyActiveLang(['name'], App::getLocale(), [$data])[0];
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $all = $this->model->get(PriceType::SELECT_ATTRIBUTES)->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), $all);
    }
}
