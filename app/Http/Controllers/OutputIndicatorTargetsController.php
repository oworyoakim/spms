<?php

namespace App\Http\Controllers;

use App\Models\OutputIndicatorTarget;
use App\Models\ReportPeriod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OutputIndicatorTargetsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = OutputIndicatorTarget::query();
            $outputIndicatorId = $request->get('outputIndicatorId');
            if ($outputIndicatorId)
            {
                $builder->where('outcome_indicator_id', $outputIndicatorId);
            }
            $milestones = $builder->get()->map(function (OutputIndicatorTarget $milestone) {
                return $milestone->getDetails();
            });
            return response()->json($milestones);
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
                'objectiveId' => 'required',
                'outputIndicatorId' => 'required',
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
            OutputIndicatorTarget::query()->create([
                'objective_id' => $request->get('objectiveId'),
                'output_indicator_id' => $request->get('outputIndicatorId'),
                'report_period_id' => $reportPeriodId,
                'target' => $request->get('target'),
                'due_date' => $reportPeriod->end_date,
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Output indicator target created!");
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
                'reportPeriodId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $outputIndicatorTarget = OutputIndicatorTarget::query()->find($id);
            if (!$outputIndicatorTarget)
            {
                throw new Exception("Output indicator target with id {$id} not found!");
            }
            $reportPeriodId = $request->get('reportPeriodId');
            $reportPeriod = ReportPeriod::query()->find($reportPeriodId);
            if (!$reportPeriod)
            {
                throw new Exception("Report period is required!");
            }

            $outputIndicatorTarget->update([
                'report_period_id' => $reportPeriodId,
                'target' => $request->get('target'),
                'due_date' => $reportPeriod->end_date,
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Output indicator target updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
