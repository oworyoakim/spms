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
 * @property float completion
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
        $stage->startDate = ($this->start_date) ? $this->start_date->toDateString() : null;
        $stage->dueDate = $this->due_date->toDateString();
        $stage->endDate = ($this->end_date) ? $this->end_date->toDateString() : null;
        $stage->createdBy = $this->created_by;
        $stage->updatedBy = $this->updated_by;
        $stage->createdAt = $this->created_at->toDateTimeString();
        $stage->updatedAt = $this->updated_at->toDateTimeString();

        return $stage;
    }

    public function start($userId){
        if($this->status == 'pending'){
            $this->start_date = Carbon::now();
            $this->status = 'ongoing';
            $this->updated_by = $userId;
            $this->save();
        }
    }

    public function updateStatus($userId)
    {
        $tasks = $this->tasks()->get();
        $completedTasks = $tasks->where('status', 'completed')->count();
        $incompleteTasks = $tasks->count() - $completedTasks;
        if ($incompleteTasks == 0)
        {
            $this->status = 'completed';
            $this->completion = 100;
            $this->end_date = Carbon::now();
        } else
        {
            $percent = round(($completedTasks / $tasks->count()) * 100, 2);
            $this->completion = $percent;
        }
        $this->updated_by = $userId;
        $this->save();
    }

}
