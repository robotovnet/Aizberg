<?php

declare(strict_types=1);

namespace Modules\Mask\Repositories\Eloquent;

use Modules\Mask\Repositories\MaskRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EloquentMaskRepository
 * @package Modules\Mask\Repositories\Eloquent
 */
class EloquentMaskRepository implements MaskRepository
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
    public function getAll(string $type): array
    {
        return $this->model
            ->where('type', $type)
            ->get(['code','mask'])
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getByCode(string $type, string $country_code): array
    {
        return $this->model
            ->where('type', $type)
            ->where('code', $country_code)
            ->first(['mask as phone'])
            ->toArray();
    }
}
