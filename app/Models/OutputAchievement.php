<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class OutputAchievement
 * @package App\Models
 * @property int id
 * @property int objective_id
 * @property int output_indicator_id
 * @property int report_period_id
 * @property Carbon achievement_date
 * @property float actual
 * @property string description
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class OutputAchievement extends Model
{
    protected $table = 'output_achievements';
    protected $dates = ['achievement_date', 'deleted_at'];

    public function indicator()
    {
        return $this->belongsTo(OutputIndicator::class, 'output_indicator_id');
    }

    public function getDetails()
    {
        $achievement = new stdClass();
        $achievement->id = $this->id;
        $achievement->indicatorId = $this->output_indicator_id;
        $achievement->objectiveId = $this->objective_id;
        $achievement->reportPeriodId = $this->report_period_id;
        $achievement->indicator = $this->indicator ? $this->indicator->getDetails() : null;
        $achievement->actual = $this->actual;
        $achievement->description = $this->description;
        $achievement->achievementDate = $this->achievement_date->toDateString();
        $achievement->createdBy = $this->created_by;
        $achievement->updatedBy = $this->updated_by;
        $achievement->createdAt = $this->created_at->toDateTimeString();
        $achievement->updatedAt = $this->updated_at->toDateTimeString();
        return $achievement;
    }
}
