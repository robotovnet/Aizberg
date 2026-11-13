<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketplaceModuleFavoritesTable extends Migration
{
    public function up(): void
    {
        // The table may already exist on installations where favorites were
        // deployed before this migration was registered in the migrations log.
        // Preserve existing favorite records and let Laravel mark the migration
        // as completed instead of attempting to recreate the table.
        if (Schema::hasTable('marketplace_module_favorites')) {
            return;
        }

        Schema::create('marketplace_module_favorites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('marketplace_module_id');
            $table->unsignedInteger('client_id');
            $table->timestamps();

            $table->foreign('marketplace_module_id', 'mm_favorites_module_fk')
                ->references('id')->on('marketplace_modules')->onDelete('cascade');
            $table->foreign('client_id', 'mm_favorites_client_fk')
                ->references('id')->on('clients')->onDelete('cascade');
            $table->unique(['marketplace_module_id', 'client_id'], 'mm_favorites_module_client_unique');
            $table->index(['client_id', 'created_at'], 'mm_favorites_client_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_module_favorites');
    }
}
