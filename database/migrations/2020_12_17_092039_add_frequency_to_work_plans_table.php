<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFrequencyToWorkPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_plans', function (Blueprint $table) {
            $table->enum('frequency', ['monthly','quarterly','4-months'])->after('end_date')->default('quarterly');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_plans', function (Blueprint $table) {
            $table->dropColumn(['frequency']);
        });
    }
}
