<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Objective
 * @package App\Models
 * @property int id
 * @property string name
 * @property string description
 * @property int plan_id
 * @property int key_result_area_id
 * @property int rank
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Objective extends Model
{

    protected $dates = ['due_date', 'deleted_at'];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
    public function keyResultArea()
    {
        return $this->belongsTo(KeyResultArea::class, 'key_result_area_id');
    }
    public function interventions()
    {
        return $this->hasMany(Intervention::class, 'objective_id');
    }

    public function activityBlocks()
    {
        return $this->hasMany(ActivityBlock::class, 'objective_id');
    }

    public function outputs()
    {
        return $this->hasMany(Output::class, 'objective_id');
    }

    public function indicators()
    {
        return $this->hasMany(OutputIndicator::class, 'objective_id');
    }

    public function targets()
    {
        return $this->hasMany(OutputIndicatorTarget::class, 'objective_id');
    }

    public function achievements()
    {
        return $this->hasMany(OutputAchievement::class, 'objective_id');
    }

    public function getDetails()
    {
        $objective = new stdClass();
        $objective->id = $this->id;
        $objective->name = $this->name;
        $objective->description = $this->description;
        $objective->rank = $this->rank;
        $objective->planId = $this->plan_id;
        $objective->keyResultAreaId = $this->key_result_area_id;
        $objective->plan = $this->plan ? $this->plan->getDetails() : null;
        $objective->reportPeriods = $objective->plan ? $objective->plan->reportPeriods : [];
        $objective->createdBy = $this->created_by;
        $objective->updatedBy = $this->updated_by;
        $objective->createdAt = $this->created_at->toDateTimeString();
        $objective->updatedAt = $this->updated_at->toDateTimeString();
        return $objective;
    }
}
