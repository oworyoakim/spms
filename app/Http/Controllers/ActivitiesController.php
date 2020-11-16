<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Output;
use App\Models\OutputAchievement;
use App\Models\OutputIndicator;
use App\Models\OutputIndicatorTarget;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ActivitiesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Activity::query();
            $activityBlockId = $request->get('activityBlockId');
            $departmentId = $request->get('departmentId');
            $objectiveId = $request->get('objectiveId');
            $workPlanId = $request->get('workPlanId');
            if (!$workPlanId && !$activityBlockId)
            {
                throw new Exception("Work Plan ID  or Main Activity ID required!");
            }
            if ($workPlanId)
            {
                $builder->where('work_plan_id', $workPlanId);
            }
            if ($activityBlockId)
            {
                $builder->where('activity_block_id', $activityBlockId);
            }

            if ($objectiveId)
            {
                $builder->where('objective_id', $objectiveId);
            }

            $status = $request->get('status');
            if ($status)
            {
                $builder->where('status', $status);
            }

            $activities = $builder->get()->map(function (Activity $activity) {
                return $activity->getDetails();
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
                'code' => 'required|unique:activities,code',
                'title' => 'required',
                'dueDate' => 'required|date',
                'workPlanId' => 'required',
                'activityBlockId' => 'required',
                'objectiveId' => 'required',
                //'departmentId' => 'required',
                'teamLeaderId' => 'required',
                'quarter' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            Activity::query()->create([
                'title' => $request->get('title'),
                'due_date' => Carbon::parse($request->get('dueDate')),
                'work_plan_id' => $request->get('workPlanId'),
                'objective_id' => $request->get('objectiveId'),
                'department_id' => $request->get('departmentId'),
                'team_leader_id' => $request->get('teamLeaderId'),
                'activity_block_id' => $request->get('activityBlockId'),
                'quarter' => $request->get('quarter'),
                'description' => $request->get('description'),
                'directorate_id' => $request->get('directorateId'),
                'cost' => $request->get('cost'),
                'expenditure' => $request->get('expenditure'),
                'code' => $request->get('code'),
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
                'dueDate' => 'required|date',
                'objectiveId' => 'required',
                //'departmentId' => 'required',
                'teamLeaderId' => 'required',
                'activityBlockId' => 'required',
                'quarter' => 'required',
                'workPlanId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $activity = Activity::query()->find($id);
            if (!$activity)
            {
                throw new Exception("Activity with id {$id} not found!");
            }

            $code = $request->get('code');
            if($activity->code != $code && Activity::query()->where('code',$code)->count()){
                throw new Exception("Activity code {$code} already taken!");
            }

            $activity->update([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'due_date' => Carbon::parse($request->get('dueDate')),
                'activity_block_id' => $request->get('activityBlockId'),
                'work_plan_id' => $request->get('workPlanId'),
                'objective_id' => $request->get('objectiveId'),
                'department_id' => $request->get('departmentId'),
                'team_leader_id' => $request->get('teamLeaderId'),
                'directorate_id' => $request->get('directorateId'),
                'cost' => $request->get('cost'),
                'expenditure' => $request->get('expenditure'),
                'code' => $code,
                'updated_by' => $request->get('userId'),
            ]);
            return response()->json("Activity updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function hold(Request $request)
    {
        try
        {
            $rules = [
                'id' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);

            $id = $request->get('id');
            $userId = $request->get('userId');

            $activity = Activity::query()->where('status', 'ongoing')->find($id);
            if (!$activity)
            {
                throw new Exception("Activity with id {$id} not found!");
            }
            DB::beginTransaction();
            $activity->hold($userId);
            DB::commit();
            return response()->json("Activity updated!");
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function unhold(Request $request)
    {
        try
        {
            $rules = [
                'id' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);

            $id = $request->get('id');
            $userId = $request->get('userId');

            $activity = Activity::query()->where('status', 'onhold')->find($id);
            if (!$activity)
            {
                throw new Exception("Activity with id {$id} not found!");
            }
            DB::beginTransaction();
            $activity->unhold($userId);
            DB::commit();
            return response()->json("Activity updated!");
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function complete(Request $request)
    {
        try
        {
            //TODO: implement activity completion
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function performance(Request $request)
    {
        try
        {
            $builder = Output::query();
            $activityId = $request->get('activityId');
            $reportPeriodId = $request->get('reportPeriodId');

            if (!$activityId)
            {
                throw new Exception("Activity ID required!");
            }
            if (!$reportPeriodId)
            {
                throw new Exception("Report period required!");
            }

            $builder->where('activity_id', $activityId);

            $outputs = $builder->get()->map(function (Output $activityOutput) use ($reportPeriodId) {
                $output = $activityOutput->getDetails();
                $output->indicators = $activityOutput->indicators()->get()->map(function (OutputIndicator $outputIndicator) use ($reportPeriodId) {
                    $indicator = $outputIndicator->getDetails();
                    $indicator->reportPeriodId = $reportPeriodId;
                    $target = $outputIndicator->targets()->where('report_period_id', $reportPeriodId)->first();
                    $indicator->target = !empty($target) ? "{$target->target}" : "";
                    $achievement = $outputIndicator->achievements()->where('report_period_id', $reportPeriodId)->first();
                    $indicator->achievement = !empty($achievement) ? "{$achievement->actual}" : "";
                    //$indicator->variance = $indicator->achievement - $indicator->target;
                    return $indicator;
                });
                return $output;
            });
            return response()->json($outputs);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function updatePerformance(Request $request)
    {
        try
        {
            $performanceOutputs = $request->get('performance');
            $userId = $request->get('userId');
            $reportPeriodId = $request->get('reportPeriodId');

            DB::beginTransaction();
            foreach ($performanceOutputs as $output)
            {
                $activity = $output['activity'];

                foreach ($output['indicators'] as $indicator)
                {
                    // we cannot register an achievement with prior targets
                    if (!is_null($indicator['achievement']) && is_null($indicator['target']))
                    {
                        throw new Exception("No targets were set for some indicators!");
                    }
                    // if we have a target
                    if (!is_null($indicator['target']))
                    {
                        $indicatorTarget = OutputIndicatorTarget::query()->firstOrNew([
                            'output_indicator_id' => $indicator['id'],
                            'report_period_id' => $reportPeriodId,
                        ]);
                        $indicatorTarget->objective_id = $output['objectiveId'];
                        $indicatorTarget->target = $indicator['target'];
                        $indicatorTarget->due_date = $activity['dueDate'];
                        if (!$indicatorTarget->id)
                        {
                            $indicatorTarget->created_by = $userId;
                        } else
                        {
                            $indicatorTarget->updated_by = $userId;
                        }
                        $indicatorTarget->save();
                    }
                    // if we have an achievement
                    if (!is_null($indicator['achievement']))
                    {
                        $indicatorAchievement = OutputAchievement::query()->firstOrNew([
                            'output_indicator_id' => $indicator['id'],
                            'report_period_id' => $reportPeriodId,
                        ]);
                        $indicatorAchievement->objective_id = $output['objectiveId'];
                        $indicatorAchievement->actual = $indicator['achievement'];
                        $indicatorAchievement->achievement_date = Carbon::now();
                        if (!$indicatorAchievement->id)
                        {
                            $indicatorAchievement->created_by = $userId;
                        } else
                        {
                            $indicatorAchievement->updated_by = $userId;
                        }
                        $indicatorAchievement->save();
                    }
                }
            }
            DB::commit();
            return response()->json('Activity performance updated!');
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
