<?php

declare(strict_types=1);

namespace Modules\Settings\Repositories;

/**
 * Interface SettingsRepository
 * @package Modules\Settings\Repositories
 */
interface SettingsRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Returns setting value by setting code.
     */
    public function getSettingByCode(string $code): string;

    /**
     * Returns all settings.
     */
    public function all(?string $code): array;

    /**
     * Updates a specific setting.
     */
    public function update(array $attributes): self;

    /**
     * Returns settings by code.
     */
    public function getByCode(string $settingCode): array;
}
