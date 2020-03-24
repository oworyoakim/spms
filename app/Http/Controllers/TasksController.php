<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class TasksController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Task::query();
            $activityId = $request->get('activityId');
            if ($activityId)
            {
                $builder->where('activity_id', $activityId);
            }
            $status = $request->get('status');
            if ($status)
            {
                $builder->where('status', $status);
            }

            $tasks = $builder->get()->map(function (Task $task) {
                return $task->getDetails();
            });
            return response()->json($tasks);
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
                'activityId' => 'required',
                'userId' => 'required',
            ]);
            Task::query()->create([
                'title' => $request->get('title'),
                'start_date' => Carbon::parse($request->get('start_date')),
                'due_date' => Carbon::parse($request->get('due_date')),
                'activity_id' => $request->get('activityId'),
                'description' => $request->get('description'),
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Task created!");
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
                'activityId' => 'required',
                'userId' => 'required',
            ]);
            $id = $request->get('id');
            $task = Task::query()->find($id);
            if (!$task)
            {
                throw new Exception("Task with id {$id} not found!");
            }
            $task->update([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'start_date' => Carbon::parse($request->get('start_date')),
                'due_date' => Carbon::parse($request->get('due_date')),
                'activity_id' => $request->get('activityId'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Task updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
