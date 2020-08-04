<?php

namespace App\Http\Controllers;


use App\Models\DirectiveResolution;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DirectivesAndResolutionsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = DirectiveResolution::query();
            $workPlanId = $request->get('workPlanId');
            if (!$workPlanId)
            {
                throw new Exception("Work plan id required!");
            }
            $builder->where('work_plan_id', $workPlanId);
            $directivesAndResolutions = $builder->get()->map(function (DirectiveResolution $directiveResolution) {
                return $directiveResolution->getDetails();
            });
            return response()->json($directivesAndResolutions);
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
                'description' => 'required',
                'workPlanId' => 'required',
                'responsibilityCentreId' => 'required',
                'type' => 'required',
                'sourceType' => 'required',
                'dateReceived' => 'required',
                'deadline' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $type = $request->get('type');
            DirectiveResolution::query()->create([
                'title' => $request->get('title'),
                'type' => $type,
                'description' => $request->get('description'),
                'work_plan_id' => $request->get('workPlanId'),
                'responsibility_centre' => $request->get('responsibilityCentreId'),
                'date_received' => Carbon::parse($request->get('dateReceived')),
                'deadline' => Carbon::parse($request->get('deadline')),
                'source_type' => $request->get('sourceType'),
                'source_organization' => $request->get('sourceOrganization'),
                'source_office' => $request->get('sourceOffice'),
                'source_officer' => $request->get('sourceOfficer'),
                'source_telephone' => $request->get('sourceTelephone'),
                'source_email' => $request->get('sourceEmail'),
                'created_by' => $request->get('userId'),
            ]);
            return response()->json(ucfirst($type) ." created!");
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
                'title' => 'required',
                'description' => 'required',
                'responsibilityCentreId' => 'required',
                'type' => 'required',
                'sourceType' => 'required',
                'dateReceived' => 'required',
                'deadline' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $directiveResolution = DirectiveResolution::query()->find($id);
            if (!$directiveResolution)
            {
                throw new Exception("Directive or resolution with id {$id} not found!");
            }
            $type = $request->get('type');
            $directiveResolution->update([
                'title' => $request->get('title'),
                'type' => $type,
                'description' => $request->get('description'),
                'responsibility_centre' => $request->get('responsibilityCentreId'),
                'date_received' => Carbon::parse($request->get('dateReceived')),
                'deadline' => Carbon::parse($request->get('deadline')),
                'source_type' => $request->get('sourceType'),
                'source_organization' => $request->get('sourceOrganization'),
                'source_office' => $request->get('sourceOffice'),
                'source_officer' => $request->get('sourceOfficer'),
                'source_telephone' => $request->get('sourceTelephone'),
                'source_email' => $request->get('sourceEmail'),
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json(ucfirst($type) ." updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
    
}
