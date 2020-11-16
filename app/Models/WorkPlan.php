<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class WorkPlan
 * @package App\Models
 * @property int id
 * @property string title
 * @property string description
 * @property int plan_id
 * @property string theme
 * @property string financial_year
 * @property string status
 * @property Carbon start_date
 * @property Carbon end_date
 * @property Carbon planning_deadline
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class WorkPlan extends Model
{

    protected $table = 'work_plans';
    protected $dates = ['start_date', 'end_date', 'planning_deadline', 'deleted_at'];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function activityBlocks()
    {
        return $this->hasMany(ActivityBlock::class, 'work_plan_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'work_plan_id');
    }

    public function getDetails()
    {
        $workPlan = new stdClass();
        $workPlan->id = $this->id;
        $workPlan->title = $this->title;
        $workPlan->description = $this->description;
        $workPlan->financialYear = $this->financial_year;
        $workPlan->status = $this->status;
        $workPlan->theme = $this->theme;
        $workPlan->planId = $this->plan_id;
        $workPlan->plan = ($this->plan) ? $this->plan->getDetails() : null;
        $workPlan->startDate = $this->start_date->toDateString();
        $workPlan->endDate = $this->end_date->toDateString();
        $workPlan->planningDeadline = $this->planning_deadline->toDateString();
        $workPlan->createdBy = $this->created_by;
        $workPlan->updatedBy = $this->updated_by;
        $workPlan->createdAt = $this->created_at->toDateTimeString();
        $workPlan->updatedAt = $this->updated_at->toDateTimeString();
        return $workPlan;
    }
}
