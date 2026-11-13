<?php

declare(strict_types=1);

namespace Modules\Language\Repositories\Eloquent;

use Modules\Language\Repositories\LanguageRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EloquentLanguageRepository
 * @package Modules\Language\Repositories\Eloquent
 */
class EloquentLanguageRepository implements LanguageRepository
{
    /** @var Model An instance of the Eloquent Model. */
    protected Model $model;

    /**
     * EloquentLanguageRepository constructor.
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
        return ($this->model->where('code', $code)->first(['id'])->toArray())['id'];
    }

    /**
     * @inheritDoc
     */
    public function checkSupportsLanguage(string $code): bool
    {
        return $this->model->where('code', $code)->limit(1)->exists();
    }
}
