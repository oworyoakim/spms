<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InterventionsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Intervention::query();
            $objectiveId = $request->get('objectiveId');
            if ($objectiveId)
            {
                $builder->where('objective_id', $objectiveId);
            }
            $interventions = $builder->get()->map(function (Intervention $intervention) {
                return $intervention->getDetails();
            });
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
            $request->validate([
                'name' => 'required',
                'rank' => 'required',
                'objectiveId' => 'required',
                'userId' => 'required',
            ]);
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
            $request->validate([
                'id' => 'required',
                'name' => 'required',
                'rank' => 'required',
                'objectiveId' => 'required',
                'userId' => 'required',
            ]);
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
