<?php

namespace App\Http\Controllers;

use App\Models\ActivityBlock;
use App\Models\DirectiveResolution;
use App\Models\Plan;
use App\Models\ReportPeriod;
use App\Models\WorkPlan;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class ReportsController extends Controller
{
    public function strategyReport(Request $request)
    {
        try
        {
            $planId = $request->get('planId');
            if (empty($planId))
            {
                throw new Exception("Strategic plan required!");
            }
            $plan = Plan::query()->find($planId);
            if (empty($plan))
            {
                throw new Exception("Strategic plan not found!");
            }

            $reportType = $request->get('reportType');
            if ($reportType == 'summary')
            {
                $planData = $plan->getSummaryReportData();
            } else
            {
                $reportPeriodId = $request->get('reportPeriodId');
                if (empty($reportPeriodId))
                {
                    throw new Exception("Report period required!");
                }
                $planData = $plan->getReportData($reportPeriodId);
            }

//            if(Carbon::today()->lessThanOrEqualTo($plan->start_date)){
//                throw new Exception("No reports available for this criteria!");
//            }

            return response()->json($planData);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function activityReport(Request $request)
    {
        try
        {
            $planId = $request->get('planId');
            $workPlanId = $request->get('workPlanId');

            if (empty($workPlanId))
            {
                throw new Exception("Work plan is required!");
            }

            $plan = Plan::query()->find($planId);
            if (empty($plan))
            {
                throw new Exception("Strategic plan not found!");
            }

            $workPlan = WorkPlan::query()->find($workPlanId);
            if (empty($workPlan))
            {
                throw new Exception("Work plan not found!");
            }

            $quarter = $request->get('quarter');
            $directorateId = intval($request->get('directorateId'));

            $reportData = new stdClass();

            $builder = ActivityBlock::query();
            $builder->where('work_plan_id', $workPlanId);

            if ($quarter)
            {
                $builder->where('quarter', $quarter);
            }

            $reportData->activityBlocks = $builder->get()
                                                  ->filter(function (ActivityBlock $activityBlock) use ($directorateId) {
                                                      $directorateIds = $activityBlock->directorates()->pluck('directorate_id')->all();
                                                      return empty($directorateId) || in_array($directorateId, $directorateIds);
                                                  })
                                                  ->map(function (ActivityBlock $activityBlock) {
                                                      $activityBlockReport = $activityBlock->generateReportData();

                                                      return $activityBlockReport;
                                                  });

            $reportData->reportDate = Carbon::today()->toDateString();
            $reportData->workPlan = $workPlan->getDetails();
            $reportData->plan = $plan->name;
            $reportData->reportFrequency = $plan->frequency;
            $reportData->quarter = $quarter;
            $reportData->directorateId = $directorateId;
            $reportData->dateParams = $plan->getDateParams($reportData->reportDate);

            return response()->json($reportData);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function directivesAndResolutionsReport(Request $request)
    {
        try
        {
            $planId = $request->get('planId');

            $workPlanId = $request->get('workPlanId');
            if (empty($workPlanId))
            {
                throw new Exception("Work plan is required!");
            }

            $plan = Plan::query()->find($planId);
            if (empty($plan))
            {
                throw new Exception("Strategic plan not found!");
            }

            $workPlan = WorkPlan::query()->find($workPlanId);
            if (empty($workPlan))
            {
                throw new Exception("Work plan not found!");
            }
            $reportData = new stdClass();
            $directivesAndResolutions = DirectiveResolution::query()
                                                           ->where('work_plan_id', $workPlanId)
                                                           ->get()
                                                           ->map(function (DirectiveResolution $directiveAndResolution) {
                                                               return $directiveAndResolution->generateReport();
                                                           });

            $reportData->directivesAndResolutions = $directivesAndResolutions;

            $reportData->reportDate = Carbon::today()->toDateString();
            $reportData->workPlan = $workPlan->getDetails();
            $reportData->plan = $plan->name;
            $reportData->reportFrequency = $plan->frequency;
            $reportData->dateParams = $plan->getDateParams($reportData->reportDate);

            return response()->json($reportData);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
