<?php

namespace App\Models;

use App\Traits\Addressable;
use App\Traits\BelongsToDirectorate;
use App\Traits\BelongsToExecutiveDirector;
use App\Traits\Commentable;
use App\Traits\Contactable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Employee
 *
 * @author yoakim
 * @property int id
 * @property int user_id
 * @property string employee_number
 * @property string username
 * @property string title
 * @property string first_name
 * @property string last_name
 * @property string middle_name
 * @property string other_names
 * @property string gender
 * @property string email
 * @property string nin
 * @property string passport
 * @property string nssf
 * @property string tin
 * @property string permit
 * @property bool approved
 * @property string nationality
 * @property string employee_status
 * @property string employment_term
 * @property string employment_status
 * @property string employment_type
 * @property int created_by
 * @property int approved_by
 * @property int salary_scale_id
 * @property int designation_id
 * @property int section_id
 * @property int division_id
 * @property int department_id
 * @property int directorate_id
 * @property string $marital_status
 * @property string religion
 * @property string avatar
 * @property Carbon dob
 * @property Carbon approved_at
 * @property Carbon date_joined
 * @property Carbon exit_date
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 *
 */
class Employee extends Model
{
    use BelongsToExecutiveDirector, BelongsToDirectorate;

    const STATUS_ONBOARDING = 'onboarding';
    const STATUS_ACTIVE = 'active';
    const STATUS_ONLEAVE = 'onleave';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_RESIGNED = 'resigned';
    const STATUS_DEAD = 'dead';
    const STATUS_RETIRED = 'retired';
    const STATUS_DISMISSED = 'dismissed';
    const STATUS_SECONDED = 'seconded';
    const STATUS_RELEASED = 'released';

    protected $dates = [
        'dob',
        'date_joined',
        'exit_date',
        'approved_at'
    ];

    public function fullName()
    {
        $fullName = "{$this->title} {$this->first_name} {$this->last_name}";
        if (!empty($this->other_names))
        {
            $fullName .= " {$this->other_names}";
        }
        if (!empty($this->middle_name))
        {
            $fullName .= " {$this->middle_name}";
        }
        return $fullName;
    }

    /**
     * Return true if the employee is allowed to log into the system
     * @return bool
     */
    public function canLogin()
    {
        return in_array($this->employee_status, [Employee::STATUS_ACTIVE, Employee::STATUS_ONLEAVE]);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function directorate()
    {
        return $this->belongsTo(Directorate::class, 'directorate_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('employee_status', Employee::STATUS_ACTIVE);
    }

    public function scopeSuspended(Builder $query)
    {
        return $query->where('employee_status', Employee::STATUS_SUSPENDED);
    }

    public function scopeOnleave(Builder $query)
    {
        return $query->where('employee_status', Employee::STATUS_ONLEAVE);
    }

    public function scopeOnboard(Builder $query)
    {
        return $query->where('employee_status', Employee::STATUS_ONBOARDING);
    }

    public function scopeExited(Builder $query)
    {
        return $query->whereIn('employee_status', [
            Employee::STATUS_RESIGNED,
            Employee::STATUS_DISMISSED,
            Employee::STATUS_DEAD,
            Employee::STATUS_RETIRED,
            Employee::STATUS_SECONDED,
        ]);
    }


}
