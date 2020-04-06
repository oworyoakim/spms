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
            $rules = [
                'name' => 'required',
                'outcomeId' => 'required',
                'keyResultAreaId' => 'required',
                'unit' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            OutcomeIndicator::query()->create([
                'name' => $request->get('name'),
                'key_result_area_id' => $request->get('keyResultAreaId'),
                'outcome_id' => $request->get('outcomeId'),
                'description' => $request->get('description'),
                'unit' => $request->get('unit'),
                'baseline' => $request->get('baseline'),
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
            $rules = [
                'id' => 'required',
                'name' => 'required',
                'unit' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $outcomeIndicator = OutcomeIndicator::query()->find($id);
            if (!$outcomeIndicator)
            {
                throw new Exception("Outcome indicator with id {$id} not found!");
            }
            $outcomeIndicator->update([
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'unit' => $request->get('unit'),
                'baseline' => $request->get('baseline'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Outcome indicator updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
