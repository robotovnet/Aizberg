<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateOrdersTable
 */
class CreateOrdersTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'orders';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->index();
            $table->unsignedInteger('staff_id')->nullable()->index();
            $table->enum('order_status', ['new', 'confirm', 'delivered', 'ready', '1c_err', 'return', 'cancel',
                'duplicate', 'non_call', 'complete'])->index();
            $table->unsignedInteger('shop_id')->index();
            $table->unsignedInteger('city_id')->index();
            $table->unsignedInteger('payment_method_id')->index();
            $table->enum('payment_status', ['waiting', 'paid', 'error', 'canceled', 'cancelled_by_timeout'])->index();
            $table->unsignedInteger('transport_company_id')->index();
            $table->enum('delivery_point_type', ['postamat', 'courier_city', 'pickup']);
            $table->unsignedInteger('delivery_point_id');
            $table->string('track_number', 60)->nullable();
            $table->unsignedInteger('delivery_cost');
            $table->unsignedInteger('total_products_cost');
            $table->unsignedInteger('total_order_cost');
            $table->text('address_comment')->nullable();
            $table->text('order_comment')->nullable();
            $table->text('delivery_address');
            $table->char('is_mobile_order', 1);
            $table->char('viewed', 1)->index();
            $table->string('client_email', 60)->index();
            $table->string('client_phone', 20)->index();
            $table->string('client_phone_add', 20)->index();
            $table->string('client_name', 60)->index();
            $table->string('client_surname', 60)->index();
            $table->string('referrer', 255)->nullable();
            $table->timestamp('completed_at')->nullable();
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
