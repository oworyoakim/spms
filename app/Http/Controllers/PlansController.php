<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\ReportPeriod;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlansController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Plan::query();
            $plans = $builder->get()->map(function (Plan $plan) {
                return $plan->getDetails();
            });
            return response()->json($plans);
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
                'name' => 'required',
                'theme' => 'required',
                'frequency' => 'required',
                'startDate' => 'required|date',
                'endDate' => 'required|date',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $startDate = Carbon::parse($request->get('startDate'));
            $endDate = Carbon::parse($request->get('endDate'));
            $frequency = $request->get('frequency');
            DB::beginTransaction();
            /**
             * @var Plan $plan
             */
            $plan = Plan::query()->create([
                'name' => $request->get('name'),
                'theme' => $request->get('theme'),
                'frequency' => $frequency,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'mission' => $request->get('mission'),
                'vision' => $request->get('vision'),
                'values' => $request->get('values'),
                'created_by' => $request->get('userId'),
            ]);
            $plan->createReportPeriods();
            DB::commit();
            return response()->json("Plan Created!");
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            $rules = [
                'id' => 'required',
                'name' => 'required',
                'theme' => 'required',
                'frequency' => 'required',
                'startDate' => 'required|date',
                'endDate' => 'required|date',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            /**
             * @var Plan $plan
             */
            $plan = Plan::query()->find($id);
            if (!$plan)
            {
                throw new Exception("Plan with id {$id} not found!");
            }
            $startDate = Carbon::parse($request->get('startDate'));
            $endDate = Carbon::parse($request->get('endDate'));
            $frequency = $request->get('frequency');
            $oldStartDate = $plan->start_date;
            $oldEndDate = $plan->end_date;
            $oldFrequency = $plan->frequency;

            DB::beginTransaction();
            $plan->update([
                'name' => $request->get('name'),
                'theme' => $request->get('theme'),
                'frequency' => $frequency,
                'updated_by' => $request->get('userId'),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'mission' => $request->get('mission'),
                'vision' => $request->get('vision'),
                'values' => $request->get('values'),
            ]);

            if ($oldFrequency != $frequency || $oldStartDate->toDateString() != $startDate->toDateString() || $oldEndDate->toDateString() != $endDate->toDateString())
            {
                $plan->createReportPeriods();
            }
            DB::commit();
            return response()->json("Plan Updated!");
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
