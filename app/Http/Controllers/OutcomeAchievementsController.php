<?php

namespace App\Http\Controllers;

use App\Models\Outcome;
use App\Models\OutcomeAchievement;
use App\Models\OutcomeIndicator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use stdClass;

class OutcomeAchievementsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $keyResultAreaId = $request->get('keyResultAreaId');
            if (!$keyResultAreaId)
            {
                throw new Exception("Key result area id required!");
            }
            $reportPeriodId = $request->get('reportPeriodId');
            if (!$reportPeriodId)
            {
                throw new Exception("Report period required!");
            }
            $outcomes = Outcome::query()
                             ->where('key_result_area_id', $keyResultAreaId)
                             ->get()
                             ->map(function (Outcome $item) use ($reportPeriodId) {
                                 $outcome = new stdClass();
                                 $outcome->id = $item->id;
                                 $outcome->name = $item->name;
                                 $outcome->description = $item->description;
                                 $outcome->keyResultAreaId = $item->key_result_area_id;
                                 $outcome->indicators = $item->indicators()
                                                            ->get()
                                                            ->map(function (OutcomeIndicator $outcomeIndicator) use ($reportPeriodId) {
                                                                $indicator = new stdClass();
                                                                $indicator->id = $outcomeIndicator->id;
                                                                $indicator->name = $outcomeIndicator->name;
                                                                $indicator->description = $outcomeIndicator->description;
                                                                $indicator->unit = $outcomeIndicator->unit;
                                                                $indicator->reportPeriodId = $reportPeriodId;
                                                                $target = $outcomeIndicator->targets()->where('report_period_id', $reportPeriodId)->first();
                                                                $indicator->target = !empty($target) ? $target->target : '0';
                                                                $achievement = $outcomeIndicator->achievements()->where('report_period_id', $reportPeriodId)->first();
                                                                $indicator->achievement = !empty($achievement) ? $achievement->actual : '0';
                                                                return $indicator;
                                                            });
                                 return $outcome;
                             });
            return response()->json($outcomes);
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
                'achievements.*.keyResultAreaId' => 'required',
                'achievements.*.indicators.*.reportPeriodId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $achievements = $request->get('achievements');
            foreach ($achievements as $achievement){
                $outcomeId = $achievement['id'];
                $keyResultAreaId = $achievement['keyResultAreaId'];
                foreach ($achievement['indicators'] as $indicator){
                    $indicatorId = $indicator['id'];
                    $reportPeriodId = $indicator['reportPeriodId'];
                    $value = $indicator['achievement'];
                    $value = floatval($value);
                    if($value > 0){
                        $outcomeAchievement = OutcomeAchievement::query()->updateOrCreate([
                            'key_result_area_id' => $keyResultAreaId,
                            'outcome_indicator_id' => $indicatorId,
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

    public function update(Request $request){

    }
}
