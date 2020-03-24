<?php

namespace App\Http\Controllers;

use App\Models\OutcomeIndicatorMilestone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OutcomeIndicatorMilestonesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = OutcomeIndicatorMilestone::query();
            $outcomeIndicatorId = $request->get('outcomeIndicatorId');
            if($outcomeIndicatorId){
                $builder->where('outcome_indicator_id',$outcomeIndicatorId);
            }
            $milestones = $builder->get()->map(function (OutcomeIndicatorMilestone $milestone) {
                return $milestone->getDetails();
            });
            return response()->json($milestones);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
