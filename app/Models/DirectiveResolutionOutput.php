<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class DirectiveResolutionOutput
 * @package App\Models
 * @property int id
 * @property string title
 * @property string description
 * @property int directive_resolution_id
 * @property int directive_resolution_activity_id
 * @property float target
 * @property float actual
 * @property string unit
 * @property int responsible_person
 * @property Carbon output_date_updated
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class DirectiveResolutionOutput extends Model
{
    protected $table = 'directive_resolution_outputs';

    protected $dates = ['deleted_at'];

    public function directiveResolution()
    {
        return $this->belongsTo(DirectiveResolution::class, 'directive_resolution_id');
    }

    public function activity()
    {
        return $this->belongsTo(DirectiveResolutionActivity::class, 'directive_resolution_activity_id');
    }

    public function getDetails()
    {
        $output = new stdClass();
        $output->id = $this->id;
        $output->directiveResolutionId = $this->directive_resolution_id;
        $output->directiveResolutionActivityId = $this->directive_resolution_activity_id;
        $output->target = $this->target;
        $output->actual = $this->actual;
        $output->unit = $this->unit;
        $output->title = $this->title;
        $output->description = $this->description;
        $output->directiveResolution = ($this->directiveResolution) ? $this->directiveResolution->getDetails() : null;
        $output->activity = ($this->activity) ? $this->activity->getDetails() : null;
        $output->responsiblePerson = $this->responsible_person;
        $output->outputDateUpdated = ($this->output_date_updated) ? $this->output_date_updated->toDateString() : null;
        $output->createdBy = $this->created_by;
        $output->updatedBy = $this->updated_by;
        $output->createdAt = $this->created_at->toDateTimeString();
        $output->updatedAt = $this->updated_at->toDateTimeString();

        return $output;
    }
}
