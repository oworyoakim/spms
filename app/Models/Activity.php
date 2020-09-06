<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Activity
 * @package App\Models
 * @property int id
 * @property int work_plan_id
 * @property int intervention_id
 * @property int department_id
 * @property int team_leader_id
 * @property string quarter
 * @property float completion
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
class Activity extends Model
{
    protected $table = 'activities';
    protected $dates = ['start_date', 'due_date','end_date', 'deleted_at'];

    public function workPlan()
    {
        return $this->belongsTo(WorkPlan::class, 'work_plan_id');
    }

    public function intervention()
    {
        return $this->belongsTo(Intervention::class, 'intervention_id');
    }

    public function stages()
    {
        return $this->hasMany(Stage::class, 'activity_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'activity_id');
    }

    public function outputs()
    {
        return $this->hasMany(Output::class, 'activity_id');
    }

    public function getDetails()
    {
        $activity = new stdClass();
        $activity->id = $this->id;
        $activity->interventionId = $this->intervention_id;
        $activity->departmentId = $this->department_id;
        $activity->teamLeaderId = $this->team_leader_id;
        $activity->workPlanId = $this->work_plan_id;
        $activity->title = $this->title;
        $activity->description = $this->description;
        $activity->quarter = $this->quarter;
        $activity->status = $this->status;
        $activity->startDate = ($this->start_date) ? $this->start_date->toDateString() : null;
        $activity->dueDate = $this->due_date->toDateString();
        $activity->endDate = ($this->end_date) ? $this->end_date->toDateString() : null;
        $activity->completion = $this->completion;
        $activity->workPlan = ($this->workPlan) ? $this->workPlan->getDetails() : null;
        $activity->intervention = ($this->intervention) ? $this->intervention->getDetails() : null;

//        $activity->stages = $this->stages()->get()->map(function (Stage $stage) {
//            return $stage->getDetails();
//        });
        $activity->createdBy = $this->created_by;
        $activity->updatedBy = $this->updated_by;
        $activity->createdAt = $this->created_at->toDateTimeString();
        $activity->updatedAt = $this->updated_at->toDateTimeString();
        return $activity;
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

    public function start($userId){
        if($this->status == 'approved'){
            $this->start_date = Carbon::now();
            $this->status = 'ongoing';
            $this->updated_by = $userId;
            $this->save();
        }
    }

    public function hold($userId){
        if($this->status == 'ongoing'){
            $this->status = 'onhold';
            $this->updated_by = $userId;
            $this->save();

            // Set all ongoing stages as onhold
            $this->stages()->where('status','ongoing')->update([
                'status' => 'onhold',
                'updated_by' => $userId,
            ]);

            // Set all ongoing tasks as onhold
            $this->tasks()->where('status','ongoing')->update([
                'status' => 'onhold',
                'updated_by' => $userId,
            ]);
        }
    }

    public function unhold($userId){
        if($this->status == 'onhold'){
            $this->status = 'ongoing';
            $this->updated_by = $userId;
            $this->save();

            // Set all onhold stages as ongoing
            $this->stages()->where('status','onhold')->update([
                'status' => 'ongoing',
                'updated_by' => $userId,
            ]);

            // Set all onhold tasks as ongoing
            $this->tasks()->where('status','onhold')->update([
                'status' => 'ongoing',
                'updated_by' => $userId,
            ]);
        }
    }

}
