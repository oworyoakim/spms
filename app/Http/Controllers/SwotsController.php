<?php

namespace App\Http\Controllers;

use App\Models\Swot;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SwotsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Swot::query();
            $categoryId = $request->get('categoryId');
            if ($categoryId)
            {
                $builder->where('category_id', $categoryId);
            }

            $planId = $request->get('planId');
            if ($planId)
            {
                $builder->where('plan_id', $planId);
            }

            $type = $request->get('type');
            if ($type)
            {
                $builder->where('type', $type);
            }

            $swots = $builder->get()->map(function (Swot $swot) {
                return $swot->getDetails();
            });
            return response()->json($swots);
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
                'type' => 'required',
                'planId' => 'required',
                'categoryId' => 'required',
                'userId' => 'required',
            ]);
            Swot::query()->create([
                'name' => $request->get('name'),
                'plan_id' => $request->get('planId'),
                'category_id' => $request->get('categoryId'),
                'created_by' => $request->get('userId'),
                'type' => $request->get('type'),
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
            $request->validate([
                'id' => 'required',
                'name' => 'required',
                'type' => 'required',
                'categoryId' => 'required',
                'userId' => 'required',
            ]);
            $id = $request->get('id');
            $swot = Swot::query()->find($id);
            if (!$swot)
            {
                throw new Exception("Swot with id {$id} not found!");
            }
            $swot->update([
                'name' => $request->get('name'),
                'category_id' => $request->get('categoryId'),
                'type' => $request->get('type'),
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
