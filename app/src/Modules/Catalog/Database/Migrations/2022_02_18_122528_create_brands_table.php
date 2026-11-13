<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateBrandsTable
 */
class CreateBrandsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'brands';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->index();
            $table->char('country_code', 2)->nullable()->index();
            $table->string('official_website', 60)->nullable();
            $table->text('desc')->nullable();
            $table->char('is_hidden', 1)->default('N')->index();
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
