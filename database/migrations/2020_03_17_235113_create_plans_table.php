<?php

use App\Models\Plan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->timestamp("start_date");
            $table->timestamp("end_date");
            $table->enum("frequency",Plan::REPORT_FREQUENCIES);
            $table->string("theme")->nullable();
            $table->text("mission")->nullable();
            $table->text("vision")->nullable();
            $table->text("values")->nullable();
            $table->enum("state",Plan::STATES)->default(Plan::STATE_PLANNING);
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
        Schema::dropIfExists('plans');
    }
}
