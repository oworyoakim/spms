<?php

namespace App\Http\Controllers;


use App\Models\DirectiveResolutionOutput;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DirAndResActivityOutputsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = DirectiveResolutionOutput::query();
            $dirAndResActivityId = $request->get('dirAndResActivityId');
            if (!$dirAndResActivityId)
            {
                throw new Exception("Activity id required!");
            }
            $builder->where('directive_resolution_activity_id', $dirAndResActivityId);
            if ($directiveResolutionId = $request->get('directiveAndResolutionId'))
            {
                $builder->where('directive_resolution_id', $directiveResolutionId);
            }
            $outputs = $builder->get()->map(function (DirectiveResolutionOutput $output) {
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
            $rules = [
                'title' => 'required',
                'dirAndResActivityId' => 'required',
                'directiveAndResolutionId' => 'required',
                'target' => 'required',
                'unit' => 'required',
                'responsiblePerson' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);

            DirectiveResolutionOutput::query()->create([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'directive_resolution_activity_id' => $request->get('dirAndResActivityId'),
                'directive_resolution_id' => $request->get('directiveAndResolutionId'),
                'target' => $request->get('target'),
                'unit' => $request->get('unit'),
                'responsible_person' => $request->get('responsiblePerson'),
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
            $rules = [
                'id' => 'required',
                'dirAndResActivityId' => 'required',
                'directiveAndResolutionId' => 'required',
                'target' => 'required',
                'unit' => 'required',
                'responsiblePerson' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $output = DirectiveResolutionOutput::query()->find($id);
            if (!$output)
            {
                throw new Exception("Activity output with id {$id} not found!");
            }

            if ($title = $request->get('title'))
            {
                $output->title = $title;
            }
            if ($description = $request->get('description'))
            {
                $output->description = $description;
            }

            if ($dirAndResActivityId = $request->get('dirAndResActivityId'))
            {
                $output->directive_resolution_activity_id = $dirAndResActivityId;
            }

            if ($target = $request->get('target'))
            {
                $output->target = $target;
            }
            if ($unit = $request->get('unit'))
            {
                $output->unit = $unit;
            }

            if ($responsiblePerson = $request->get('responsiblePerson'))
            {
                $output->responsible_person = $responsiblePerson;
            }

            $output->updated_by = $request->get('userId');

            $output->save();
            return response()->json("Activity output updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

}
