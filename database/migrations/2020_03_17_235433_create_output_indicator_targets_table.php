<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutputIndicatorTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('output_indicator_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('objective_id')->nullable();
            $table->unsignedBigInteger('output_indicator_id');
            $table->unsignedBigInteger('report_period_id');
            $table->unsignedFloat('target')->nullable();
            $table->date('due_date');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['objective_id','output_indicator_id','report_period_id'],'unique_output_targets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('output_indicator_targets');
    }
}
