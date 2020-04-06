<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class OutcomeIndicatorTarget
 * @package App\Models
 * @property int id
 * @property int key_result_area_id
 * @property int outcome_indicator_id
 * @property int report_period_id
 * @property Carbon due_date
 * @property float target
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class OutcomeIndicatorTarget extends Model
{
    protected $table = 'outcome_indicator_targets';
    protected $dates = ['due_date', 'deleted_at'];

    public function indicator()
    {
        return $this->belongsTo(OutcomeIndicator::class, 'outcome_indicator_id');
    }

    public function reportPeriod()
    {
        return $this->belongsTo(ReportPeriod::class, 'report_period_id');
    }

    public function getDetails()
    {
        $indicatorTarget = new stdClass();
        $indicatorTarget->id = $this->id;
        $indicatorTarget->keyResultAreaId = $this->key_result_area_id;
        $indicatorTarget->outcomeIndicatorId = $this->outcome_indicator_id;
        $indicatorTarget->outcomeIndicator = $this->indicator ? $this->indicator->getDetails() : null;
        $indicatorTarget->reportPeriodId = $this->report_period_id;
        $indicatorTarget->reportPeriod = $this->reportPeriod ? $this->reportPeriod->getDetails() : null;
        $indicatorTarget->target = $this->target;
        $indicatorTarget->dueDate = $this->due_date->toDateTimeString();
        $indicatorTarget->createdBy = $this->created_by;
        $indicatorTarget->updatedBy = $this->updated_by;
        $indicatorTarget->createdAt = $this->created_at->toDateTimeString();
        $indicatorTarget->updatedAt = $this->updated_at->toDateTimeString();
        return $indicatorTarget;
    }
}
