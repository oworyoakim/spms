<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
    protected $dates = ['start_date', 'due_date', 'end_date', 'deleted_at'];

    public function workPlan()
    {
        return $this->belongsTo(WorkPlan::class, 'work_plan_id');
    }

    public function directiveAndResolution()
    {
        return $this->belongsTo(DirectiveResolution::class, 'directive_resolution_id');
    }

    public function outputs()
    {
        return $this->hasMany(DirectiveResolutionOutput::class, 'directive_resolution_activity_id');
    }

    public function scopeSubmitted(Builder $builder)
    {
        return $builder->where('status', 'submitted');
    }

    public function scopeDeclined(Builder $builder)
    {
        return $builder->where('status', 'declined');
    }

    public function scopeApproved(Builder $builder)
    {
        return $builder->where('status', 'approved');
    }

    public function scopeOngoing(Builder $builder)
    {
        return $builder->where('status', 'ongoing');
    }

    public function scopeOnhold(Builder $builder)
    {
        return $builder->where('status', 'onhold');
    }

    public function scopeCompleted(Builder $builder)
    {
        return $builder->where('status', 'completed');
    }

    public function getDetails($expanded = false)
    {
        $activity = new stdClass();
        $activity->id = $this->id;
        $activity->workPlanId = $this->work_plan_id;
        $activity->directiveAndResolutionId = $this->directive_resolution_id;
        $activity->title = $this->title;
        $activity->description = $this->description;
        $activity->status = $this->status;
        $activity->startDate = !empty($this->start_date) ? $this->start_date->toDateString() : '';
        $activity->dueDate = $this->due_date->toDateString();
        $activity->endDate = ($this->end_date) ? $this->end_date->toDateString() : null;
        $activity->outputs = [];
        $activity->workPlan = null;
        $activity->directiveAndResolution = null;
        if ($expanded)
        {
            $activity->outputs = $this->outputs()->get()->map(function (DirectiveResolutionOutput $output) use ($expanded) {
                return $output->getDetails(!$expanded);
            });
            if ($this->workPlan)
            {
                $activity->workPlan = $this->workPlan->getDetails();
            }
            if ($this->directiveAndResolution)
            {
                $activity->directiveAndResolution = $this->directiveAndResolution->getDetails();
            }
        }

        $activity->createdBy = $this->created_by;
        $activity->updatedBy = $this->updated_by;
        $activity->createdAt = $this->created_at->toDateTimeString();
        $activity->updatedAt = $this->updated_at->toDateTimeString();
        $activity->canBeApproved = $this->status == 'submitted';
        $activity->canBeDeclined = $this->status == 'submitted';
        $activity->canBeStarted = $this->status == 'approved';
        $activity->canBeHeld = $this->status == 'ongoing';
        $activity->canBeResumed = $this->status == 'onhold';
        $activity->canBeCompleted = $this->status == 'ongoing';
        $activity->canBeEdited = $this->status == 'submitted';
        return $activity;
    }
}
