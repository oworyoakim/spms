<?php

namespace App\Http\Controllers;


use App\Models\DirectiveResolution;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DirectivesAndResolutionsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = DirectiveResolution::query();
            $workPlanId = $request->get('workPlanId');
            if (!$workPlanId)
            {
                throw new Exception("Work plan id required!");
            }
            $builder->where('work_plan_id', $workPlanId);
            $directivesAndResolutions = $builder->get()->map(function (DirectiveResolution $directiveResolution) {
                return $directiveResolution->getDetails();
            });
            return response()->json($directivesAndResolutions);
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
                'rank' => 'required',
                'workPlanId' => 'required',
                'responsibilityCentre' => 'required',
                'type' => 'required',
                'sourceType' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            Objective::query()->create([
                'name' => $request->get('name'),
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
