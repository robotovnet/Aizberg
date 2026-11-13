<?php

declare(strict_types=1);

namespace Modules\Lottery\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

/**
 * Сидер для модуля Lottery
 * 
 * Отвечает за последовательный запуск всех сидеров модуля
 */
final class LotteryDatabaseSeeder extends Seeder
{
    /**
     * Список сидеров для последовательного запуска
     */
    private const SEEDERS = [
        StaffPermissionsSeeder::class,
        DashboardMenus::class,
    ];

    /**
     * Запуск сидеров модуля
     * 
     * @return void
     */
    public function run(): void
    {
        Model::unguard();

        foreach (self::SEEDERS as $seeder) {
            $this->call($seeder);
        }

        Model::reguard();
    }
}
