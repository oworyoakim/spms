<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class SwotCategory
 * @package App\Models
 * @property int id
 * @property string name
 * @property string description
 * @property int created_by
 * @property Carbon created_at
 * @property int updated_by
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class SwotCategory extends Model
{
    public function swots()
    {
        return $this->hasMany(Swot::class, 'category_id');
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'swot_categories_plans', 'plan_id', 'category_id');
    }

    public function getDetails()
    {
        $category = new stdClass();
        $category->id = $this->id;
        $category->name = $this->name;
        $category->description = $this->description;
        $category->createdBy = $this->created_by;
        $category->updatedBy = $this->updated_by;
        $category->createdAt = $this->created_at->toDateTimeString();
        $category->updatedAt = $this->updated_at->toDateTimeString();
        return $category;
    }
}
