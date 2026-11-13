<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketplaceModulePurchasesTable extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_module_purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('marketplace_module_id');
            $table->unsignedInteger('client_id');
            $table->timestamp('purchased_at');
            $table->timestamps();

            $table->foreign('marketplace_module_id', 'mm_purchases_module_fk')
                ->references('id')->on('marketplace_modules');
            $table->foreign('client_id', 'mm_purchases_client_fk')
                ->references('id')->on('clients');
            $table->unique(['marketplace_module_id', 'client_id'], 'mm_purchases_module_client_unique');
            $table->index(['client_id', 'purchased_at'], 'mm_purchases_client_purchased_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_module_purchases');
    }
}
