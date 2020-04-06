<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class OutputIndicator
 * @package App\Models
 * @property int id
 * @property string name
 * @property string description
 * @property int output_id
 * @property int objective_id
 * @property string unit
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class OutputIndicator extends Model
{
    const UNIT_PERCENT = 'percent';
    const UNIT_COUNT = 'count';

    protected $dates = ['deleted_at'];

    public function output()
    {
        return $this->belongsTo(Output::class, 'output_id');
    }

    public function targets()
    {
        return $this->hasMany(OutputIndicatorTarget::class, 'output_indicator_id');
    }

    public function achievements()
    {
        return $this->hasMany(OutputAchievement::class, 'output_indicator_id');
    }

    public function getDetails()
    {
        $indicator = new stdClass();
        $indicator->id = $this->id;
        $indicator->objectiveId = $this->objective_id;
        $indicator->outputId = $this->output_id;
        $indicator->name = $this->name;
        $indicator->description = $this->description;
        $indicator->unit = $this->unit;
        /*
        $indicator->targets = $this->targets()
                                      ->get()
                                      ->map(function (OutputIndicatorTarget $target) {
                                          return $target->getDetails();
                                      });
        $indicator->achievements = $this->achievements()
                                        ->get()
                                        ->map(function (OutputAchievement $achievement) {
                                            return $achievement->getDetails();
                                        });
        */
        $indicator->createdBy = $this->created_by;
        $indicator->createdAt = $this->created_at->toDateTimeString();
        $indicator->updatedBy = $this->updated_by;
        $indicator->updatedAt = $this->updated_at->toDateTimeString();

        return $indicator;
    }
}
