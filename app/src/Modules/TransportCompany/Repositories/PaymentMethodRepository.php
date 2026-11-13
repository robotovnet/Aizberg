<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories;

/**
 * Interface PaymentMethodRepository
 * @package Modules\TransportCompany\Repositories
 */
interface PaymentMethodRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Returns the model id.
     */
    public function getModelId(): int;

    /**
     * Updates payment method data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new payment method.
     */
    public function create(array $data): int;

    /**
     * Deletes the payment method.
     */
    public function delete(): self;

    /**
     * Returns payment method data.
     */
    public function data(): array;

    /**
     * Returns data of all payment methods.
     */
    public function all(): array;

    /**
     * Establishes the order of payment methods.
     * @param  int[]  $orders
     */
    public function order(array $orders): self;
}
