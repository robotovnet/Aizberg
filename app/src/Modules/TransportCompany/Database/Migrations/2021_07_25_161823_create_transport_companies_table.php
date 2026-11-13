<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateTransportCompaniesTable
 */
class CreateTransportCompaniesTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'transport_companies';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->text('additional_comment')->nullable();
            $table->string('code', 30)->unique();
            $table->string('accept_orders_start_time', 5)->nullable();
            $table->string('accept_orders_completion_time', 5)->nullable();
            $table->unsignedInteger('minimum_order_value');
            $table->string('icon_path', 60)->nullable();
            $table->char('is_support_courier_delivery', 1);
            $table->char('is_support_postamats', 1);
            $table->char('is_support_pickup_points', 1);
            $table->char('is_active', 1);
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
