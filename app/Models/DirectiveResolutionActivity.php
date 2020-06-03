<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class DirectiveResolutionActivity
 * @package App\Models
 * @property int id
 * @property int work_plan_id
 * @property int directive_resolution_id
 * @property string title
 * @property string description
 * @property string status
 * @property Carbon start_date
 * @property Carbon due_date
 * @property Carbon end_date
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class DirectiveResolutionActivity extends Model
{
    protected $table = 'directive_resolution_activities';
    protected $dates = ['start_date', 'due_date','end_date', 'deleted_at'];

    public function workPlan()
    {
        return $this->belongsTo(WorkPlan::class, 'work_plan_id');
    }

    public function directiveResolution()
    {
        return $this->belongsTo(DirectiveResolution::class, 'directive_resolution_id');
    }

    public function outputs()
    {
        return $this->hasMany(DirectiveResolutionOutput::class, 'directive_resolution_activity_id');
    }

    public function getDetails()
    {
        $activity = new stdClass();
        $activity->id = $this->id;
        $activity->workPlanId = $this->work_plan_id;
        $activity->title = $this->title;
        $activity->description = $this->description;
        $activity->status = $this->status;
        $activity->startDate = $this->start_date->toDateString();
        $activity->dueDate = $this->due_date->toDateString();
        $activity->endDate = ($this->end_date) ? $this->end_date->toDateString() : null;
        $activity->workPlan = ($this->workPlan) ? $this->workPlan->getDetails() : null;
        $activity->directiveResolution = ($this->directiveResolution) ? $this->directiveResolution->getDetails() : null;

        $activity->outputs = $this->outputs()->get()->map(function (DirectiveResolutionOutput $output) {
            return $output->getDetails();
        });

        $activity->createdBy = $this->created_by;
        $activity->updatedBy = $this->updated_by;
        $activity->createdAt = $this->created_at->toDateTimeString();
        $activity->updatedAt = $this->updated_at->toDateTimeString();
        return $activity;
    }
}
