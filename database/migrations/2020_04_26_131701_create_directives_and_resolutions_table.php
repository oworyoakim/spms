<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectivesAndResolutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directives_and_resolutions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_plan_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type',['directive','resolution']);
            $table->enum('source_type',['internal','external']);
            $table->string('source_organization')->nullable();
            $table->string('source_office')->nullable();
            $table->string('source_officer')->nullable();
            $table->string('source_telephone')->nullable();
            $table->string('source_email')->nullable();
            $table->date('date_received');
            $table->date('deadline');
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('responsibility_centre');
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
        Schema::dropIfExists('directives_and_resolutions');
    }
}
