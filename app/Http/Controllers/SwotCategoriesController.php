<?php

namespace App\Http\Controllers;

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
            $rules = [
                'name' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
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
            $rules = [
                'id' => 'required',
                'name' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
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
