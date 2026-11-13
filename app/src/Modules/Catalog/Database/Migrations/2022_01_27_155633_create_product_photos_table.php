<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProductPhotosTable
 */
class CreateProductPhotosTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'product_photos';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->string('file_path', 100);
            $table->string('file_name', 60);
            $table->string('file_extension', 4);
            $table->unsignedInteger('file_width');
            $table->unsignedInteger('file_height');
            $table->char('uuid', 36)->index();
            $table->char('is_main', 1)->index();
            $table->unsignedInteger('order');
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
