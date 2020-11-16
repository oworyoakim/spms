<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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
 * @property string state
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

    public function objectives()
    {
        return $this->hasMany(Objective::class, 'plan_id');
    }

    public function keyResultAreas()
    {
        return $this->hasMany(KeyResultArea::class, 'plan_id');
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
        $plan->status = $this->state;
        $plan->financialYears = $this->financialYears();
        $plan->reportPeriods = $this->reportPeriods()
                                    ->get()
                                    ->map(function (ReportPeriod $period) {
                                        return $period->getDetails();
                                    });

        $previousReportPeriod = $this->getPreviousReportPeriod();
        $plan->previousReportPeriod = empty($previousReportPeriod) ? null : $previousReportPeriod->getDetails();

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

    public function financialYears()
    {
        $financialYears = [];
        $periodStartDate = $this->start_date->clone();
        while ($periodStartDate->lessThan($this->end_date))
        {
            $periodEndDate = $periodStartDate->clone()->addMonths(12)->subDays(1);
            $financialYear = "{$periodStartDate->year}/{$periodEndDate->year}";
            $financialYears[] = $financialYear;
            $periodStartDate = $periodEndDate->addDays(1);
        }

        return $financialYears;
    }

    /**
     * @return ReportPeriod|null
     */
    public function getPreviousReportPeriod()
    {
        $today = Carbon::today();
        if ($today->lessThan($this->start_date))
        {
            return null;
        }
        $previousPeriod = null;
        foreach ($this->reportPeriods()->orderBy('start_date')->get() as $period)
        {
            if ($today->isBetween($period->start_date, $period->end_date))
            {
                break;
            }
            $previousPeriod = $period;
        }
        return empty($previousPeriod) ? null : $previousPeriod;
    }

    /**
     * @param $reportPeriodId
     *
     * @return stdClass
     * @throws Exception
     */
    public function getReportData($reportPeriodId)
    {
        $reportPeriod = $this->reportPeriods()->find($reportPeriodId);

        if (empty($reportPeriod))
        {
            throw new Exception("Reporting period not found!");

        }

        $startDate = $reportPeriod->startDate;
        $endDate = $reportPeriod->endDate;
        $comment = "The purpose of UNEB examinations is selection, certification and accountability.";

        $reportData = new stdClass();
        $reportData->startDate = $startDate;
        $reportData->endDate = $endDate;
        $reportData->reportFrequency = $this->frequency;
        $reportData->reportDate = Carbon::today()->toDateString();
        $reportData->plan = $this->name;
        $reportData->reportPeriod = $reportPeriod->getDetails();
        $reportData->dateParams = $this->getDateParams($reportData->reportPeriod->endDate);

        $reportData->objectives = Collection::make();
        foreach ($this->objectives as $objective)
        {
            $strategicObjective = new stdClass();
            $strategicObjective->name = $objective->name;
            $strategicObjective->rank = $objective->rank;
            // Strategic Interventions
            $strategicObjective->interventions = Collection::make();
            foreach ($objective->interventions as $intervention)
            {
                $strategicIntervention = new stdClass();
                $strategicIntervention->name = $intervention->name;
                $strategicIntervention->rank = $intervention->rank;
                // Outputs
                $strategicIntervention->outputs = Collection::make();
                foreach ($intervention->outputs as $output)
                {
                    $interventionOutput = new stdClass();
                    $interventionOutput->name = $output->name;
                    $interventionOutput->rank = $output->rank;
                    // Indicators
                    $interventionOutput->indicators = Collection::make();
                    foreach ($output->indicators as $indicator)
                    {
                        $outputIndicator = new stdClass();
                        $outputIndicator->name = $indicator->name;
                        $outputIndicator->rank = $indicator->rank;
                        $outputIndicator->unit = $indicator->unit;
                        // targets
                        $target = $indicator->targets()->where('report_period_id', $reportPeriod->id)->first();
                        $outputIndicator->target = empty($target) ? null : $target->target;
                        // achievement
                        $achievement = $indicator->achievements()->where('report_period_id', $reportPeriod->id)->first();
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
                        $interventionOutput->indicators->push($outputIndicator);
                    }

                    $strategicIntervention->outputs->push($interventionOutput);
                }

                $strategicObjective->interventions->push($strategicIntervention);
            }

            $reportData->objectives->push($strategicObjective);
        }

        return $reportData;
    }


    public function getDateParams($date)
    {
        $date = Carbon::parse($date);
        if ($date->isAfter("{$date->year}-06-30"))
        {
            $year = $date->year + 1;
            $fyStartDate = Carbon::parse("{$date->year}-07-01");
            $fyEndDate = Carbon::parse("{$year}-06-30");
        } else
        {
            $year = $date->year - 1;
            $fyStartDate = Carbon::parse("{$year}-07-01");
            $fyEndDate = Carbon::parse("{$date->year}-06-30");
        }
        $fYear = "{$fyStartDate->year}/{$fyEndDate->year}";
        $params = new stdClass();
        $params->financialYear = $fYear;
        $params->financialYearStartDate = $fyStartDate->toDateString();
        $params->financialYearEndDate = $fyEndDate->toDateString();
        $quarters = [];
        $currentDate = $fyStartDate->clone();
        $qIndex = 0;
        while ($currentDate->lessThan($fyEndDate))
        {
            $quarter = new stdClass();
            $quarter->startDate = $currentDate->toDateString();
            $quarter->name = "Q" . (++$qIndex);
            $currentDate = $currentDate->addMonths(3)->subDays(1);
            $quarter->endDate = $currentDate->toDateString();
            $quarter->isCurrent = $date->between($quarter->startDate, $quarter->endDate);
            $quarters[] = $quarter;
            if ($quarter->isCurrent)
            {
                $params->currentQuarter = $quarter;
            }
            $currentDate = $currentDate->addDays(1);
        }

        $params->quarters = $quarters;

        return $params;
    }

}
