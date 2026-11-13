<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketplaceModuleSearchQueriesTable extends Migration
{
    public function up(): void
    {
        // Keep search statistics when the table predates its migration record.
        if (Schema::hasTable('marketplace_module_search_queries')) {
            return;
        }

        Schema::create('marketplace_module_search_queries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('query_hash', 64)->unique('mm_search_queries_hash_unique');
            $table->text('query');
            $table->unsignedBigInteger('searches_count')->default(0);
            $table->timestamps();

            $table->index(['searches_count', 'id'], 'mm_search_queries_popularity_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_module_search_queries');
    }
}
