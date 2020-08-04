<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectiveResolutionOutputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directive_resolution_outputs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('directive_resolution_id');
            $table->unsignedBigInteger('directive_resolution_activity_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->float('target');
            $table->float('actual')->nullable();
            $table->enum('unit',['count','percent']);
            $table->date('output_date_updated')->nullable();
            $table->unsignedBigInteger('responsible_person');
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
        Schema::dropIfExists('directive_resolution_outputs');
    }
}
