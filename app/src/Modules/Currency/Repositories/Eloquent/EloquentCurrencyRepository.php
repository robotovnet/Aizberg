<?php

declare(strict_types=1);

namespace Modules\Currency\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Modules\Currency\Repositories\CurrencyRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Currency\Entities\Currency;
use App\Helpers\Helper;

/**
 * Class EloquentCurrencyRepository
 * @package Modules\Currency\Repositories\Eloquent
 */
class EloquentCurrencyRepository implements CurrencyRepository
{
    /** @var Model An instance of the Eloquent Model. */
    protected Model $model;

    /**
     * EloquenCurrencyRepository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function getIdByCode(string $code): int
    {
        return ($this->model->where('country_code', $code)->first(['id'])->toArray())['id'];
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
            throw new ModelNotFoundException('Currency not found.');
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
        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $data = $this->model->only(Currency::SELECT_ATTRIBUTES);

        return Arr::onlyActiveLang(['name'], App::getLocale(), [$data])[0];
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $all = $this->model->get(Currency::SELECT_ATTRIBUTES)->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), $all);
    }

    /**
     * Resets default currency.
     */
    private function resetDefault(int $withoutId = 0): void
    {
        $this->model->where('id', '!=', $withoutId)
            ->where('is_default', 'Y')
            ->update(['is_default' => 'N']);
    }
}
