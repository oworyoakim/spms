<?php

namespace App\Http\Controllers;

use App\Models\Objective;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class ObjectivesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Objective::query();
            $planId = $request->get('planId');
            if (!$planId)
            {
                throw new Exception("Strategic plan id required!");
            }
            $builder->where('plan_id', $planId);
            $objectives = $builder->get()->map(function (Objective $objective) {
                return $objective->getDetails();
            });
            return response()->json($objectives);
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
                'dueDate' => 'required|date',
                'rank' => 'required',
                'planId' => 'required',
                'userId' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
            {
                $errors = Collection::make();
                foreach ($validator->errors()->messages() as $key => $messages)
                {
                    $field = ucfirst($key);
                    $message = implode(', ', $messages);
                    $error = "{$field}: {$message}";
                    $errors->push($error);
                }
                throw new Exception($errors->implode('<br>'));
            }
            Objective::query()->create([
                'name' => $request->get('name'),
                'due_date' => Carbon::parse($request->get('dueDate')),
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
                'dueDate' => 'required|date',
                'rank' => 'required',
                'userId' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
            {
                $errors = Collection::make();
                foreach ($validator->errors()->messages() as $key => $messages)
                {
                    $field = ucfirst($key);
                    $message = implode(', ', $messages);
                    $error = "{$field}: {$message}";
                    $errors->push($error);
                }
                throw new Exception($errors->implode('<br>'));
            }
            $id = $request->get('id');
            $objective = Objective::query()->find($id);
            if (!$objective)
            {
                throw new Exception("Strategic objective with id {$id} not found!");
            }
            $objective->update([
                'name' => $request->get('name'),
                'due_date' => Carbon::parse($request->get('dueDate')),
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
}
