<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProductAdYandexMarketTable
 */
class CreateProductAdYandexMarketTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'product_ad_yandex_market';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->text('overview_text')->nullable();
            $table->string('picture_url', 255);
            $table->char('is_active', 1);
            $table->unsignedInteger('product_id')->index();
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
