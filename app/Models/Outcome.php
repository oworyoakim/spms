<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Outcome
 * @package App\Models
 * @property int id
 * @property string name
 * @property string description
 * @property int key_result_area_id
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Outcome extends Model
{
    protected $dates = ['deleted_at'];

    public function keyResultArea(){
        return $this->belongsTo(KeyResultArea::class,'key_result_area_id');
    }

    public function indicators()
    {
        return $this->hasMany(OutcomeIndicator::class, 'output_id');
    }

    public function getDetails()
    {
        $outcome = new stdClass();
        $outcome->id = $this->id;
        $outcome->keyResultAreaId = $this->key_result_area_id;
        $outcome->name = $this->name;
        $outcome->description = $this->description;

        $outcome->indicators = $this->indicators()->get()->map(function (OutcomeIndicator $indicator){
            return $indicator->getDetails();
        });

        $outcome->createdBy = $this->created_by;
        $outcome->updatedBy = $this->updated_by;
        $outcome->createdAt = $this->created_at->toDateTimeString();
        $outcome->updatedAt = $this->updated_at->toDateTimeString();

        return $outcome;
    }
}
