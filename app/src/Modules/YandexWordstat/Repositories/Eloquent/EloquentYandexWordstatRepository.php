<?php

namespace Modules\YandexWordstat\Repositories\Eloquent;

use Modules\YandexWordstat\Repositories\YandexWordstatRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class EloquentYandexWordstatRepository
 * @package Modules\YandexWordstat\Repositories\Eloquent
 */
class EloquentYandexWordstatRepository implements YandexWordstatRepository
{
    /** @var Model An instance of the Eloquent Model. */
    public Model $model;

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
            throw new ModelNotFoundException('YandexWordstat not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save(array $data): array
    {
        return $this->model->create($data);
    }
}
