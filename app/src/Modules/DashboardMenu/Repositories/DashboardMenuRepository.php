<?php

declare(strict_types=1);

namespace Modules\DashboardMenu\Repositories;

/**
 * Interface DashboardMenuRepository
 * @package Modules\DashboardMenu\Repositories
 */
interface DashboardMenuRepository
{
    /**
     * Returns all sections and items menu.
     * Supports three levels of nesting.
     * The menu items that the user has access to are returned.
     */
    public function all(): array;
}
