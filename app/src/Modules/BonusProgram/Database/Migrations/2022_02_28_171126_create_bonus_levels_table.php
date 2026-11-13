<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateBonusLevelsTable
 */
class CreateBonusLevelsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'bonus_levels';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60)->unique();
            $table->unsignedInteger('client_type_id');
            $table->unsignedInteger('level_num');
            $table->unsignedInteger('amount_up');
            $table->unsignedInteger('amount_instantly_up');
            $table->unsignedInteger('amount_saving');
            $table->char('is_entry', 1);
            $table->unsignedInteger('calculation_period');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
