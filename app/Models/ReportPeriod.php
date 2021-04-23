<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class ReportPeriod
 * @package App\Models
 * @property int id
 * @property int plan_id
 * @property string name
 * @property Carbon start_date
 * @property Carbon end_date
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class ReportPeriod extends Model
{
    protected $table = 'report_periods';
    protected $dates = ['start_date','end_date','deleted_at'];

    public function reportable()
    {
        return $this->morphTo();
    }

    public function getDetails()
    {
        $reportPeriod = new stdClass();
        $reportPeriod->id = $this->id;
        $reportPeriod->name = $this->name;
        $reportPeriod->startDate = $this->start_date->toDateString();
        $reportPeriod->endDate = $this->end_date->toDateString();
        return $reportPeriod;
    }
}
