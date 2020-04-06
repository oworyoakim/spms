<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class OutcomeAchievement
 * @package App\Models
 * @property int id
 * @property int key_result_area_id
 * @property int outcome_indicator_id
 * @property Carbon achievement_date
 * @property float actual
 * @property string description
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class OutcomeAchievement extends Model
{
    protected $table = 'outcome_achievements';
    protected $dates = ['achievement_date', 'deleted_at'];

    public function indicator()
    {
        return $this->belongsTo(OutcomeIndicator::class, 'outcome_indicator_id');
    }

    public function getDetails()
    {
        $achievement = new stdClass();
        $achievement->id = $this->id;
        $achievement->keyResultAreaId = $this->key_result_area_id;
        $achievement->indicatorId = $this->outcome_indicator_id;
        $achievement->indicator = $this->indicator ? $this->indicator->getDetails() : null;
        $achievement->actual = $this->actual;
        $achievement->description = $this->description;
        $achievement->achievementDate = $this->achievement_date->toDateTimeString();
        $achievement->createdBy = $this->created_by;
        $achievement->updatedBy = $this->updated_by;
        $achievement->createdAt = $this->created_at->toDateTimeString();
        $achievement->updatedAt = $this->updated_at->toDateTimeString();
        return $achievement;
    }
}
