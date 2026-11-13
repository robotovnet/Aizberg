<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateClientsTable
 */
class CreateClientsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'clients';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 60)->nullable()->index();
            $table->string('last_name', 60)->nullable()->index();
            $table->string('phone', 20)->index();
            $table->string('email', 100)->index();
            $table->unsignedBigInteger('telegram')->nullable();
            $table->unsignedInteger('telegram_date')->nullable();
            $table->char('permanent_type', 1)->default('N');
            $table->char('is_blocked', 1)->default('N');
            $table->string('password', 60);
            $table->rememberToken()->unique();
            $table->timestamps();
            $table->softDeletes();
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
