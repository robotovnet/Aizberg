<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories;

/**
 * Interface FeatureRepository
 * @package Modules\Catalog\Repositories
 */
interface FeatureRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates feature data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new feature.
     */
    public function create(array $data): int;

    /**
     * Deletes the feature.
     */
    public function delete(): self;

    /**
     * Returns feature data.
     */
    public function data(): array;

    /**
     * Returns data of all features.
     */
    public function all(?string $search): array;

    /**
     * Returns all unique values of the feature.
     */
    public function getValues(): array;

    /**
     * Migrates feature values from one feature to another.
     * 
     * @param int $sourceFeatureId ID исходного параметра
     * @param int $targetFeatureId ID нового параметра
     * @param string|null $currentValue Текущее значение параметра (если null, то мигрируются все значения)
     * @param string|null $newValue Новое значение параметра (если null, то используется старое значение)
     * @return array Статистика миграции
     */
    public function migrateValues(int $sourceFeatureId, int $targetFeatureId, ?string $currentValue = null, ?string $newValue = null): array;

    /**
     * Получает товары по параметру и значению.
     * 
     * @param int $featureId ID параметра
     * @param string $value Значение параметра
     * @return array Массив товаров с данными name, vendor_code и url
     */
    public function getProductsByFeatureValue(int $featureId, string $value): array;
}
