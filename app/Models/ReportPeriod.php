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

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function getDetails()
    {
        $intervention = new stdClass();
        $intervention->id = $this->id;
        $intervention->name = $this->name;
        $intervention->startDate = $this->start_date->toDateTimeString();
        $intervention->endDate = $this->end_date->toDateTimeString();
        $intervention->createdAt = $this->created_at->toDateTimeString();
        $intervention->updatedAt = $this->updated_at->toDateTimeString();
        return $intervention;
    }
}
