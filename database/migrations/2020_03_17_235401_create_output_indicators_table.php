<?php

use App\Models\OutputIndicator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutputIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('output_indicators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('objective_id')->nullable();
            $table->unsignedBigInteger('output_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('unit', [OutputIndicator::UNIT_COUNT, OutputIndicator::UNIT_PERCENT]);
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
        Schema::dropIfExists('output_indicators');
    }
}
