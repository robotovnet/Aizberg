<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateLotteryAppsTable
 */
class CreateLotteryAppsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'lottery_apps';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            
            $table->unsignedInteger('ticket_id');
            $table->string('player_name', 60);
            $table->string('player_phone', 20);
            $table->string('player_email', 60);
            $table->string('screenshot_url', 100);
            $table->enum('status', ['accepted','rejected','winning','pending','pending2','blocked']);
            
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
