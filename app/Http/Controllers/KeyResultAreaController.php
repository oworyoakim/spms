<?php

namespace App\Http\Controllers;

use App\Models\KeyResultArea;
use App\Models\Outcome;
use App\Models\OutcomeAchievement;
use App\Models\OutcomeIndicator;
use App\Models\OutcomeIndicatorTarget;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class KeyResultAreaController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = KeyResultArea::query();
            $planId = $request->get('planId');
            if (!$planId)
            {
                throw new Exception("Strategic plan id required!");
            }
            $builder->where('plan_id', $planId);
            $keyResultAreas = $builder->get()
                                      ->map(function (KeyResultArea $keyResultArea) {
                                          return $keyResultArea->getDetails();
                                      });
            return response()->json($keyResultAreas);
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
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            KeyResultArea::query()->create([
                'name' => $request->get('name'),
                'rank' => $request->get('rank'),
                'plan_id' => $request->get('planId'),
                'description' => $request->get('description'),
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Key result area created!");
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
            $keyResultArea = KeyResultArea::query()->find($id);
            if (!$keyResultArea)
            {
                throw new Exception("Key result area with id {$id} not found!");
            }
            $keyResultArea->update([
                'name' => $request->get('name'),
                'rank' => $request->get('rank'),
                'description' => $request->get('description'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Key result area updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function show(Request $request)
    {
        try
        {
            $keyResultAreaId = $request->get('keyResultAreaId');
            if (!$keyResultAreaId)
            {
                throw new Exception("Key result area id required!");
            }
            $kra = KeyResultArea::query()->find($keyResultAreaId);
            if (!$kra)
            {
                throw new Exception("Key result area not found!");
            }
            $keyResultArea = $kra->getDetails();

            $keyResultArea->outcomes = $kra->outcomes()
                                      ->get()
                                      ->map(function (Outcome $outcome) {
                                          return $outcome->getDetails();
                                      });

            $keyResultArea->indicators = $kra->indicators()
                                         ->get()
                                         ->map(function (OutcomeIndicator $indicator) {
                                             return $indicator->getDetails();
                                         });
            $keyResultArea->targets = $kra->targets()
                                      ->get()
                                      ->map(function (OutcomeIndicatorTarget $target) {
                                          return $target->getDetails();
                                      });
            $keyResultArea->achievements = $kra->achievements()
                                           ->get()
                                           ->map(function (OutcomeAchievement $achievement) {
                                               return $achievement->getDetails();
                                           });
            return response()->json($keyResultArea);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
