<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class KeyResultArea
 * @package App\Models
 * @property int id
 * @property int plan_id
 * @property string name
 * @property string description
 * @property int rank
 * @property Carbon due_date
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class KeyResultArea extends Model
{
    protected $dates = ['due_date', 'deleted_at'];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function outcomes()
    {
        return $this->belongsTo(Outcome::class, 'key_result_area_id');
    }

    public function getDetails()
    {
        $resultArea = new stdClass();
        $resultArea->id = $this->id;
        $resultArea->planId = $this->plan_id;
        $resultArea->name = $this->name;
        $resultArea->description = $this->description;
        $resultArea->rank = $this->rank;
        $resultArea->dueDate = $this->due_date->toDateTimeString();
        $resultArea->createdBy = $this->created_by;
        $resultArea->updatedBy = $this->updated_by;
        $resultArea->createdAt = $this->created_at->toDateTimeString();
        $resultArea->updatedAt = $this->updated_at->toDateTimeString();
        return $resultArea;
    }
}
