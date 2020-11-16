<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\Objective;
use App\Models\Output;
use App\Models\OutputAchievement;
use App\Models\OutputIndicator;
use App\Models\OutputIndicatorTarget;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use stdClass;

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
                'rank' => 'required',
                'planId' => 'required',
                'keyResultAreaId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            Objective::query()->create([
                'name' => $request->get('name'),
                'rank' => $request->get('rank'),
                'plan_id' => $request->get('planId'),
                'key_result_area_id' => $request->get('keyResultAreaId'),
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
                'rank' => 'required',
                'keyResultAreaId' => 'required',
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
                'rank' => $request->get('rank'),
                'description' => $request->get('description'),
                'key_result_area_id' => $request->get('keyResultAreaId'),
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
            $objectiveId = $request->get('objectiveId');
            if (!$objectiveId)
            {
                throw new Exception("Strategic objective id required!");
            }
            $obj = Objective::query()->find($objectiveId);
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
            $objective->outputs = $obj->outputs()
                                      ->get()
                                      ->map(function (Output $output) {
                                          return $output->getDetails();
                                      });
            $objective->indicators = $obj->indicators()
                                         ->get()
                                         ->map(function (OutputIndicator $indicator) {
                                             return $indicator->getDetails();
                                         });
            $objective->targets = $obj->targets()
                                      ->get()
                                      ->map(function (OutputIndicatorTarget $target) {
                                          return $target->getDetails();
                                      });
            $objective->achievements = $obj->achievements()
                                           ->get()
                                           ->map(function (OutputAchievement $achievement) {
                                               return $achievement->getDetails();
                                           });
            return response()->json($objective);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

}
