<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\Objective;
use App\Models\Plan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class InterventionsController extends Controller
{
    public function index(Request $request)
    {
        try
        {

            $planId = $request->get('planId');
            if ($planId)
            {
                $plan = Plan::query()->find($planId);
                if (!$plan)
                {
                    throw new Exception("Strategic plan not found!");
                }
                $interventions = Collection::make();
                foreach ($plan->objectives()->get() as $objective)
                {
                    foreach ($objective->interventions()->get() as $intervention)
                    {
                        $interventions->push($intervention->getDetails());
                    }
                }
            } else
            {
                $builder = Intervention::query();
                $objectiveId = $request->get('objectiveId');
                if (!$objectiveId)
                {
                    throw new Exception("Objective ID required!");
                }
                $builder->where('objective_id', $objectiveId);
                $interventions = $builder->get()->map(function (Intervention $intervention) {
                    return $intervention->getDetails();
                });
            }
            return response()->json($interventions);
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
                'rank' => 'required',
                'objectiveId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            Intervention::query()->create([
                'name' => $request->get('name'),
                'rank' => $request->get('rank'),
                'objective_id' => $request->get('objectiveId'),
                'description' => $request->get('description'),
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Intervention Created!");
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
                'rank' => 'required',
                'objectiveId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $intervention = Intervention::query()->find($id);
            if (!$intervention)
            {
                throw new Exception("Intervention with id {$id} not found!");
            }
            $intervention->update([
                'name' => $request->get('name'),
                'rank' => $request->get('rank'),
                'description' => $request->get('description'),
                'objective_id' => $request->get('objectiveId'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Intervention Updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
