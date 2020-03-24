<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutputIndicatorMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('output_indicator_milestones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('output_indicator_id');
            $table->string('financial_year');
            $table->timestamp('due_date');
            $table->unsignedFloat('target')->nullable();
            $table->unsignedFloat('actual')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outcome_indicator_milestones');
    }
}
