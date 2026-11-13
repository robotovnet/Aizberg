<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateLotteryAppsTable
 */
class CreateLotteryDrawsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'lottery_draws';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('day', 5);
            $table->unsignedSmallInteger('week');
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('prize_id');
            $table->unsignedInteger('app_id');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at');
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
