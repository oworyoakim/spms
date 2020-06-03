<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Task
 * @package App\Models
 * @property int id
 * @property int work_plan_id
 * @property int activity_id
 * @property int stage_id
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
class Task extends Model
{
    protected $table = 'tasks';
    protected $dates = ['start_date', 'due_date', 'deleted_at'];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id');
    }

    public function getDetails()
    {
        $task = new stdClass();
        $task->id = $this->id;
        $task->workPlanId = $this->work_plan_id;
        $task->activityId = $this->activity_id;
        $task->stageId = $this->stage_id;
        $task->title = $this->title;
        $task->description = $this->description;
        $task->status = $this->status;
        $task->startDate = ($this->start_date) ? $this->start_date->toDateString() : null;
        $task->dueDate = $this->due_date->toDateString();
        $task->endDate = ($this->end_date) ? $this->end_date->toDateString() : null;
        $task->stage = ($this->stage) ? $this->stage->getDetails() : null;
        $task->createdBy = $this->created_by;
        $task->updatedBy = $this->updated_by;
        $task->createdAt = $this->created_at->toDateTimeString();
        $task->updatedAt = $this->updated_at->toDateTimeString();

        return $task;
    }

    public function start($userId){
        $this->update([
            'start_date' => Carbon::now(),
            'status' => 'ongoing',
            'updated_by' => $userId,
        ]);
    }

    public function complete($userId){
        $this->update([
            'end_date' => Carbon::now(),
            'status' => 'completed',
            'updated_by' => $userId,
        ]);
    }
}
