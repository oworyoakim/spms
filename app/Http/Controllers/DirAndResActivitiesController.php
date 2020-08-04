<?php

namespace App\Http\Controllers;


use App\Models\Comment;
use App\Models\DirectiveResolutionActivity;
use App\Models\DirectiveResolutionOutput;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DirAndResActivitiesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = DirectiveResolutionActivity::query();
            $directiveResolutionId = $request->get('directiveAndResolutionId');
            if (!$directiveResolutionId)
            {
                throw new Exception("Directive and resolution id required!");
            }
            $builder->where('directive_resolution_id', $directiveResolutionId);
            if ($workPlanId = $request->get('workPlanId'))
            {
                $builder->where('work_plan_id', $workPlanId);
            }
            $activities = $builder->get()->map(function (DirectiveResolutionActivity $activity) {
                return $activity->getDetails(true);
            });
            return response()->json($activities);
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
                'workPlanId' => 'required',
                'directiveAndResolutionId' => 'required',
                'dueDate' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);

            $startDate = $request->get('startDate');

            DirectiveResolutionActivity::query()->create([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'work_plan_id' => $request->get('workPlanId'),
                'directive_resolution_id' => $request->get('directiveAndResolutionId'),
                'due_date' => Carbon::parse($request->get('dueDate')),
                'start_date' => !empty($startDate) ? Carbon::parse($startDate) : null,
                'created_by' => $request->get('userId'),
            ]);
            return response()->json("Activity created!");
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
                'directiveAndResolutionId' => 'required',
                'dueDate' => 'required|date_format:Y-m-d',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $activity = DirectiveResolutionActivity::query()->find($id);
            if (!$activity)
            {
                throw new Exception("Activity with id {$id} not found!");
            }

            if($startDate = $request->get('startDate')){
                $activity->start_date = Carbon::parse($startDate);
            }

            if($title = $request->get('title')){
                $activity->title = $title;
            }
            if($description = $request->get('description')){
                $activity->description = $description;
            }

            if($directiveAndResolutionId = $request->get('directiveAndResolutionId')){
                $activity->directive_resolution_id = $directiveAndResolutionId;
            }

            $activity->updated_by = $request->get('userId');

            $activity->save();
            return response()->json("Activity updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function updateStatus(Request $request)
    {
        try
        {
            $rules = [
                'id' => 'required',
                'action' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $activity = DirectiveResolutionActivity::query()->find($id);
            if (!$activity)
            {
                throw new Exception("Activity with id {$id} not found!");
            }

            $action = $request->get('action');
            $status = null;
            $endDate = null;
            if ($action === 'hold')
            {
                if ($activity->status == 'onhold')
                {
                    $status = "ongoing";
                } else
                {
                    $status = "onhold";
                }

            } elseif ($action === 'approve')
            {
                $status = "approved";
            } elseif ($action === 'decline')
            {
                $status = "declined";
            } elseif ($action === 'start')
            {
                $status = "ongoing";
            } elseif ($action === 'complete')
            {
                $status = "completed";
                $endDate = Carbon::now();
            }
            if (!empty($status))
            {
                $activity->status = $status;
                $activity->end_date = $endDate;
                $activity->updated_by = $request->get('userId');
                $activity->save();
            }
            return response()->json("Activity updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function complete(Request $request)
    {
        try
        {
            $rules = [
                'id' => 'required',
                'directiveAndResolutionId' => 'required',
                'outputs' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $activity = DirectiveResolutionActivity::query()->find($id);
            if (!$activity)
            {
                throw new Exception("Activity with id {$id} not found!");
            }
            DB::beginTransaction();
            $outputs = $request->get('outputs');
            foreach ($outputs as $output){
                $activityOutput = DirectiveResolutionOutput::query()->find($id);
                if(empty($activityOutput)){
                    continue;
                }
                // set the actual value
                $activityOutput->actual = $output['actual'];
                $activityOutput->updated_by = $request->get('userId');
                $activityOutput->save();
                // add the comment
                if(!empty($output['comment']))
                {
                    $activityOutput->comments()->save(new Comment([
                        'user_id' => $request->get('userId'),
                        'body' => $output['comment'],
                    ]));
                }
            }
            $status = "completed";
            $endDate = Carbon::now();
            $activity->status = $status;
            $activity->end_date = $endDate;
            $activity->updated_by = $request->get('userId');
            $activity->save();
            DB::commit();
            return response()->json("Activity updated!");
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

}
