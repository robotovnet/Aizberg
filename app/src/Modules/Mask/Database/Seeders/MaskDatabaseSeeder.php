<?php

declare(strict_types=1);

namespace Modules\Mask\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class MaskDatabaseSeeder
 * @package Modules\Mask\Database\Seeders
 */
class MaskDatabaseSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'masks';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $masks = [
            [
                'mask' => '+{7} (000) 000-00-00',
                'code' => 'ru',
                'type' => 'phone'
            ],
            [
                'mask' => '+{380} (00) 000-00-00',
                'code' => 'ua',
                'type' => 'phone'
            ],
            [
                'mask' => '+{375} (000) 000-00-00',
                'code' => 'by',
                'type' => 'phone'
            ],
        ];

        DB::table($this->table)->insert($masks);
    }
}
