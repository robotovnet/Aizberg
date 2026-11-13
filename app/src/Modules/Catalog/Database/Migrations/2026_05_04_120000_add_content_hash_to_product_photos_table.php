<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContentHashToProductPhotosTable extends Migration
{
    private string $table = 'product_photos';

    public function up(): void
    {
        // Preserve hashes if the column was added before this migration
        // was recorded in the migrations table.
        if (Schema::hasColumn($this->table, 'content_hash')) {
            return;
        }

        Schema::table($this->table, function (Blueprint $table) {
            $table->string('content_hash', 64)->nullable()->after('uuid');
        });
    }

    public function down(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn('content_hash');
        });
    }
}
