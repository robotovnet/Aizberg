<?php

declare(strict_types=1);

namespace Modules\Qa\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface QaRepository
 * @package Modules\Qa\Repositories
 */
interface QaRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates qa data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new qa.
     */
    public function create(array $data): int;

    /**
     * Deletes the qa.
     */
    public function delete(): self;

    /**
     * Returns qa data.
     */
    public function data(): array;

    /**
     * Answer the question.
     *
     * @param      int     $id          The identifier
     * @param      string  $answerText  The answer text
     */
    public function answer(Model $questionModel, string $answerText): bool;

    /**
     * Returns data of all qa.
     */
    public function all(?string $isReplied, ?string $onlyCount, int $page = 1): array;
}
