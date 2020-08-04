<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectiveResolutionActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directive_resolution_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_plan_id');
            $table->unsignedBigInteger('directive_resolution_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date');
            $table->date('end_date')->nullable();
            $table->enum('status',['submitted','declined','approved','ongoing','onhold','completed'])->default('submitted')->index();
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
        Schema::dropIfExists('directive_resolution_activities');
    }
}
