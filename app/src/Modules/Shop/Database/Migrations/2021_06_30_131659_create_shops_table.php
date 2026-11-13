<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateShopsTable
 */
class CreateShopsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'shops';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('subdomain', 30)->nullable();
            $table->unsignedInteger('city_id');
            $table->string('city_name_dative_case', 60);
            $table->string('phone', 20);
            $table->char('is_active', 1)->default('N');
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
