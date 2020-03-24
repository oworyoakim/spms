<?php

namespace App\Http\Controllers;

use App\Models\Swot;
use App\Models\SwotCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SwotCategoriesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = SwotCategory::query();
            $swotCategories = $builder->get()->map(function (SwotCategory $swotCategory) {
                return $swotCategory->getDetails();
            });
            return response()->json($swotCategories);
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
                'userId' => 'required',
            ]);
            SwotCategory::query()->create([
                'name' => $request->get('name'),
                'created_by' => $request->get('userId'),
                'description' => $request->get('description'),
            ]);
            return response()->json("Swot category created!");
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
                'userId' => 'required',
            ]);
            $id = $request->get('id');
            $swotCategory = SwotCategory::query()->find($id);
            if (!$swotCategory)
            {
                throw new Exception("Swot category with id {$id} not found!");
            }
            $swotCategory->update([
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Swot category updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
