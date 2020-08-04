<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Swot
 * @package App\Models
 * @property int id
 * @property string name
 * @property string description
 * @property string type
 * @property int category_id
 * @property int plan_id
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Swot extends Model
{
    const TYPE_STRENGTHS = 'strengths';
    const TYPE_WEAKNESSES = 'weaknesses';
    const TYPE_OPPORTUNITIES = 'opportunities';
    const TYPE_THREATS = 'threats';

    protected $dates = ['deleted_at'];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function category()
    {
        return $this->belongsTo(SwotCategory::class, 'category_id');
    }

    public function scopeStrengths(Builder $query){
        return $query->where('type',self::TYPE_STRENGTHS);
    }

    public function scopeWeaknesses(Builder $query){
        return $query->where('type',self::TYPE_WEAKNESSES);
    }

    public function scopeOpportunities(Builder $query){
        return $query->where('type',self::TYPE_OPPORTUNITIES);
    }

    public function scopeThreats(Builder $query){
        return $query->where('type',self::TYPE_THREATS);
    }

    public function getDetails()
    {
        $swot = new stdClass();
        $swot->id = $this->id;
        //$swot->name = $this->name;
        $swot->description = $this->description;
        $swot->type = $this->type;
        $swot->createdBy = $this->created_by;
        $swot->updatedBy = $this->updated_by;
        $swot->planId = $this->plan_id;
        //$swot->categoryId = $this->category_id;
        //$swot->category = null;
//        if ($this->category)
//        {
//            $swot->category = new stdClass();
//            $swot->category->id = $this->category_id;
//            $swot->category->name = $this->category->name;
//            $swot->category->description = $this->category->description;
//        }
        $swot->createdAt = $this->created_at->toDateTimeString();
        $swot->updatedAt = $this->updated_at->toDateTimeString();
        return $swot;
    }
}
