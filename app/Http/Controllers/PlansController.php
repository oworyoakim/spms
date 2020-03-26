<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class PlansController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Plan::query();
            $plans = $builder->get()->map(function (Plan $plan) {
                return $plan->getDetails();
            });
            return response()->json($plans);
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
                'theme' => 'required',
                'frequency' => 'required',
                'startDate' => 'required|date',
                'endDate' => 'required|date',
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
            Plan::query()->create([
                'name' => $request->get('name'),
                'theme' => $request->get('theme'),
                'frequency' => $request->get('frequency'),
                'start_date' => Carbon::parse($request->get('startDate')),
                'end_date' => Carbon::parse($request->get('endDate')),
                'mission' => $request->get('mission'),
                'vision' => $request->get('vision'),
                'values' => $request->get('values'),
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Plan Created!");
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
                'theme' => 'required',
                'frequency' => 'required',
                'startDate' => 'required|date',
                'endDate' => 'required|date',
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
            $plan = Plan::query()->find($id);
            if (!$plan)
            {
                throw new Exception("Plan with id {$id} not found!");
            }
            $plan->update([
                'name' => $request->get('name'),
                'theme' => $request->get('theme'),
                'frequency' => $request->get('frequency'),
                'updated_by' => $request->get('userId'),
                'start_date' => Carbon::parse($request->get('startDate')),
                'end_date' => Carbon::parse($request->get('endDate')),
                'mission' => $request->get('mission'),
                'vision' => $request->get('vision'),
                'values' => $request->get('values'),
            ]);
            return response()->json("Plan Updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
