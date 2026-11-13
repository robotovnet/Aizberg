<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateStaffGroupStaffPermissionTable
 */
class CreateStaffGroupStaffPermissionTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'staff_group_staff_permission';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('staff_group_id')->index();
            $table->unsignedInteger('staff_permission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
}
