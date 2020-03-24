<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Plan
 * @package App\Models
 * @property int id
 * @property string name
 * @property string theme
 * @property string mission
 * @property string vision
 * @property string values
 * @property string frequency
 * @property Carbon start_date
 * @property Carbon end_date
 * @property int created_by
 * @property Carbon created_at
 * @property int updated_by
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Plan extends Model
{
    protected $dates = ['start_date','end_date','deleted_at'];

    public function categories()
    {
        return $this->belongsToMany(SwotCategory::class, 'swot_categories_plans', 'category_id', 'plan_id');
    }
    public function swots()
    {
        return $this->hasMany(Swot::class, 'plan_id');
    }

    public function getDetails()
    {
        $plan = new stdClass();
        $plan->id = $this->id;
        $plan->name = $this->name;
        $plan->theme = $this->theme;
        $plan->mission = $this->mission;
        $plan->vision = $this->vision;
        $plan->values = $this->values;
        $plan->frequency = $this->frequency;
        $plan->startDate = $this->start_date->toDateTimeString();
        $plan->endDate = $this->end_date->toDateTimeString();
        $plan->createdBy = $this->created_by;
        $plan->updatedBy = $this->updated_by;
        $plan->createdAt = $this->created_at->toDateTimeString();
        $plan->updatedAt = $this->updated_at->toDateTimeString();
        return $plan;
    }
}
