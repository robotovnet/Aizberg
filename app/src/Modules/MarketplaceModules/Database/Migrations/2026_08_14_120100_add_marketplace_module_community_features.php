<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMarketplaceModuleCommunityFeatures extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('marketplace_module_versions', 'changes')) {
            Schema::table('marketplace_module_versions', function (Blueprint $table) {
                $table->json('changes')->nullable()->after('release_date');
            });
        }

        if (!Schema::hasTable('marketplace_module_review_votes')) {
            Schema::create('marketplace_module_review_votes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('marketplace_module_review_id');
                $table->unsignedInteger('client_id');
                $table->boolean('is_helpful');
                $table->timestamps();

                $table->foreign('marketplace_module_review_id', 'mm_review_votes_review_fk')
                    ->references('id')->on('marketplace_module_reviews')->onDelete('cascade');
                $table->foreign('client_id', 'mm_review_votes_client_fk')
                    ->references('id')->on('clients')->onDelete('cascade');
                $table->unique(
                    ['marketplace_module_review_id', 'client_id'],
                    'mm_review_votes_review_client_unique'
                );
            });
        }

        $ratingValueType = Schema::getColumnType('marketplace_module_ratings', 'value');
        if (in_array($ratingValueType, ['string', 'text'], true)) {
            DB::table('marketplace_module_ratings')->where('value', 'Bad')->update(['value' => 1]);
            DB::table('marketplace_module_ratings')->where('value', 'Good')->update(['value' => 3]);
            DB::table('marketplace_module_ratings')->where('value', 'Top')->update(['value' => 5]);
        }

        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE marketplace_module_ratings MODIFY value TINYINT UNSIGNED NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE marketplace_module_ratings ALTER COLUMN value TYPE SMALLINT USING value::smallint'
            );
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE marketplace_module_ratings MODIFY value VARCHAR(16) NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE marketplace_module_ratings ALTER COLUMN value TYPE VARCHAR(16) USING value::varchar'
            );
        }

        DB::table('marketplace_module_ratings')->where('value', '1')->update(['value' => 'Bad']);
        DB::table('marketplace_module_ratings')->whereIn('value', ['2', '3', '4'])->update(['value' => 'Good']);
        DB::table('marketplace_module_ratings')->where('value', '5')->update(['value' => 'Top']);

        Schema::dropIfExists('marketplace_module_review_votes');

        if (Schema::hasColumn('marketplace_module_versions', 'changes')) {
            Schema::table('marketplace_module_versions', function (Blueprint $table) {
                $table->dropColumn('changes');
            });
        }
    }
}
