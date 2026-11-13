<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreatePaymentMethodTransportCompanyTable
 */
class CreatePaymentMethodTransportCompanyTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'payment_method_transport_company';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('payment_method_id');
            $table->unsignedInteger('transport_company_id');
            $table->char('is_support_courier_delivery', 1)->default('N');
            $table->char('is_support_postamats', 1)->default('N');
            $table->char('is_support_pickup_points', 1)->default('N');
            $table->unique(['payment_method_id', 'transport_company_id'], 'payment_transport_unique');
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
