<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepartmentIdTeamLeaderIdAndQuarterToActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->after('intervention_id')->nullable();
            $table->unsignedBigInteger('team_leader_id')->after('department_id')->nullable();
            $table->string('quarter')->after('team_leader_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['department_id','team_leader_id','quarter']);
        });
    }
}
