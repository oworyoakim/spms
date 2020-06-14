<?php

namespace App\Http\Controllers;

use App\Models\Swot;
use App\Models\SwotCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

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
            $data = [];
            $data[Swot::TYPE_STRENGTHS] = [];
            $data[Swot::TYPE_WEAKNESSES] = [];
            $data[Swot::TYPE_OPPORTUNITIES] = [];
            $data[Swot::TYPE_THREATS] = [];

            foreach (SwotCategory::all() as $category){
                // STRENGTHS
                $strengths = $swots->filter(function ($swot) use ($category){
                    return $swot->type == Swot::TYPE_STRENGTHS && $swot->categoryId == $category->id;
                });
                $swotCategory = $category->getDetails();
                $swotCategory->swots = $strengths;
                if(count($swotCategory->swots)){
                    $data[Swot::TYPE_STRENGTHS][] = $swotCategory;
                }

                // WEAKNESSES
                $weaknesses = $swots->filter(function ($swot) use ($category){
                    return $swot->type == Swot::TYPE_WEAKNESSES && $swot->categoryId == $category->id;
                });
                $swotCategory = $category->getDetails();
                $swotCategory->swots = $weaknesses;
                if(count($swotCategory->swots)){
                    $data[Swot::TYPE_WEAKNESSES][] = $swotCategory;
                }

                // OPPORTUNITIES
                $opportunities = $swots->filter(function ($swot) use ($category){
                    return $swot->type == Swot::TYPE_OPPORTUNITIES && $swot->categoryId == $category->id;
                });
                $swotCategory = $category->getDetails();
                $swotCategory->swots = $opportunities;
                if(count($swotCategory->swots)){
                    $data[Swot::TYPE_OPPORTUNITIES][] = $swotCategory;
                }

                // THREATS
                $threats = $swots->filter(function ($swot) use ($category){
                    return $swot->type == Swot::TYPE_THREATS && $swot->categoryId == $category->id;
                });
                $swotCategory = $category->getDetails();
                $swotCategory->swots = $threats;
                if(count($swotCategory->swots)){
                    $data[Swot::TYPE_THREATS][] = $swotCategory;
                }
            }
            /*
            $categoryId = $request->get('categoryId');
            if ($categoryId)
            {
                $builder->where('category_id', $categoryId);
            }

            $type = $request->get('type');
            if ($type)
            {
                $builder->where('type', $type);
            }

            $swots = $builder->get()
                             ->map(function (Swot $swot) {
                                 return $swot->getDetails();
                             })
                             ->groupBy('type')
                             ->map(function (Collection $swots) {
                                 return $swots->groupBy('categoryId')->map(function (Swot $item){

                                 });
                             });
            */
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
                'name' => 'required',
                'type' => 'required',
                'planId' => 'required',
                'categoryId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
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
            $rules = [
                'id' => 'required',
                'name' => 'required',
                'type' => 'required',
                'categoryId' => 'required',
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
