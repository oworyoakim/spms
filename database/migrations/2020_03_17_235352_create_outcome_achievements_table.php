<?php

use App\Models\OutcomeIndicator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutcomeAchievementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outcome_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('key_result_area_id');
            $table->unsignedBigInteger('outcome_indicator_id');
            $table->unsignedBigInteger('report_period_id');
            $table->date('achievement_date');
            $table->unsignedFloat('actual');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['key_result_area_id','outcome_indicator_id','report_period_id'],'unique_outcome_achievements');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outcome_achievements');
    }
}
