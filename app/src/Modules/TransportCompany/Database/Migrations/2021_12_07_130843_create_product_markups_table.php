<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProductMarkupsTable
 */
class CreateProductMarkupsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'product_markups';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('payment_method_id');
            $table->unsignedInteger('transport_company_id')->nullable();
            $table->char('is_support_courier_delivery', 1);
            $table->char('is_support_postamats', 1);
            $table->char('is_support_pickup_points', 1);
            $table->unsignedFloat('markup');
            $table->enum('markup_type', ['percentage', 'fixed_value']);
            $table->char('is_active', 1);
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
