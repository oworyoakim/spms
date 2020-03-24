<?php

namespace App\Http\Controllers;

use App\Models\KeyResultArea;
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
            $keyResultAreas = $builder->get()->map(function (KeyResultArea $keyResultArea) {
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
            $request->validate([
                'name' => 'required',
                'dueDate' => 'required|date',
                'rank' => 'required',
                'planId' => 'required',
                'userId' => 'required',
            ]);
            KeyResultArea::query()->create([
                'name' => $request->get('name'),
                'due_date' => Carbon::parse($request->get('dueDate')),
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
            $request->validate([
                'id' => 'required',
                'name' => 'required',
                'dueDate' => 'required|date',
                'rank' => 'required',
                'userId' => 'required',
            ]);
            $id = $request->get('id');
            $keyResultArea = KeyResultArea::query()->find($id);
            if (!$keyResultArea)
            {
                throw new Exception("Key result area with id {$id} not found!");
            }
            $keyResultArea->update([
                'name' => $request->get('name'),
                'due_date' => Carbon::parse($request->get('dueDate')),
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
}
