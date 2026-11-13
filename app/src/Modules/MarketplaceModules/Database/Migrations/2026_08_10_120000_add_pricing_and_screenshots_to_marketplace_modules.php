<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricingAndScreenshotsToMarketplaceModules extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_modules', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->after('pricing_type');
            $table->char('currency', 3)->nullable()->after('price');
        });

        Schema::create('marketplace_module_screenshots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('marketplace_module_id');
            $table->string('path', 512);
            $table->unsignedTinyInteger('position');
            $table->timestamps();

            $table->foreign('marketplace_module_id', 'mm_screenshots_module_fk')
                ->references('id')->on('marketplace_modules')->onDelete('cascade');
            $table->unique(['marketplace_module_id', 'position'], 'mm_screenshots_module_position_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_module_screenshots');

        Schema::table('marketplace_modules', function (Blueprint $table) {
            $table->dropColumn(['price', 'currency']);
        });
    }
}
