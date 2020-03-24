<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Task
 * @package App\Models
 * @property int id
 * @property int activity_id
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
class Task extends Model
{
    protected $table = 'tasks';
    protected $dates = ['start_date', 'due_date', 'deleted_at'];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function getDetails()
    {
        $task = new stdClass();
        $task->id = $this->id;
        $task->activityId = $this->activity_id;
        $task->title = $this->title;
        $task->description = $this->description;
        $task->status = $this->status;
        $task->startDate = $this->start_date->toDateTimeString();
        $task->dueDate = $this->due_date->toDateTimeString();
        $task->createdBy = $this->created_by;
        $task->updatedBy = $this->updated_by;
        $task->createdAt = $this->created_at->toDateTimeString();
        $task->updatedAt = $this->updated_at->toDateTimeString();

        return $task;
    }

}
