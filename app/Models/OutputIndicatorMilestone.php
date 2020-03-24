<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class OutputIndicatorMilestone
 * @package App\Models
 * @property int id
 * @property int output_indicator_id
 * @property string financial_year
 * @property Carbon due_date
 * @property float target
 * @property float actual
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class OutputIndicatorMilestone extends Model
{
    protected $table = 'output_indicator_milestones';
    protected $dates = ['due_date', 'deleted_at'];

    public function indicator()
    {
        return $this->belongsTo(OutputIndicator::class, 'output_indicator_id');
    }

    public function getDetails()
    {
        $milestone = new stdClass();
        $milestone->id = $this->id;
        $milestone->indicatorId = $this->output_indicator_id;
        $milestone->financialYear = $this->financial_year;
        $milestone->target = $this->target;
        $milestone->actual = $this->actual;
        $milestone->dueDate = $this->due_date->toDateTimeString();
        $milestone->createdBy = $this->created_by;
        $milestone->updatedBy = $this->updated_by;
        $milestone->createdAt = $this->created_at->toDateTimeString();
        $milestone->updatedAt = $this->updated_at->toDateTimeString();
        return $milestone;
    }
}
