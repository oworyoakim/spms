<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use stdClass;

/**
 * Class DirectiveResolution
 * @package App\Models
 * @property int id
 * @property string title
 * @property string description
 * @property int work_plan_id
 * @property int responsibility_centre // a designation
 * @property string type // resolution or directive
 * @property string source_type // internal or external
 * @property string source_organization // null if internal
 * @property string source_office // null if internal
 * @property string source_officer // null if internal
 * @property string source_telephone // null if internal
 * @property string source_email // null if internal
 * @property Carbon date_received
 * @property Carbon deadline
 * @property Carbon end_date
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class DirectiveResolution extends Model
{
    protected $table = 'directives_and_resolutions';

    protected $dates = ['date_received', 'deadline','end_date', 'deleted_at'];

    public function workPlan()
    {
        return $this->belongsTo(WorkPlan::class, 'work_plan_id');
    }

    public function activities()
    {
        return $this->hasMany(DirectiveResolutionActivity::class, 'directive_resolution_id');
    }

    public function outputs()
    {
        return $this->hasMany(DirectiveResolutionOutput::class, 'directive_resolution_id');
    }

    public function getDetails()
    {
        $directiveResolution = new stdClass();
        $directiveResolution->id = $this->id;
        $directiveResolution->title = $this->title;
        $directiveResolution->description = $this->description;
        $directiveResolution->responsibilityCentreId = $this->responsibility_centre;
        $directiveResolution->responsibilityCentre = null;
        $directiveResolution->type = $this->type;
        $directiveResolution->sourceType = $this->source_type;
        $directiveResolution->sourceOrganization = $this->source_organization;
        $directiveResolution->sourceOffice = $this->source_office;
        $directiveResolution->sourceOfficer = $this->source_officer;
        $directiveResolution->sourceTelephone = $this->source_telephone;
        $directiveResolution->sourceEmail = $this->source_email;
        $directiveResolution->dateReceived = $this->date_received->toDateString();
        $directiveResolution->deadline = $this->deadline->toDateString();
        $directiveResolution->endDate = ($this->end_date) ? $this->end_date->toDateString() : null;
        $directiveResolution->workPlanId = $this->work_plan_id;
        $directiveResolution->workPlan = ($this->workPlan) ? $this->workPlan->getDetails() : null;
        $directiveResolution->createdBy = $this->created_by;
        $directiveResolution->updatedBy = $this->updated_by;
        $directiveResolution->createdAt = $this->created_at->toDateTimeString();
        $directiveResolution->updatedAt = $this->updated_at->toDateTimeString();

        return $directiveResolution;
    }

    /**
     * @return stdClass
     * @throws Exception
     */
    public function generateReport()
    {
        $reportData = new stdClass();
        $reportData->title = $this->title;
        $reportData->responsibilityCentreId = $this->responsibility_centre;
        $reportData->reportDate = Carbon::today()->toDateString();
        $reportData->workPlan = ($this->workPlan) ? $this->workPlan->getDetails() : null;
        //$reportData->reportPeriod = $reportPeriod->getDetails();

        $reportData->activities = Collection::make();
        foreach ($this->activities()->get() as $activity)
        {
            $dirAndResActivity = new stdClass();
            $dirAndResActivity->title = $activity->title;
            $dirAndResActivity->status = $activity->status;
            // Outputs
            $dirAndResActivity->outputs = Collection::make();
            foreach ($activity->outputs as $output)
            {
                $dirAndResActivityOutput = new stdClass();
                $dirAndResActivityOutput->title = $output->title;
                $dirAndResActivityOutput->unit = $output->unit;
                $dirAndResActivityOutput->target = empty($output->target) ? null : $output->target;
                $dirAndResActivityOutput->actual = empty($output->actual) ? null : $output->actual;
                //  Percentage of achievement and variance
                if ($dirAndResActivityOutput->target && $dirAndResActivityOutput->actual)
                {
                    $dirAndResActivityOutput->achieved = round(($dirAndResActivityOutput->actual / $dirAndResActivityOutput->target) * 100, 2);
                    $dirAndResActivityOutput->variance = $dirAndResActivityOutput->target - $dirAndResActivityOutput->actual;
                } else
                {
                    $dirAndResActivityOutput->achieved = null;
                    $dirAndResActivityOutput->variance = null;
                }
                $dirAndResActivityOutput->comments = $output->comments()->pluck('body')->implode("\n");
                $dirAndResActivity->outputs->push($dirAndResActivityOutput);
            }

            $reportData->activities->push($dirAndResActivity);
        }

        return $reportData;
    }
}
