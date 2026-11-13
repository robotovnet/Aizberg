<?php

namespace Modules\YandexWordstat\Repositories;

interface YandexWordstatRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Saves data from Yandex Wordstat.
     */
    public function save(array $data): array;
}
