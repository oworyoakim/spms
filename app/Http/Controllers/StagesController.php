<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class StagesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Stage::query();
            $activityId = $request->get('activityId');
            if (!$activityId)
            {
                throw new Exception("Activity ID required!");
            }
            $builder->where('activity_id', $activityId);
            $status = $request->get('status');
            if ($status)
            {
                $builder->where('status', $status);
            }

            $stages = $builder->get()->map(function (Stage $stage) {
                return $stage->getDetails();
            });
            return response()->json($stages);
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
                'startDate' => 'required|date',
                'dueDate' => 'required|date',
                'workPlanId' => 'required',
                'activityId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            Stage::query()->create([
                'title' => $request->get('title'),
                'start_date' => Carbon::parse($request->get('startDate')),
                'due_date' => Carbon::parse($request->get('dueDate')),
                'work_plan_id' => $request->get('workPlanId'),
                'activity_id' => $request->get('activityId'),
                'description' => $request->get('description'),
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Stage created!");
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
                'startDate' => 'required|date',
                'dueDate' => 'required|date',
                'activityId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $stage = Stage::query()->find($id);
            if (!$stage)
            {
                throw new Exception("Stage with id {$id} not found!");
            }
            $stage->update([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'start_date' => Carbon::parse($request->get('startDate')),
                'due_date' => Carbon::parse($request->get('dueDate')),
                'activity_id' => $request->get('activityId'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Stage updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
