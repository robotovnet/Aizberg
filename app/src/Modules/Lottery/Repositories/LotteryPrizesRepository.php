<?php

declare(strict_types=1);

namespace Modules\Lottery\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface LotteryPrizesRepository
 * @package Modules\Lottery\Repositories
 */
interface LotteryPrizesRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Returns data of all lottery prizes.
     */
    public function all(): array;

    /**
     * Adds a new prize to the lottery.
     */
    public function add(string $productVendorCode): int;

    /**
     * Deletes a prize from the lottery.
     */
    public function delete(int $prizeId): bool;
}
