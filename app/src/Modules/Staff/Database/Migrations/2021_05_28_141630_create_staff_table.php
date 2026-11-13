<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateStaffTable
 */
class CreateStaffTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'staff';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 60);
            $table->string('last_name', 60);
            $table->string('phone', 20)->unique();
            $table->string('email', 100)->unique();
            $table->string('userpic_name', 37)->nullable();
            $table->char('is_blocked', 1)->default('N');
            $table->string('password', 60);
            $table->string('whatsapp', 50)->nullable();
            $table->string('telegram', 50)->nullable();
            $table->string('max', 100)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
