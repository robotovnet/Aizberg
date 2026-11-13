<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProductQaTable
 */
class CreateProductQaTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'product_qa';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('staff_id')->nullable();
            $table->unsignedInteger('reply_to_qid')->nullable();
            $table->char('replied', 1)->default('N');
            $table->text('content');
            $table->char('reply_to_mail', 1)->default('N');
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
