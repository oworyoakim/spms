<?php

use App\Models\OutcomeIndicator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutcomeIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outcome_indicators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('key_result_area_id');
            $table->unsignedBigInteger('outcome_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('unit', [OutcomeIndicator::UNIT_COUNT, OutcomeIndicator::UNIT_PERCENT]);
            $table->unsignedFloat('baseline')->nullable();
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
        Schema::dropIfExists('outcome_indicators');
    }
}
