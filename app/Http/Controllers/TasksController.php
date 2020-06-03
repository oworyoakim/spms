<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TasksController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Task::query();
            $stageId = $request->get('stageId');
            $activityId = $request->get('activityId');
            if (!$activityId || !$stageId)
            {
               throw new Exception("Activity ID or Stage ID required!");
            }
            if ($activityId)
            {
                $builder->where('activity_id', $activityId);
            }
            if ($stageId)
            {
                $builder->where('stage_id', $stageId);
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
            $rules = [
                'title' => 'required',
                'dueDate' => 'required|date',
                'workPlanId' => 'required',
                'activityId' => 'required',
                'stageId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            Task::query()->create([
                'title' => $request->get('title'),
                'due_date' => Carbon::parse($request->get('dueDate')),
                'work_plan_id' => $request->get('workPlanId'),
                'activity_id' => $request->get('activityId'),
                'stage_id' => $request->get('stageId'),
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
            $rules = [
                'id' => 'required',
                'title' => 'required',
                'dueDate' => 'required|date',
                'stageId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $task = Task::query()->find($id);
            if (!$task)
            {
                throw new Exception("Task with id {$id} not found!");
            }
            $task->update([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'due_date' => Carbon::parse($request->get('dueDate')),
                'stage_id' => $request->get('stageId'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Task updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function start(Request $request)
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
            $task = Task::query()->find($id);
            if (!$task)
            {
                throw new Exception("Task with id {$id} not found!");
            }

            $activityStatus = $task->activity->status;
            if(!in_array($activityStatus, ['approved','ongoing'])){
                $msg = ($activityStatus == 'submitted') ? "not yet approved" : $activityStatus;
                throw new Exception("Activity for this task is {$msg}!");
            }
            DB::beginTransaction();
            $task->start($userId);
            $task->stage->start($userId);
            $task->activity->start($userId);
            DB::commit();
            return response()->json("Task started!");
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
            $rules = [
                'id' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $userId = $request->get('userId');
            $task = Task::query()->find($id);
            if (!$task)
            {
                throw new Exception("Task with id {$id} not found!");
            }
            DB::beginTransaction();
            $task->complete($userId);
            $task->stage->updateStatus($userId);
            //$task->activity->updateStatus($userId);
            DB::commit();
            return response()->json("Task completed!");
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
