<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 12/17/2020
 * Time: 8:25 AM
 */

namespace App\Traits;

use App\Models\ReportPeriod;
use Exception;
use Illuminate\Support\Collection;

trait Reportable
{
    static $FREQUENCY_MONTHLY = 'monthly';
    static $FREQUENCY_QUARTERLY = 'quarterly';
    static $FREQUENCY_TRIMONTHLY = '4-months';
    static $FREQUENCY_HALF_YEARLY = '6-months';
    static $FREQUENCY_YEARLY = 'yearly';

    public function reportPeriods()
    {
        return $this->morphMany(ReportPeriod::class, 'reportable');
    }

    public function createReportPeriods()
    {
        $periods = Collection::make();
        $periodStartDate = $this->start_date->clone();
        switch ($this->frequency)
        {
            case self::$FREQUENCY_MONTHLY:
                while ($periodStartDate->lessThan($this->end_date))
                {
                    $periodEndDate = $periodStartDate->clone()->addMonths(1)->subDays(1);
                    $name = "{$periodStartDate->format('d/m/Y')} - {$periodEndDate->format('d/m/Y')}";
                    $period = new ReportPeriod([
                        'name' => $name,
                        'start_date' => $periodStartDate->clone()->toDateString(),
                        'end_date' => $periodEndDate->clone()->toDateString(),
                    ]);
                    $periods->push($period);
                    $periodStartDate = $periodEndDate->addDays(1);
                }
                break;
            case self::$FREQUENCY_QUARTERLY:
                while ($periodStartDate->lessThan($this->end_date))
                {
                    $periodEndDate = $periodStartDate->clone()->addMonths(3)->subDays(1);
                    $name = "{$periodStartDate->format('d/m/Y')} - {$periodEndDate->format('d/m/Y')}";
                    $period = new ReportPeriod([
                        'name' => $name,
                        'start_date' => $periodStartDate->clone()->toDateString(),
                        'end_date' => $periodEndDate->clone()->toDateString(),
                    ]);
                    $periods->push($period);
                    $periodStartDate = $periodEndDate->addDays(1);
                }
                break;
            case self::$FREQUENCY_TRIMONTHLY:
                while ($periodStartDate->lessThan($this->end_date))
                {
                    $periodEndDate = $periodStartDate->clone()->addMonths(4)->subDays(1);
                    $name = "{$periodStartDate->format('d/m/Y')} - {$periodEndDate->format('d/m/Y')}";
                    $period = new ReportPeriod([
                        'name' => $name,
                        'start_date' => $periodStartDate->clone()->toDateString(),
                        'end_date' => $periodEndDate->clone()->toDateString(),
                    ]);
                    $periods->push($period);
                    $periodStartDate = $periodEndDate->addDays(1);
                }
                break;
            case self::$FREQUENCY_HALF_YEARLY:
                while ($periodStartDate->lessThan($this->end_date))
                {
                    $periodEndDate = $periodStartDate->clone()->addMonths(6)->subDays(1);
                    $name = "{$periodStartDate->format('d/m/Y')} - {$periodEndDate->format('d/m/Y')}";
                    $period = new ReportPeriod([
                        'name' => $name,
                        'start_date' => $periodStartDate->clone()->toDateString(),
                        'end_date' => $periodEndDate->clone()->toDateString(),
                    ]);
                    $periods->push($period);
                    $periodStartDate = $periodEndDate->addDays(1);
                }
                break;
            case self::$FREQUENCY_YEARLY:
                while ($periodStartDate->lessThan($this->end_date))
                {
                    $periodEndDate = $periodStartDate->clone()->addMonths(12)->subDays(1);
                    $name = "{$periodStartDate->format('d/m/Y')} - {$periodEndDate->format('d/m/Y')}";
                    $period = new ReportPeriod([
                        'name' => $name,
                        'start_date' => $periodStartDate->clone()->toDateString(),
                        'end_date' => $periodEndDate->clone()->toDateString(),
                    ]);
                    $periods->push($period);
                    $periodStartDate = $periodEndDate->addDays(1);
                }
                break;
            default:
                break;
        }
        if ($periods->count())
        {
            $this->reportPeriods()->forceDelete();
            $this->reportPeriods()->saveMany($periods->all());
        }
        // throw new Exception("{$this->frequency} {$periods->count()} report periods created!");
    }
}
