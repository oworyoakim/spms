<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Stage;
use App\Models\Task;
use App\Models\WorkPlan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class WorkPlansController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = WorkPlan::query();
            $planId = $request->get('planId');
            if (!$planId)
            {
                throw new Exception("Strategic Plan ID required!");
            }
            $builder->where('plan_id', $planId);

            $status = $request->get('status');
            if ($status)
            {
                $builder->where('status', $status);
            }

            $workPlans = $builder->get()->map(function (WorkPlan $plan) {
                $workPlan = $plan->getDetails();
                $workPlan->activities = $plan->activities()->get()->map(function (Activity $activity) {
                    return $activity->getDetails();
                });
                $workPlan->stages = Stage::query()->where('work_plan_id', $plan->id)->get()->map(function (Stage $stage) {
                    return $stage->getDetails();
                });
                $workPlan->tasks = Task::query()->where('work_plan_id', $plan->id)->get()->map(function (Task $task) {
                    return $task->getDetails();
                });

                return $workPlan;
            });
            return response()->json($workPlans);
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
                'title' => 'required',
                'financialYear' => 'required',
                'planningDeadline' => 'required',
                'planId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $financialYear = $request->get('financialYear');
            $years = explode('/', $financialYear);
            $deadline = $request->get('planningDeadline');

            WorkPlan::query()->create([
                'title' => $request->get('title'),
                'financial_year' => $financialYear,
                'start_date' => Carbon::parse("{$years[0]}-07-01"),
                'end_date' => Carbon::parse("{$years[1]}-06-30"),
                'planning_deadline' => Carbon::parse($deadline),
                'plan_id' => $request->get('planId'),
                'theme' => $request->get('theme'),
                'description' => $request->get('description'),
                'created_by' => $request->get('userId'),
            ]);

            return response()->json("Work plan created!");
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
                'title' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $workPlan = WorkPlan::query()->find($id);
            if (!$workPlan)
            {
                throw new Exception("Work plan with id {$id} not found!");
            }
            $deadline = $request->get('planningDeadline');
            if ($deadline && $deadline != $workPlan->planning_deadline)
            {
                $deadline = Carbon::parse($deadline);
                $workPlan->planning_deadline = $deadline;
            }

            $workPlan->title = $request->get('title');
            $workPlan->theme = $request->get('theme');
            $workPlan->description = $request->get('description');
            $workPlan->updated_by = $request->get('userId');
            $workPlan->save();
            return response()->json("Work plan updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
