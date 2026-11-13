<?php

declare(strict_types=1);

namespace Modules\BonusProgram\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Modules\BonusProgram\Entities\BonusLevel;
use Modules\BonusProgram\Repositories\BonusProgramRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\Helper;

/**
 * Class EloquentBonusProgramRepository
 * @package Modules\BonusProgram\Repositories\Eloquent
 */
class EloquentBonusProgramRepository implements BonusProgramRepository
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
    public function setModel(int $id): self
    {
        try {
            $this->model = $this->model->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ModelNotFoundException('Bonus Level not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        Helper::havePhraseOnDefaultLang($data['name'], 'name');

        if ($data['is_entry'] == 'Y') {
            BonusLevel::where('id', '>', 0)->update(['is_entry' => 'N']);
        }

        $this->model = $this->model->create($data);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        Helper::havePhraseOnDefaultLang($attributes['name'], 'name');

        if ($attributes['is_entry'] == 'Y') {
            $levelId = request()->segment(2);
            BonusLevel::where('id', '!=', $levelId)->update(['is_entry' => 'N']);
        }

        $this->model->update($attributes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        $this->model->forceDelete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $all = BonusLevel::get(BonusLevel::SELECT_ATTRIBUTES)
            ->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), $all);
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $data = BonusLevel::find($this->model->id, BonusLevel::SELECT_ATTRIBUTES)
            ->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), [$data])[0];
    }
}
