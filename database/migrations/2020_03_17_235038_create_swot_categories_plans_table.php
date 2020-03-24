<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSwotCategoriesPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('swot_categories_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['plan_id','category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('swot_categories_plans');
    }
}
