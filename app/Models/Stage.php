<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Stage
 * @package App\Models
 * @property int id
 * @property int work_plan_id
 * @property int activity_id
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
class Stage extends Model
{
    protected $table = 'stages';
    protected $dates = ['start_date', 'due_date', 'deleted_at'];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'stage_id');
    }

    public function getDetails()
    {
        $stage = new stdClass();
        $stage->id = $this->id;
        $stage->workPlanId = $this->work_plan_id;
        $stage->activityId = $this->activity_id;
        $stage->title = $this->title;
        $stage->description = $this->description;
        $stage->status = $this->status;
        $stage->startDate = $this->start_date->toDateTimeString();
        $stage->dueDate = $this->due_date->toDateTimeString();
        $stage->endDate = ($this->end_date) ? $this->end_date->toDateTimeString() : null;
//        $stage->tasks = $this->tasks()->get()->map(function (Task $task) {
//            return $task->getDetails();
//        });
        $stage->activity = ($this->activity) ? $this->activity->getDetails() : null;
        $stage->createdBy = $this->created_by;
        $stage->updatedBy = $this->updated_by;
        $stage->createdAt = $this->created_at->toDateTimeString();
        $stage->updatedAt = $this->updated_at->toDateTimeString();

        return $stage;
    }

}
