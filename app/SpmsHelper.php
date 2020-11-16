<?php


namespace App;


use App\Models\Activity;
use App\Models\ActivityBlock;
use App\Models\Intervention;
use App\Models\KeyResultArea;
use App\Models\Objective;
use App\Models\Outcome;
use App\Models\Output;
use App\Models\Plan;
use App\Models\WorkPlan;

class SpmsHelper
{
    /**
     * @return array
     */
    public static function getFormSelections()
    {
        //TODO: Implement caching of the results
        $data = [
            'plans' => self::getPlans(),
            'workPlans' => self::getWorkPlans(),
            'objectives' => self::getObjectives(),
            'keyResultAreas' => self::getKeyResultAreas(),
            'outcomes' => self::getOutcomes(),
            'activityBlocks' => self::getActivityBlocks(),
            'outputs' => self::getOutputs(),
            'activities' => self::getActivities(),
            'interventions' => self::getInterventions(),
        ];

        return $data;
    }

    public static function getPlans()
    {
        return Plan::query()->get(['id', 'name as title']);
    }

    public static function getWorkPlans()
    {
        return WorkPlan::query()->get([
            'id',
            'title',
            'plan_id as planId',
            'financial_year as financialYear',
            'start_date as startDate',
            'end_date as endDate',
        ]);
    }

    public static function getKeyResultAreas()
    {
        return KeyResultArea::query()->get([
            'id',
            'name as title',
            'plan_id as planId',
        ]);
    }

    public static function getObjectives()
    {
        return Objective::query()->get([
            'id',
            'name as title',
            'plan_id as planId',
            'key_result_area_id as keyResultAreaId',
        ]);
    }

    public static function getOutcomes()
    {
        return Outcome::query()->get([
            'id',
            'name as title',
            'key_result_area_id as keyResultAreaId',
        ]);
    }

    public static function getActivityBlocks()
    {
        return ActivityBlock::query()->get([
            'id',
            'title',
            'objective_id as objectiveId',
            'outcome_id as outcomeId',
            'work_plan_id as workPlanId',
            'quarter',
        ]);
    }

    public static function getInterventions()
    {
        return Intervention::query()->get([
            'id',
            'name as title',
            'objective_id as objectiveId',
        ]);
    }

    public static function getActivities()
    {
        return Activity::query()->get([
            'id',
            'title',
            'activity_block_id as activityBlockId',
            'work_plan_id as workPlanId',
            'intervention_id as interventionId',
            'department_id as departmentId',
            'team_leader_id as teamLeaderId',
            'quarter',
        ]);
    }

    public static function getOutputs()
    {
        return Output::query()->get([
            'id',
            'name as title',
            'objective_id as objectiveId',
            'intervention_id as interventionId',
            'activity_id as activityId',
        ]);
    }
}
