<?php

namespace App\Http\Controllers;

use App\Models\OutputIndicatorMilestone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OutputIndicatorMilestonesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = OutputIndicatorMilestone::query();
            $outputIndicatorId = $request->get('outputIndicatorId');
            if($outputIndicatorId){
                $builder->where('outcome_indicator_id',$outputIndicatorId);
            }
            $milestones = $builder->get()->map(function (OutputIndicatorMilestone $milestone) {
                return $milestone->getDetails();
            });
            return response()->json($milestones);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
