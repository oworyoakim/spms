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
 * @property int rank
 * @property Carbon due_date
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

    public function interventions()
    {
        return $this->hasMany(Intervention::class, 'objective_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'objective_id');
    }

    public function getDetails()
    {
        $objective = new stdClass();
        $objective->id = $this->id;
        $objective->planId = $this->plan_id;
        $objective->name = $this->name;
        $objective->description = $this->description;
        $objective->rank = $this->rank;
        $objective->dueDate = $this->due_date->toDateTimeString();
        $objective->createdBy = $this->created_by;
        $objective->updatedBy = $this->updated_by;
        $objective->createdAt = $this->created_at->toDateTimeString();
        $objective->updatedAt = $this->updated_at->toDateTimeString();
        return $objective;
    }
}
