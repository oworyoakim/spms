<?php

namespace App\Http\Controllers;

use App\Models\OutcomeIndicator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OutcomeIndicatorsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = OutcomeIndicator::query();
            $outcomeId = $request->get('outcome_id');
            if ($outcomeId)
            {
                $builder->where('outcome_id', $outcomeId);
            }
            $outcomeIndicators = $builder->get()->map(function (OutcomeIndicator $indicator) {
                return $indicator->getDetails();
            });
            return response()->json($outcomeIndicators);
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
                'name' => 'required',
                'outcomeId' => 'required',
                'unit' => 'required',
                'userId' => 'required',
            ]);
            OutcomeIndicator::query()->create([
                'name' => $request->get('name'),
                'outcome_id' => $request->get('outcomeId'),
                'description' => $request->get('description'),
                'unit' => $request->get('unit'),
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Outcome indicator created!");
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
                'name' => 'required',
                'outcomeId' => 'required',
                'unit' => 'required',
                'userId' => 'required',
            ]);
            $id = $request->get('id');
            $outcomeIndicator = OutcomeIndicator::query()->find($id);
            if (!$outcomeIndicator)
            {
                throw new Exception("Outcome indicator with id {$id} not found!");
            }
            $outcomeIndicator->update([
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'outcome_id' => $request->get('outcomeId'),
                'unit' => $request->get('unit'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Outcome indicator updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
