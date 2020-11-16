<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityBlockDirectoratesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_block_directorates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_block_id');
            $table->unsignedBigInteger('directorate_id');
            $table->unique(['activity_block_id','directorate_id'],'activity_block_directorates_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_block_directorates');
    }
}
