<?php

namespace App\Http\Controllers;

use App\Models\Swot;
use App\Models\SwotCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use stdClass;

class SwotsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $planId = $request->get('planId');
            if (!$planId)
            {
                throw new Exception("Strategic plan id required!");
            }
            $builder = Swot::query();
            $builder->where('plan_id', $planId);
            $swots = $builder->get()->map(function (Swot $swot) {
                return $swot->getDetails();
            });

            $data = new stdClass();
            $data->{Swot::TYPE_STRENGTHS} = null;
            $data->{Swot::TYPE_WEAKNESSES} = null;
            $data->{Swot::TYPE_OPPORTUNITIES} = null;
            $data->{Swot::TYPE_THREATS} = null;

            if($item = $swots->firstWhere('type',Swot::TYPE_STRENGTHS)){
                $data->{Swot::TYPE_STRENGTHS} = $item;
            }
            if($item = $swots->firstWhere('type',Swot::TYPE_WEAKNESSES)){
                $data->{Swot::TYPE_WEAKNESSES} = $item;
            }
            if($item = $swots->firstWhere('type',Swot::TYPE_OPPORTUNITIES)){
                $data->{Swot::TYPE_OPPORTUNITIES} = $item;
            }

            if($item = $swots->firstWhere('type',Swot::TYPE_THREATS)){
                $data->{Swot::TYPE_THREATS} = $item;
            }
            return response()->json($data);
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
                'type' => 'required',
                'description' => 'required',
                'planId' => 'required',
                'userId' => 'required',
            ];
            $planId = $request->get('planId');
            $type = $request->get('type');
            $this->validateData($request->all(), $rules);
            $someSwot = Swot::query()->where('type', $type)->where('plan_id', $planId)->first();
            if ($someSwot)
            {
                return response()->json("Swot of type {$type} already exists for this strategic plan!", Response::HTTP_FORBIDDEN);
            }
            Swot::query()->create([
                'plan_id' => $planId,
                'created_by' => $request->get('userId'),
                'type' => $type,
                'description' => $request->get('description'),
            ]);
            return response()->json("Swot Created!");
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
                'description' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $swot = Swot::query()->find($id);
            if (!$swot)
            {
                throw new Exception("Swot with id {$id} not found!");
            }
            $swot->update([
                'description' => $request->get('description'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Swot Updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
