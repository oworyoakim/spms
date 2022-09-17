<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->float('cost',13,2)->default(0);
            $table->float('expenditure',13,2)->nullable();
            $table->unsignedBigInteger('work_plan_id')->index();
            $table->unsignedBigInteger('activity_block_id')->nullable();
            $table->unsignedBigInteger('objective_id')->index();
            $table->unsignedBigInteger('intervention_id')->nullable();
            $table->unsignedBigInteger('directorate_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('team_leader_id')->nullable();
            $table->string('quarter')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date');
            $table->date('end_date')->nullable();
            $table->enum('status',['submitted','declined','approved','ongoing','onhold','completed'])->default('submitted')->index();
            $table->unsignedFloat('completion')->nullable();
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
        Schema::dropIfExists('activities');
    }
}
