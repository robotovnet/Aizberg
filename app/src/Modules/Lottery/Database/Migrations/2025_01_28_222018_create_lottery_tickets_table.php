<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateLotteryAppsTable
 */
class CreateLotteryTicketsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'lottery_tickets';

     /** @var array Ticket statuses */
     private const STATUSES = [
        'unregistered',
        'registered',
        'winning',
        'blocked',
        'pending',
    ];

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('code', 13)->unique();
            $table->unsignedInteger('generation_id');
            $table->enum('status', self::STATUSES)->default('unregistered');
            
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
