<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class OutcomeIndicator
 * @package App\Models
 * @property int id
 * @property string name
 * @property string description
 * @property int outcome_id
 * @property string unit
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class OutcomeIndicator extends Model
{
    const UNIT_PERCENT = 'percent';
    const UNIT_COUNT = 'count';

    protected $dates = ['deleted_at'];

    public function outcome()
    {
        return $this->belongsTo(Outcome::class, 'outcome_id');
    }

    public function milestones()
    {
        return $this->hasMany(OutcomeIndicatorMilestone::class, 'outcome_indicator_id');
    }

    public function getDetails()
    {
        $indicator = new stdClass();
        $indicator->id = $this->id;
        $indicator->outcomeId = $this->outcome_id;
        $indicator->name = $this->name;
        $indicator->description = $this->description;
        $indicator->unit = $this->unit;
        $indicator->milestones = $this->milestones()->get()->map(function (OutcomeIndicatorMilestone $milestone) {
            return $milestone->getDetails();
        });
        $indicator->createdBy = $this->created_by;
        $indicator->createdAt = $this->created_at->toDateTimeString();
        $indicator->updatedBy = $this->updated_by;
        $indicator->updatedAt = $this->updated_at->toDateTimeString();

        return $indicator;
    }
}
