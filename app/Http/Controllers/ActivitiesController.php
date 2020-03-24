<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class ActivitiesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Activity::query();
            $objectiveId = $request->get('objectiveId');
            if ($objectiveId)
            {
                $builder->where('objective_id', $objectiveId);
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
            $request->validate([
                'title' => 'required',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'objectiveId' => 'required',
                'userId' => 'required',
            ]);
            Activity::query()->create([
                'title' => $request->get('title'),
                'start_date' => Carbon::parse($request->get('start_date')),
                'due_date' => Carbon::parse($request->get('due_date')),
                'objective_id' => $request->get('objectiveId'),
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
            $request->validate([
                'id' => 'required',
                'title' => 'required',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'objectiveId' => 'required',
                'userId' => 'required',
            ]);
            $id = $request->get('id');
            $activity = Activity::query()->find($id);
            if (!$activity)
            {
                throw new Exception("Activity with id {$id} not found!");
            }
            $activity->update([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'start_date' => Carbon::parse($request->get('start_date')),
                'due_date' => Carbon::parse($request->get('due_date')),
                'objective_id' => $request->get('objectiveId'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Activity updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
