<?php

declare(strict_types=1);

namespace Modules\Lottery\Repositories;

use Modules\Lottery\Entities\LotteryTickets;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Interface LotteryTicketsRepository
 * @package Modules\Lottery\Repositories
 */
interface LotteryTicketsRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Returns all lottery tickets.
     */
    public function all(int $generationId): StreamedResponse;

    /**
     * Generates a new lottery ticket.
     */
    public function generator(int $totalTickets): int;

    /**
     * Returns the generation ID.
     */
    public function getGenerationId(): int;

    /**
     * Saves a ticket.
     */
    public function saveTicket(string $code, int $generationId): LotteryTickets;

    /**
     * Returns the total registered tickets.
     */
    public function getTotalRegisteredTickets(): int;

    /**
     * Returns list of generations with ticket counts
     * 
     * @return array Array of generations with their ticket counts
     */
    public function getGenerations(): array;
}
