<?php

namespace App\Http\Controllers;

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
            $outputId = $request->get('output_id');
            if ($outputId)
            {
                $builder->where('output_id', $outputId);
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
                'objectiveId' => 'required',
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
