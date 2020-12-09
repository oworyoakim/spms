<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use stdClass;

/**
 * Class ActivityBlock
 * @package App\Models
 * @property int id
 * @property int objective_id
 * @property int outcome_id
 * @property int output_id
 * @property int output_indicator_id
 * @property int work_plan_id
 * @property string title
 * @property string description
 * @property string quarter
 * @property string code
 * @property float cost
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class ActivityBlock extends Model
{
    protected $table = 'activity_blocks';

    protected $dates = ['deleted_at'];

    protected $guarded = [];

    public function outcome()
    {
        return $this->belongsTo(Outcome::class, 'outcome_id');
    }

    public function indicator()
    {
        return $this->belongsTo(OutputIndicator::class, 'output_indicator_id');
    }

    public function objective()
    {
        return $this->belongsTo(Objective::class, 'objective_id');
    }

    public function workPlan()
    {
        return $this->belongsTo(WorkPlan::class, 'work_plan_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'activity_block_id');
    }

    public function directorates()
    {
        return $this->hasMany(ActivityBlockDirectorate::class, 'activity_block_id');
    }

    public function getDetails()
    {
        $activityBlock = new stdClass();
        $activityBlock->id = $this->id;
        $activityBlock->objectiveId = $this->objective_id;
        $activityBlock->workPlanId = $this->work_plan_id;
        $activityBlock->outcomeId = $this->outcome_id;
        $activityBlock->title = $this->title;
        $activityBlock->description = $this->description;
        $activityBlock->quarter = $this->quarter;
        $activityBlock->code = $this->code;
        $activityBlock->cost = $this->cost;
        $activityBlock->directorateIds = $this->directorates()->pluck('directorate_id')->all();
        $activityBlock->objective = $this->objective ? $this->objective->getDetails() : null;
        $activityBlock->outcome = $this->outcome ? $this->outcome->getDetails() : null;
        $activityBlock->outputId = $this->output_id;
        $activityBlock->output = null;
        $activityBlock->indicatorId = $this->output_indicator_id;
        $activityBlock->indicator = null;
        if($this->indicator){
            $activityBlock->indicator = new stdClass();
            $activityBlock->indicator->id = $this->output_indicator_id;
            $activityBlock->indicator->name = $this->indicator->name;
        }
        $activityBlock->createdBy = $this->created_by;
        $activityBlock->updatedBy = $this->updated_by;
        $activityBlock->createdAt = $this->created_at->toDateTimeString();
        $activityBlock->updatedAt = $this->updated_at->toDateTimeString();
        return $activityBlock;
    }

    public function generateReportData()
    {
        $reportData = new stdClass();
        $reportData->id = $this->id;
        $reportData->objectiveId = $this->objective_id;
        $reportData->workPlanId = $this->work_plan_id;
        $reportData->outcomeId = $this->outcome_id;
        $reportData->name = $this->title;
        $reportData->description = $this->description;
        $reportData->quarter = $this->quarter;
        $reportData->code = $this->code;
        $reportData->cost = $this->cost;
        $reportData->indicatorId = $this->output_indicator_id;
        $reportData->indicator = null;
        $reportData->outputId = $this->output_id;
        $reportData->output = null;
        if($this->output){
            $reportData->output = new stdClass();
            $reportData->output->id = $this->output_id;
            $reportData->output->name = $this->output->name;
        }
        if($this->indicator){
            $reportData->indicator = new stdClass();
            $reportData->indicator->id = $this->output_indicator_id;
            $reportData->indicator->name = $this->indicator->name;
            $reportData->indicator->unit = $this->indicator->unit;
            $target = null;
            // $target = $this->indicator->targets()->where('report_period_id', $reportPeriod->id)->first();
            $reportData->indicator->target = empty($target) ? null : $target->target;
            $achievement = null;
            // $achievement = $this->indicator->achievements()->where('report_period_id', $reportPeriod->id)->first();
            $reportData->indicator->actual = empty($achievement) ? null : $achievement->actual;
            //  Percentage of achievement and variance
            if ($reportData->indicator->target && $reportData->indicator->actual)
            {
                $reportData->indicator->achieved = round(($reportData->indicator->actual / $reportData->indicator->target) * 100, 2);
                $reportData->indicator->variance = $reportData->indicator->target - $reportData->indicator->actual;
            } else
            {
                $reportData->indicator->achieved = null;
                $reportData->indicator->variance = null;
            }
        }
        $comment = "The purpose of UNEB examinations is selection, certification and accountability.";
        $reportData->activities = Collection::make();
        foreach ($this->activities as $activity)
        {
            $activityData = new stdClass();
            $activityData->name = $activity->title;
            $activityData->status = $activity->status;
            $activityData->cost = $activity->cost;
            // Outputs
            $activityData->outputs = Collection::make();

            foreach ($activity->outputs as $output)
            {
                $activityOutput = new stdClass();
                $activityOutput->name = $output->name;
                $activityOutput->rank = $output->rank;
                // Indicators
                $activityOutput->indicators = Collection::make();
                foreach ($output->indicators as $indicator)
                {
                    $outputIndicator = new stdClass();
                    $outputIndicator->name = $indicator->name;
                    $outputIndicator->rank = $indicator->rank;
                    $outputIndicator->unit = $indicator->unit;
                    // targets
                    //$target = $indicator->targets()->where('report_period_id', $reportPeriod->id)->first();
                    $target = null;
                    $outputIndicator->target = empty($target) ? null : $target->target;
                    // achievement
                    //$achievement = $indicator->achievements()->where('report_period_id', $reportPeriod->id)->first();
                    $achievement = null;
                    $outputIndicator->actual = empty($achievement) ? null : $achievement->actual;

                    //  Percentage of achievement and variance
                    if ($outputIndicator->target && $outputIndicator->actual)
                    {
                        $outputIndicator->achieved = round(($outputIndicator->actual / $outputIndicator->target) * 100, 2);
                        $outputIndicator->variance = $outputIndicator->target - $outputIndicator->actual;
                    } else
                    {
                        $outputIndicator->achieved = null;
                        $outputIndicator->variance = null;
                    }
                    $outputIndicator->comments = $comment;
                    $activityOutput->indicators->push($outputIndicator);
                }

                $activityData->outputs->push($activityOutput);
            }

            $reportData->activities->push($activityData);
        }

        return $reportData;
    }

}
