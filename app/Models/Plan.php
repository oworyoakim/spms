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
 * @property string status
 * @property Carbon start_date
 * @property Carbon end_date
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Plan extends Model
{
    protected $dates = ['start_date', 'end_date', 'deleted_at'];

    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_QUARTERLY = 'quarterly';
    const FREQUENCY_TRIMONTHLY = '4-months';
    const FREQUENCY_HALF_YEARLY = '6-months';
    const FREQUENCY_YEARLY = 'yearly';

    const REPORT_FREQUENCIES = [
        self::FREQUENCY_MONTHLY ?: 'monthly',
        self::FREQUENCY_QUARTERLY ?: 'quarterly',
        self::FREQUENCY_TRIMONTHLY ?: '4-months',
        self::FREQUENCY_HALF_YEARLY ?: '6-months',
        self::FREQUENCY_YEARLY ?: 'yearly',
    ];

    const STATE_PLANNING = 'planing';
    const STATE_EXECUTION = 'execution';
    const STATE_COMPLETED = 'completed';

    const STATES = [
        self::STATE_PLANNING,
        self::STATE_EXECUTION,
        self::STATE_COMPLETED,
    ];

    public function categories()
    {
        return $this->belongsToMany(SwotCategory::class, 'swot_categories_plans', 'category_id', 'plan_id');
    }

    public function swots()
    {
        return $this->hasMany(Swot::class, 'plan_id');
    }

    public function reportPeriods()
    {
        return $this->hasMany(ReportPeriod::class, 'plan_id');
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
        $plan->status = $this->status;
        $plan->reportPeriods = $this->reportPeriods()
                                    ->get()
                                    ->map(function (ReportPeriod $period) {
                                        return $period->getDetails();
                                    });
        $plan->startDate = $this->start_date->toDateString();
        $plan->endDate = $this->end_date->toDateString();
        $plan->createdBy = $this->created_by;
        $plan->updatedBy = $this->updated_by;
        $plan->createdAt = $this->created_at->toDateTimeString();
        $plan->updatedAt = $this->updated_at->toDateTimeString();
        return $plan;
    }

    public function createPeriods()
    {
        $periods = [];
        $periodStartDate = $this->start_date->clone();
        switch ($this->frequency)
        {
            case self::FREQUENCY_MONTHLY:
                while ($periodStartDate->lessThan($this->end_date))
                {
                    $periodEndDate = $periodStartDate->clone()->addMonths(1)->subDays(1);
                    $name = "{$periodStartDate->format('d/m/Y')} - {$periodEndDate->format('d/m/Y')}";
                    $periods[] = new ReportPeriod([
                        'name' => $name,
                        'start_date' => $periodStartDate->clone()->toDateString(),
                        'end_date' => $periodEndDate->clone()->toDateString(),
                    ]);
                    $periodStartDate = $periodEndDate->addDays(1);
                }
                break;
            case self::FREQUENCY_QUARTERLY:
                while ($periodStartDate->lessThan($this->end_date))
                {
                    $periodEndDate = $periodStartDate->clone()->addMonths(3)->subDays(1);
                    $name = "{$periodStartDate->format('d/m/Y')} - {$periodEndDate->format('d/m/Y')}";
                    $periods[] = new ReportPeriod([
                        'name' => $name,
                        'start_date' => $periodStartDate->clone()->toDateString(),
                        'end_date' => $periodEndDate->clone()->toDateString(),
                    ]);
                    $periodStartDate = $periodEndDate->addDays(1);
                }
                break;
            case self::FREQUENCY_TRIMONTHLY:
                while ($periodStartDate->lessThan($this->end_date))
                {
                    $periodEndDate = $periodStartDate->clone()->addMonths(4)->subDays(1);
                    $name = "{$periodStartDate->format('d/m/Y')} - {$periodEndDate->format('d/m/Y')}";
                    $periods[] = new ReportPeriod([
                        'name' => $name,
                        'start_date' => $periodStartDate->clone()->toDateString(),
                        'end_date' => $periodEndDate->clone()->toDateString(),
                    ]);
                    $periodStartDate = $periodEndDate->addDays(1);
                }
                break;
            case self::FREQUENCY_HALF_YEARLY:
                while ($periodStartDate->lessThan($this->end_date))
                {
                    $periodEndDate = $periodStartDate->clone()->addMonths(6)->subDays(1);
                    $name = "{$periodStartDate->format('d/m/Y')} - {$periodEndDate->format('d/m/Y')}";
                    $periods[] = new ReportPeriod([
                        'name' => $name,
                        'start_date' => $periodStartDate->clone()->toDateString(),
                        'end_date' => $periodEndDate->clone()->toDateString(),
                    ]);
                    $periodStartDate = $periodEndDate->addDays(1);
                }
                break;
            case self::FREQUENCY_YEARLY:
                while ($periodStartDate->lessThan($this->end_date))
                {
                    $periodEndDate = $periodStartDate->clone()->addMonths(12)->subDays(1);
                    $name = "{$periodStartDate->format('d/m/Y')} - {$periodEndDate->format('d/m/Y')}";
                    $periods[] = new ReportPeriod([
                        'name' => $name,
                        'start_date' => $periodStartDate->clone()->toDateString(),
                        'end_date' => $periodEndDate->clone()->toDateString(),
                    ]);
                    $periodStartDate = $periodEndDate->addDays(1);
                }
                break;
            default:
                break;
        }
        if (count($periods))
        {
            $this->reportPeriods()->forceDelete();
            $this->reportPeriods()->saveMany($periods);
        }
    }
}
