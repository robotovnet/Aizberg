<?php

declare(strict_types=1);

namespace Modules\Timezone\Repositories\Eloquent;

use Modules\Timezone\Repositories\TimezoneRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EloquentTimezoneRepository
 * @package Modules\Timezone\Repositories\Eloquent
 */
class EloquentTimezoneRepository implements TimezoneRepository
{
    /**
     * @var Model An instance of the Eloquent Model
     */
    protected Model $model;

    /**
     * EloquentTimezoneRepository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function getIdByName(string $name): int
    {
        return ($this->model->where('name', $name)->first(['id'])->toArray())['id'];
    }
}
