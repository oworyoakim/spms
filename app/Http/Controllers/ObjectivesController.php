<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\Objective;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class ObjectivesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Objective::query();
            $planId = $request->get('planId');
            if (!$planId)
            {
                throw new Exception("Strategic plan id required!");
            }
            $builder->where('plan_id', $planId);
            $objectives = $builder->get()->map(function (Objective $objective) {
                return $objective->getDetails();
            });
            return response()->json($objectives);
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
                'dueDate' => 'required|date',
                'rank' => 'required',
                'planId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            Objective::query()->create([
                'name' => $request->get('name'),
                'due_date' => Carbon::parse($request->get('dueDate')),
                'rank' => $request->get('rank'),
                'plan_id' => $request->get('planId'),
                'description' => $request->get('description'),
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Strategic objective created!");
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
                'name' => 'required',
                'dueDate' => 'required|date',
                'rank' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $objective = Objective::query()->find($id);
            if (!$objective)
            {
                throw new Exception("Strategic objective with id {$id} not found!");
            }
            $objective->update([
                'name' => $request->get('name'),
                'due_date' => Carbon::parse($request->get('dueDate')),
                'rank' => $request->get('rank'),
                'description' => $request->get('description'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Strategic objective updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function show(Request $request)
    {
        try
        {
            $builder = Objective::query();
            $objectiveId = $request->get('objectiveId');
            if (!$objectiveId)
            {
                throw new Exception("Strategic objective id required!");
            }
            $obj = $builder->find($objectiveId);
            if (!$obj)
            {
                throw new Exception("Strategic objective not found!");
            }
            $objective = $obj->getDetails();
            $objective->interventions = $obj->interventions()
                                             ->get()
                                             ->map(function (Intervention $intervention) {
                                                 return $intervention->getDetails();
                                             });
            return response()->json($objective);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
