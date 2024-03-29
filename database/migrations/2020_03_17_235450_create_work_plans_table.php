<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id')->index();
            $table->string('title');
            $table->string('financial_year');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('frequency', ['monthly','quarterly','4-months'])->default('quarterly');
            $table->date('planning_deadline')->nullable();
            $table->string('theme')->nullable();
            $table->text('description')->nullable();
            $table->enum('status',['submitted','declined','approved','ongoing','onhold','completed'])->default('submitted')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['plan_id', 'financial_year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_plans');
    }
}
