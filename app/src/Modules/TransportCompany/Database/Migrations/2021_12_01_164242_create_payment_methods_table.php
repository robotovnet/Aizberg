<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreatePaymentMethodsTable
 */
class CreatePaymentMethodsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'payment_methods';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->text('additional_comment')->nullable();
            $table->string('code', 30)->unique();
            $table->unsignedInteger('minimum_order_value');
            $table->unsignedInteger('maximum_order_value');
            $table->unsignedInteger('order');
            $table->char('is_active', 1)->default('N');
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
