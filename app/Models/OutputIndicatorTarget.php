<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class OutputIndicatorTarget
 * @package App\Models
 * @property int id
 * @property int output_indicator_id
 * @property int report_period_id
 * @property int objective_id
 * @property Carbon due_date
 * @property float target
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class OutputIndicatorTarget extends Model
{
    protected $table = 'output_indicator_targets';
    protected $dates = ['due_date', 'deleted_at'];

    public function indicator()
    {
        return $this->belongsTo(OutputIndicator::class, 'output_indicator_id');
    }

    public function reportPeriod()
    {
        return $this->belongsTo(ReportPeriod::class, 'report_period_id');
    }

    public function getDetails()
    {
        $indicatorTarget = new stdClass();
        $indicatorTarget->id = $this->id;
        $indicatorTarget->objectiveId = $this->objective_id;
        $indicatorTarget->outputIndicatorId = $this->output_indicator_id;
        $indicatorTarget->outputIndicator = $this->indicator ? $this->indicator->getDetails() : null;
        $indicatorTarget->reportPeriodId = $this->report_period_id;
        $indicatorTarget->reportPeriod = $this->reportPeriod ? $this->reportPeriod->getDetails() : null;
        $indicatorTarget->target = $this->target;
        $indicatorTarget->dueDate = $this->due_date->toDateString();
        $indicatorTarget->createdBy = $this->created_by;
        $indicatorTarget->updatedBy = $this->updated_by;
        $indicatorTarget->createdAt = $this->created_at->toDateTimeString();
        $indicatorTarget->updatedAt = $this->updated_at->toDateTimeString();
        return $indicatorTarget;
    }
}
