<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutcomeIndicatorMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outcome_indicator_milestones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outcome_indicator_id');
            $table->string('financial_year');
            $table->timestamp('due_date');
            $table->unsignedFloat('baseline')->nullable();
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
