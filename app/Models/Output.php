<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Output
 * @package App\Models
 * @property int id
 * @property string name
 * @property string description
 * @property int objective_id
 * @property int intervention_id
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Output extends Model
{
    protected $dates = ['deleted_at'];

    public function intervention()
    {
        return $this->belongsTo(Intervention::class, 'intervention_id');
    }

    public function indicators()
    {
        return $this->hasMany(OutputIndicator::class, 'output_id');
    }

    public function getDetails()
    {
        $output = new stdClass();
        $output->id = $this->id;
        $output->objectiveId = $this->objective_id;
        $output->interventionId = $this->intervention_id;
        $output->name = $this->name;
        $output->description = $this->description;
        $output->intervention = $this->intervention ? $this->intervention->getDetails() : null;
        /*
        $output->indicators = $this->indicators()
                                   ->get()
                                   ->map(function (OutputIndicator $indicator) {
                                       return $indicator->getDetails();
                                   });
        */
        $output->createdBy = $this->created_by;
        $output->updatedBy = $this->updated_by;
        $output->createdAt = $this->created_at->toDateTimeString();
        $output->updatedAt = $this->updated_at->toDateTimeString();

        return $output;
    }
}
