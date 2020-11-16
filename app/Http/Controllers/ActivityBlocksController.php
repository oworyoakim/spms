<?php

namespace App\Http\Controllers;

use App\Models\ActivityBlock;
use App\Models\ActivityBlockDirectorate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\DB;

class ActivityBlocksController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = ActivityBlock::query();
            $objectiveId = $request->get('objectiveId');
            $outcomeId = $request->get('outcomeId');
            $workPlanId = $request->get('workPlanId');
            if ($objectiveId)
            {
                $builder->where('objective_id', $objectiveId);
            }

            if ($outcomeId)
            {
                $builder->where('outcome_id', $outcomeId);
            }

            if ($workPlanId)
            {
                $builder->where('work_plan_id', $workPlanId);
            }

            $activityBlocks = $builder->get()->map(function (ActivityBlock $block) {
                return $block->getDetails();
            });
            return response()->json($activityBlocks);
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
                'objectiveId' => 'required',
                'outcomeId' => 'required',
                'workPlanId' => 'required',
                'title' => 'required',
                'cost' => 'required',
                'code' => 'required|unique:activity_blocks',
                'quarter' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);

            $directorateIds = $request->get('directorateIds');
            if (!count($directorateIds))
            {
                throw new Exception("You must choose at least one directorate!");
            }

            DB::beginTransaction();
            $activityBlock = ActivityBlock::query()->create([
                'objective_id' => $request->get('objectiveId'),
                'outcome_id' => $request->get('outcomeId'),
                'work_plan_id' => $request->get('workPlanId'),
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'quarter' => $request->get('quarter'),
                'cost' => $request->get('cost'),
                'code' => $request->get('code'),
                'created_by' => $request->get('userId'),
            ]);
            foreach ($directorateIds as $directorateId)
            {
                ActivityBlockDirectorate::query()->create([
                    'activity_block_id' => $activityBlock->id,
                    'directorate_id' => $directorateId
                ]);
            }
            DB::commit();
            return response()->json("Activity block created!");
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            $id = $request->get('id');
            $activityBlock = ActivityBlock::query()->find($id);
            if (!$activityBlock)
            {
                throw new Exception("Activity block not found!");
            }
            $rules = [
                'id' => 'required',
                'title' => 'required',
                'objectiveId' => 'required',
                'outcomeId' => 'required',
                'workPlanId' => 'required',
                'cost' => 'required',
                'code' => 'required',
                'quarter' => 'required',
                'userId' => 'required',
            ];
            $code = $request->get('code');
            if (empty($code))
            {
                $rules['code'] = 'required|unique:activity_blocks';
            } elseif ($code != $activityBlock->code && ActivityBlock::query()->where('code', $code)->count())
            {
                throw new Exception("An activity block with code {$code} already exists!");
            }
            $this->validateData($request->all(), $rules);

            $directorateIds = $request->get('directorateIds');
            if (!count($directorateIds))
            {
                throw new Exception("You must choose at least one directorate!");
            }
            DB::beginTransaction();
            $activityBlock->update([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'objective_id' => $request->get('objectiveId'),
                'work_plan_id' => $request->get('workPlanId'),
                'outcome_id' => $request->get('outcomeId'),
                'quarter' => $request->get('quarter'),
                'cost' => $request->get('cost'),
                'code' => $request->get('code'),
                'updated_by' => $request->get('userId'),
            ]);

            ActivityBlockDirectorate::query()
                                    ->where('activity_block_id', $activityBlock->id)
                                    ->whereNotIn('directorate_id', $directorateIds )
                                    ->delete();

            foreach ($directorateIds as $directorateId)
            {
                ActivityBlockDirectorate::query()->updateOrCreate([
                    'activity_block_id' => $activityBlock->id,
                    'directorate_id' => $directorateId
                ]);
            }
            DB::commit();
            return response()->json("Activity block updated!");
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function show($id)
    {
        try
        {
            $block = ActivityBlock::query()->find($id);
            if (!$block)
            {
                throw new Exception("Activity block with id {$id} not found!");
            }
            $activityBlock = $block->getDetails();
            return response()->json($activityBlock);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
