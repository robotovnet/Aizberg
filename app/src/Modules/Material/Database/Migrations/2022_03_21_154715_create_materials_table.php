<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateMaterialsTable
 */
class CreateMaterialsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'materials';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shop_id');
            $table->string('name', 255)->index();
            $table->string('url_slug', 160)->nullable()->index();
            $table->string('code', 60)->unique();
            $table->string('section_name', 60)->index();
            $table->enum('type', ['page', 'block']);
            $table->char('is_active', 1);
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->longText('content')->nullable();
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
