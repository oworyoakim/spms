<?php

namespace App\Http\Controllers;

use App\Models\Output;
use App\Models\OutputAchievement;
use App\Models\OutputIndicator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use stdClass;

class OutputAchievementsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $objectiveId = $request->get('objectiveId');
            if (!$objectiveId)
            {
                throw new Exception("Strategic objective id required!");
            }
            $reportPeriodId = $request->get('reportPeriodId');
            if (!$reportPeriodId)
            {
                throw new Exception("Report period required!");
            }
            $outputs = Output::query()
                             ->where('objective_id', $objectiveId)
                             ->get()
                             ->map(function (Output $item) use ($reportPeriodId) {
                                 $output = new stdClass();
                                 $output->id = $item->id;
                                 $output->name = $item->name;
                                 $output->description = $item->description;
                                 $output->objectiveId = $item->objective_id;
                                 $output->indicators = $item->indicators()
                                                            ->get()
                                                            ->map(function (OutputIndicator $outputIndicator) use ($reportPeriodId) {
                                                                $indicator = new stdClass();
                                                                $indicator->id = $outputIndicator->id;
                                                                $indicator->name = $outputIndicator->name;
                                                                $indicator->description = $outputIndicator->description;
                                                                $indicator->unit = $outputIndicator->unit;
                                                                $indicator->reportPeriodId = $reportPeriodId;
                                                                $target = $outputIndicator->targets()->where('report_period_id', $reportPeriodId)->first();
                                                                $indicator->target = !empty($target) ? $target->target : '0';
                                                                $achievement = $outputIndicator->achievements()->where('report_period_id', $reportPeriodId)->first();
                                                                $indicator->achievement = !empty($achievement) ? $achievement->actual : '0';
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

    public function store(Request $request)
    {
        try
        {
            $rules = [
                'achievements.*.objectiveId' => 'required',
                'achievements.*.indicators.*.reportPeriodId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $achievements = $request->get('achievements');
            foreach ($achievements as $achievement){
                $outputId = $achievement['id'];
                $objectiveId = $achievement['objectiveId'];
                foreach ($achievement['indicators'] as $indicator){
                    $indicatorId = $indicator['id'];
                    $reportPeriodId = $indicator['reportPeriodId'];
                    $value = $indicator['achievement'];
                    $value = floatval($value);
                    if($value > 0){
                        $outputAchievement = OutputAchievement::query()->updateOrCreate([
                            'objective_id' => $objectiveId,
                            'output_indicator_id' => $indicatorId,
                            'report_period_id' => $reportPeriodId,
                        ],[
                            'achievement_date' => Carbon::today(),
                            'actual' => $value,
                            'description' => $request->get('description'),
                            'created_by' => $request->get('userId'),
                        ]);
                    }
                }
            }
            return response()->json("Achievement saved!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {

    }
}
