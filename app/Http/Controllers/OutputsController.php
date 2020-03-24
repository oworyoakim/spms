<?php

namespace App\Http\Controllers;

use App\Models\Output;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class OutputsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Output::query();
            $interventionId = $request->get('interventionId');
            if ($interventionId)
            {
                $builder->where('intervention_id', $interventionId);
            }
            $outputs = $builder->get()->map(function (Output $output) {
                return $output->getDetails();
            });
            return response()->json($outputs);
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
                'interventionId' => 'required',
                'userId' => 'required',
            ]);
            Output::query()->create([
                'name' => $request->get('name'),
                'intervention_id' => $request->get('interventionId'),
                'description' => $request->get('description'),
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Output created!");
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
                'interventionId' => 'required',
            ]);
            $id = $request->get('id');
            $output = Output::query()->find($id);
            if (!$output)
            {
                throw new Exception("Output with id {$id} not found!");
            }
            $output->update([
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'intervention_id' => $request->get('interventionId'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Output updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
