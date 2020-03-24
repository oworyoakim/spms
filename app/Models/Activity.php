<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Activity
 * @package App\Models
 * @property int id
 * @property int objective_id
 * @property float completion
 * @property string title
 * @property string description
 * @property string status
 * @property Carbon start_date
 * @property Carbon due_date
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Activity extends Model
{
    protected $table = 'activities';
    protected $dates = ['start_date', 'due_date', 'deleted_at'];

    public function objective()
    {
        return $this->belongsTo(Objective::class, 'objective_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'activity_id');
    }

    public function getDetails()
    {
        $activity = new stdClass();
        $activity->id = $this->id;
        $activity->objectiveId = $this->objective_id;
        $activity->title = $this->title;
        $activity->description = $this->description;
        $activity->status = $this->status;
        $activity->startDate = $this->start_date->toDateTimeString();
        $activity->dueDate = $this->due_date->toDateTimeString();
        $activity->completion = $this->completion;
        $activity->tasks = $this->tasks()->get()->map(function (Task $task) {
            return $task->getDetails();
        });
        $activity->objective = null;
        if ($this->objective)
        {
            $activity->objective = $this->objective->getDetails();
        }
        $activity->createdBy = $this->created_by;
        $activity->updatedBy = $this->updated_by;
        $activity->createdAt = $this->created_at->toDateTimeString();
        $activity->updatedAt = $this->updated_at->toDateTimeString();
        return $activity;
    }
}
