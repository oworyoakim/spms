<?php

namespace App\Http\Controllers;

use App\Models\OutcomeIndicatorTarget;
use App\Models\ReportPeriod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OutcomeIndicatorTargetsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = OutcomeIndicatorTarget::query();
            $outcomeIndicatorId = $request->get('outcomeIndicatorId');
            if ($outcomeIndicatorId)
            {
                $builder->where('outcome_indicator_id', $outcomeIndicatorId);
            }
            $targets = $builder->get()
                               ->map(function (OutcomeIndicatorTarget $milestone) {
                                   return $milestone->getDetails();
                               });
            return response()->json($targets);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $rules = [
                'target' => 'required|numeric',
                'keyResultAreaId' => 'required',
                'outcomeIndicatorId' => 'required',
                'reportPeriodId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $reportPeriodId = $request->get('reportPeriodId');
            $reportPeriod = ReportPeriod::query()->find($reportPeriodId);
            if (!$reportPeriod)
            {
                throw new Exception("Report period is required!");
            }
            OutcomeIndicatorTarget::query()->updateOrCreate([
                'key_result_area_id' => $request->get('keyResultAreaId'),
                'outcome_indicator_id' => $request->get('outcomeIndicatorId'),
                'report_period_id' => $reportPeriodId,
            ], [
                'target' => $request->get('target'),
                'due_date' => $reportPeriod->end_date,
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Outcome indicator created!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            $rules = [
                'id' => 'required',
                'target' => 'required|numeric',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $outcomeIndicatorTarget = OutcomeIndicatorTarget::query()->find($id);
            if (!$outcomeIndicatorTarget)
            {
                throw new Exception("Outcome indicator target with id {$id} not found!");
            }

            $outcomeIndicatorTarget->update([
                'target' => $request->get('target'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Outcome indicator target updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
