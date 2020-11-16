<?php

namespace App\Http\Controllers;

use App\Models\Output;
use App\Models\OutputIndicator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OutputIndicatorsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = OutputIndicator::query();
            $objectiveId = $request->get('objectiveId');
            $interventionId = $request->get('interventionId');
            $activityId = $request->get('activityId');
            $outputId = $request->get('outputId');
            if (!empty($objectiveId))
            {
                $outputIds = Output::query()->where('objective_id', $objectiveId)->pluck('id')->all();
                $builder->whereIn('output_id', $outputIds);
            } elseif (!empty($interventionId))
            {
                $outputIds = Output::query()->where('intervention_id', $interventionId)->pluck('id')->all();
                $builder->whereIn('output_id', $outputIds);
            } elseif (!empty($activityId))
            {
                $outputIds = Output::query()->where('activity_id', $activityId)->pluck('id')->all();
                $builder->whereIn('output_id', $outputIds);
            } elseif (!empty($outputId))
            {
                $builder->where('output_id', $outputId);
            } else
            {
                throw new Exception("Strategic objective ID, Intervention ID, Activity ID, or Output ID required!");
            }

            $outputIndicators = $builder->get()->map(function (OutputIndicator $indicator) {
                return $indicator->getDetails();
            });
            return response()->json($outputIndicators);
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
                'outputId' => 'required',
                'unit' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            OutputIndicator::query()->create([
                'name' => $request->get('name'),
                'output_id' => $request->get('outputId'),
                'objective_id' => $request->get('objectiveId'),
                'description' => $request->get('description'),
                'unit' => $request->get('unit'),
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Output indicator created!");
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
                'outputId' => 'required',
                'unit' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $outputIndicator = OutputIndicator::query()->find($id);
            if (!$outputIndicator)
            {
                throw new Exception("Output indicator with id {$id} not found!");
            }
            $outputIndicator->update([
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'output_id' => $request->get('outputId'),
                'unit' => $request->get('unit'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Output indicator updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
