<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModuleBlockingToMarketplaceModules extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_modules', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false)->after('is_archived');
            $table->timestamp('blocked_at')->nullable()->after('is_blocked');
            $table->unsignedInteger('blocked_by_client_id')->nullable()->after('blocked_at');

            $table->foreign('blocked_by_client_id', 'mm_blocked_by_client_fk')
                ->references('id')->on('clients')->onDelete('set null');
            $table->index(['is_blocked', 'is_archived', 'deleted_at'], 'mm_public_visibility_idx');
        });

        Schema::create('marketplace_module_moderator_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('client_id')->unique();
            $table->timestamps();

            $table->foreign('client_id', 'mm_moderator_client_fk')
                ->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_module_moderator_permissions');

        Schema::table('marketplace_modules', function (Blueprint $table) {
            $table->dropForeign('mm_blocked_by_client_fk');
            $table->dropIndex('mm_public_visibility_idx');
            $table->dropColumn(['is_blocked', 'blocked_at', 'blocked_by_client_id']);
        });
    }
}
