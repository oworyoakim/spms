<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ActivitiesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Activity::query();
            $interventionId = $request->get('interventionId');
            $workPlanId = $request->get('workPlanId');
            if (!$workPlanId || !$interventionId)
            {
                throw new Exception("Intervention ID or Work Plan ID required!");
            }
            if ($workPlanId)
            {
                $builder->where('work_plan_id', $workPlanId);
            }

            if ($interventionId)
            {
                $builder->where('intervention_id', $interventionId);
            }

            $status = $request->get('status');
            if ($status)
            {
                $builder->where('status', $status);
            }

            $activities = $builder->get()->map(function (Activity $activity) {
                return $activity->getDetails();
            });
            return response()->json($activities);
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
                'dueDate' => 'required|date',
                'workPlanId' => 'required',
                'interventionId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            Activity::query()->create([
                'title' => $request->get('title'),
                'due_date' => Carbon::parse($request->get('dueDate')),
                'work_plan_id' => $request->get('workPlanId'),
                'intervention_id' => $request->get('interventionId'),
                'description' => $request->get('description'),
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Activity created!");
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
                'dueDate' => 'required|date',
                'interventionId' => 'required',
                'workPlanId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $activity = Activity::query()->find($id);
            if (!$activity)
            {
                throw new Exception("Activity with id {$id} not found!");
            }
            $activity->update([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'due_date' => Carbon::parse($request->get('dueDate')),
                'work_plan_id' => $request->get('workPlanId'),
                'intervention_id' => $request->get('interventionId'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Activity updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function hold(Request $request)
    {
        try
        {
            $rules = [
                'id' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);

            $id = $request->get('id');
            $userId = $request->get('userId');

            $activity = Activity::query()->where('status','ongoing')->find($id);
            if (!$activity)
            {
                throw new Exception("Activity with id {$id} not found!");
            }
            DB::beginTransaction();
            $activity->hold($userId);
            DB::commit();
            return response()->json("Activity updated!");
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function unhold(Request $request)
    {
        try
        {
            $rules = [
                'id' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);

            $id = $request->get('id');
            $userId = $request->get('userId');

            $activity = Activity::query()->where('status','onhold')->find($id);
            if (!$activity)
            {
                throw new Exception("Activity with id {$id} not found!");
            }
            DB::beginTransaction();
            $activity->unhold($userId);
            DB::commit();
            return response()->json("Activity updated!");
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function complete(Request $request)
    {
        try
        {
            //TODO: implement activity completion
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
