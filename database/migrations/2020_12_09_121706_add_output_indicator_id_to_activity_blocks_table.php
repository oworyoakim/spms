<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOutputIndicatorIdToActivityBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_blocks', function (Blueprint $table) {
            $table->unsignedBigInteger('output_indicator_id')
                  ->index()
                  ->nullable()
                  ->after('outcome_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_blocks', function (Blueprint $table) {
            $table->dropColumn(['output_indicator_id']);
        });
    }
}
