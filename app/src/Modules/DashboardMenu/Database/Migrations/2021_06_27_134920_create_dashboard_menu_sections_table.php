<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateDashboardMenuSectionsTable
 */
class CreateDashboardMenuSectionsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'dashboard_menus';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->string('code', 60);
            $table->string('parent', 60)->nullable();
            $table->tinyInteger('level');
            $table->string('policy', 60)->index()->nullable();
            $table->string('href', 100)->nullable();
            $table->string('image_src', 100)->nullable();
            $table->text('image_inline')->nullable();
            $table->char('symbol', 1)->nullable();
            $table->unsignedInteger('order');
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
