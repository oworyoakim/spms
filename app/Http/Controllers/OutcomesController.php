<?php

namespace App\Http\Controllers;

use App\Models\Outcome;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OutcomesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Outcome::query();
            $keyResultAreaId = $request->get('keyResultAreaId');
            if ($keyResultAreaId)
            {
                $builder->where('key_result_area_id', $keyResultAreaId);
            }
            $outcomes = $builder->get()->map(function (Outcome $outcome) {
                return $outcome->getDetails();
            });
            return response()->json($outcomes);
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
                'keyResultAreaId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            Outcome::query()->create([
                'name' => $request->get('name'),
                'key_result_area_id' => $request->get('keyResultAreaId'),
                'description' => $request->get('description'),
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Outcome created!");
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
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $outcome = Outcome::query()->find($id);
            if (!$outcome)
            {
                throw new Exception("Outcome with id {$id} not found!");
            }
            $outcome->update([
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Outcome updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
