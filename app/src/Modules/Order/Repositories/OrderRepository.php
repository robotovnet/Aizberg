<?php

declare(strict_types=1);

namespace Modules\Order\Repositories;

/**
 * Interface OrderRepository
 * @package Modules\Order\Repositories
 */
interface OrderRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Sets the track number assigned to the order.
     */
    public function changeTracknumber(int $orderId, string $tracknumber): self;

    /**
     * Changes the order payment method.
     */
    public function changeMethodPay(int $orderId, int $paymentMethodId): self;

    /**
     * Changes the order status.
     */
    public function changeStatus(int $orderId, string $status): self;

    /**
     * Thanks for the feedback.
     */
    public function thanksFeedback(int $orderId): self;

    /**
     * Returns order details.
     */
    public function details(int $orderId): array;

    /**
     * Returns data of all orders.
     * @param  int          $page      Page sequence number.
     * @param  array        $filters   Sets the conditions that the returned orders must meet.
     * @param  string       $search    Search query for finding orders by client name or phone or email.
     * @param  string|null  $onlyCount Return only the number of orders.
     */

    public function all(
        int $page,
        array $filters,
        string $search,
        ?string $onlyCount,
    ): array;

    /**
     * Creates a new order.
     */
    public function create(array $data): int;
}
