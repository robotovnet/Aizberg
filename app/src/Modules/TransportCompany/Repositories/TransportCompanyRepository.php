<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories;

/**
 * Interface TransportCompanyRepository
 * @package Modules\TransportCompany\Repositories
 */
interface TransportCompanyRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates transport company data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new transport company.
     */
    public function create(array $data): self;

    /**
     * Deletes the transport company.
     */
    public function delete(): self;

    /**
     * Returns transport company data.
     */
    public function data(): array;

    /**
     * Returns data of all transport companies.
     */
    public function all(): array;

    /**
     * Returns id of transport company.
     */
    public function getId(): int;

    /**
     * Establishes the order of transport companies.
     * @param  int[]  $orders
     */
    public function order(array $orders): self;

    /**
     * Returns the maximum order value.
     */
    public function getMaxOrder(): int;
}
