<?php

namespace App\Models;

use App\Scopes\IsDirectorate;
use App\Traits\Addressable;
use App\Traits\BelongsToDirectorate;
use App\Traits\BelongsToExecutiveDirector;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Designation
 * @package App\Models
 * @property int id
 * @property string heads
 * @property int heads_id
 * @property string title
 * @property string description
 * @property string summary
 * @property boolean probational
 * @property int probation_period
 * @property int max_holders
 * @property boolean active
 * @property int supervisor_id
 * @property int salary_scale_id
 * @property int directorate_id
 * @property int department_id
 * @property int division_id
 * @property int section_id
 * @property int created_by
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Designation extends Model
{
    use BelongsToExecutiveDirector, BelongsToDirectorate;
    protected $table = 'designations';

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function directorate()
    {
        return $this->belongsTo(Directorate::class, 'directorate_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Designation::class, 'supervisor_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Designation::class, 'supervisor_id');
    }

    public function holders()
    {
        return $this->hasMany(Employee::class, 'designation_id');
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('active', true);
    }

    public function scopeInactive(Builder $query)
    {
        return $query->where('active', false);
    }


    public function getHolders()
    {
        return $this->holders()->get()->map(function ($holder) {
            $employee = new stdClass();
            $employee->id = $holder->id;
            $employee->fullName = $holder->fullName();
            $employee->username = $holder->username;
            $employee->avatar = $holder->avatar;
            $employee->designation = new stdClass();
            $employee->designation->id = $this->id;
            $employee->designation->title = $this->title;
            return $employee;
        });
    }

    public function getDetails($minimal = false)
    {
        $designation = new stdClass();
        $designation->id = $this->id;
        $designation->title = $this->title;
        $designation->shortName = $this->short_name;
        $designation->description = $this->description;
        $designation->probational = !!$this->probational;
        $designation->probationPeriod = $this->probation_period;
        $designation->summary = $this->summary;
        $designation->maxHolders = $this->max_holders;
        $designation->holders = $this->getHolders();
        $designation->active = !!$this->active;
        $designation->supervisorId = $this->supervisor_id;
        $designation->salaryScaleId = $this->salary_scale_id ?: null;
        $designation->heads = $this->heads ?: null;
        $designation->headsId = $this->heads_id ?: null;
        $designation->isHeadOf = null;
        $designation->createdBy = $this->created_by;
        $designation->updatedBy = $this->updated_by;
        $designation->createdAt = $this->created_at->toDateString();
        $designation->updatedAt = $this->updated_at->toDateString();

        return $designation;
    }
}
