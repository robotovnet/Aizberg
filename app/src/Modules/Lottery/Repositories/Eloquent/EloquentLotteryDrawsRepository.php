<?php

declare(strict_types=1);

namespace Modules\Lottery\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Lottery\Entities\LotteryDraws;
use Modules\Lottery\Repositories\LotteryDrawsRepository;
use Illuminate\Support\Facades\App;

/**
 * Class EloquentLotteryDrawsRepository
 * @package Modules\Lottery\Repositories\Eloquent
 */
class EloquentLotteryDrawsRepository implements LotteryDrawsRepository
{
    public function __construct(
        private Model $model
    ) {}

    /**
     * @inheritDoc
     */
    public function setModel(int $id): self
    {
        try {
            $this->model = $this->model->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ModelNotFoundException('Lottery draws not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $draws = LotteryDraws::with('prize.product')
            ->with('ticket')
            ->with('app')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->toArray();

        $draws = array_map(function ($draw) {
            $draw['prize']['product']['name'] = json_decode($draw['prize']['product']['name'], true)[App::getLocale()];
            return $draw;
        }, $draws);

        return $draws;
    }
}
