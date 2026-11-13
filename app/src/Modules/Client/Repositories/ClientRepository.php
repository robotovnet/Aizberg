<?php

declare(strict_types=1);

namespace Modules\Client\Repositories;

/**
 * Interface ClientRepository
 * @package Modules\Client\Repositories
 */
interface ClientRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Creates a new user.
     */
    public function create(array $data): int;

    /**
     * Attaches the entity to the client.
     */
    public function attach(string $entityName, int $entityId): void;

    /**
     * Find a client by id.
     */
    public function getClientById(int $clientId): array;

    /**
     * Set new password for client
     */
    public function setNewPassword(int $clientId, string $newPassword): void;

    /**
     * Find a client by phone.
     */
    public function findClientByPhone(string $phone): array;

    /**
     * Find a client by email.
     */
    public function findClientByEmail(string $email): array;

    /**
     * Updates client profile data.
     */
    public function updateClientProfile(array $attributes): self;

    /**
     * Returns user profile data by id.
     */
    public function getClientProfile(): array;

    /**
     * Returns users data by params.
     * @param  int  $cityId         City Id.
     * @param  int  $staffId        Staff Id.
     * @param  string $regDateFrom  Date of registration (from)
     * @param  string $regDateTo    Date of registration (to)
     * @param  int  $clientTypeId   Client type Id
     * @param  string  $search      Search query (name, phone, email)
     * @param  int  $page           Page sequence number.
     * @param  string|null  $onlyCount Return only the number of clients.
     * @param  string  $sortBy      Sort by field (ransom_amount or total_orders)
     * @param  string  $sortOrder   Sort order (asc or desc)
     * @return array
     */
    public function all(
        int $cityId,
        int $staffId,
        string $regDateFrom,
        string $regDateTo,
        int $clientTypeId,
        string $search,
        int $page,
        ?string $onlyCount,
        string $sortBy = '',
        string $sortOrder = 'desc'
    ): array;

    /**
     * Deletes the client.
     */
    public function delete(): self;
}
