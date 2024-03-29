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
            $table->date("start_date");
            $table->date("end_date");
            $table->enum("frequency", [
                Plan::$FREQUENCY_MONTHLY,
                Plan::$FREQUENCY_QUARTERLY,
                Plan::$FREQUENCY_TRIMONTHLY,
                Plan::$FREQUENCY_HALF_YEARLY,
                Plan::$FREQUENCY_YEARLY,
            ])->default(Plan::$FREQUENCY_QUARTERLY);
            $table->string("theme")->nullable();
            $table->text("mission")->nullable();
            $table->text("vision")->nullable();
            $table->text("values")->nullable();
            $table->enum("state", Plan::STATES)->default(Plan::STATE_PLANNING);
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
