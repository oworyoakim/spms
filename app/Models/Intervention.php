<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Intervention
 * @package App\Models
 * @property int id
 * @property string name
 * @property string description
 * @property int objective_id
 * @property int rank
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Intervention extends Model
{
    public function objective()
    {
        return $this->belongsTo(Objective::class, 'objective_id');
    }

    public function outputs()
    {
        return $this->hasMany(Output::class, 'intervention_id');
    }

    public function getDetails()
    {
        $intervention = new stdClass();
        $intervention->id = $this->id;
        $intervention->name = $this->name;
        $intervention->description = $this->description;
        $intervention->rank = $this->rank;
        $intervention->createdBy = $this->created_by;
        $intervention->updatedBy = $this->updated_by;
        $intervention->objectiveId = $this->objective_id;
        $intervention->objective = $this->objective ? $this->objective->getDetails() : null;
        /*
        $intervention->outputs = $this->outputs()
                                      ->get()
                                      ->map(function (Output $output) {
                                          return $output->getDetails();
                                      });
        */
        $intervention->createdAt = $this->created_at->toDateTimeString();
        $intervention->updatedAt = $this->updated_at->toDateTimeString();
        return $intervention;
    }
}
