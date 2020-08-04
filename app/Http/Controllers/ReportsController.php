<?php

namespace App\Http\Controllers;

use App\Models\DirectiveResolution;
use App\Models\Plan;
use App\Models\ReportPeriod;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function strategyReport(Request $request)
    {
        try
        {
            $planId = $request->get('planId');
            $reportPeriodId = $request->get('reportPeriodId');
            if (empty($planId))
            {
                throw new Exception("Strategic plan required!");
            }
            if (empty($reportPeriodId))
            {
                throw new Exception("Report period required!");
            }
            $plan = Plan::query()->find($planId);
            if (empty($plan))
            {
                throw new Exception("Strategic plan not found!");
            }

//            if(Carbon::today()->lessThanOrEqualTo($plan->start_date)){
//                throw new Exception("No reports available for this criteria!");
//            }

            $planData = $plan->getReportData($reportPeriodId);

            return response()->json($planData);
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
            $directiveAndResolutionId = $request->get('directiveAndResolutionId');
            //$startDate = $request->get('startDate');
            //$endDate = $request->get('endDate');

            if (empty($planId))
            {
                throw new Exception("Strategic plan is required!");
            }
            $plan = Plan::query()->find($planId);
            if(!$plan){
                throw new Exception("Strategic plan not found!");
            }
            if (empty($directiveAndResolutionId))
            {
                throw new Exception("Directive or resolution required!");
            }
            $directiveAndResolution = DirectiveResolution::query()->find($directiveAndResolutionId);
            if (empty($directiveAndResolution))
            {
                throw new Exception("Directive or resolution not found!");
            }

//            if(empty($startDate) || empty($endDate)){
//                throw new Exception("Start and End dates are required!");
//            }

            $directiveAndResolutionData = $directiveAndResolution->generateReport();
            $directiveAndResolutionData->dateParams = $plan->getDateParams($directiveAndResolutionData->reportDate);

            return response()->json($directiveAndResolutionData);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
