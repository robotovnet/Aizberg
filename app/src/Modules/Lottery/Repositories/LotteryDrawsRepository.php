<?php

declare(strict_types=1);

namespace Modules\Lottery\Repositories;

/**
 * Interface LotteryDrawsRepository
 * @package Modules\Lottery\Repositories
 */
interface LotteryDrawsRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Returns all lottery draws.
     */
    public function all(): array;
}
