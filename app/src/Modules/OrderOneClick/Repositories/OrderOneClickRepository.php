<?php

declare(strict_types=1);

namespace Modules\OrderOneClick\Repositories;

/**
 * Interface OrderOneClickRepository
 * @package Modules\OrderOneClick\Repositories
 */
interface OrderOneClickRepository
{
    /**
     *  Set a list of recipients of information about new orders in one click.
     */
    public function setSubscribers(array $subscribers): self;

    /**
     * Set catalog sections with support for placing orders in one click.
     */
    public function setCatalogSections(array $sections): self;

    /**
     * Get a list of email addresses to which information about new orders is sent in one click.
     */
    public function getSubscribers(): array;

    /**
     * Allows to mark an order as completed.
     */
    public function completed(int $orderId, string $isCompleted): void;

    /**
     * Get catalog sections with the activated ability to place orders in one click.
     */
    public function getCatalogSections(): array;

    /**
     * Returns data of all one click orders.
     * @param  string|null  $onlyCount Return only the number of orders.
     */
    public function all(string $onlyCompleted, string $dateFrom, string $dateTo, int $page, ?string $onlyCount): array;
}
