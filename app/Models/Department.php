<?php

namespace App\Models;

use App\Traits\BelongsToDirectorate;
use App\Traits\BelongsToExecutiveDirector;
use App\Traits\HasAHead;
use stdClass;

class Department extends Model
{
    use BelongsToExecutiveDirector, BelongsToDirectorate, HasAHead;

    public function directorate()
    {
        return $this->belongsTo(Directorate::class, 'directorate_id');
    }

    public function divisions()
    {
        return $this->hasMany(Division::class, 'department_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'department_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    public function getDetails()
    {
        $department = new stdClass();
        $department->id = $this->id;
        $department->title = $this->title;
        $department->description = $this->description;
        $department->directorateId = $this->directorate_id ?: null;
        $department->directorate = $this->directorate ? $this->directorate->getDetails() : null;
        $department->createdBy = $this->created_by;
        $department->updatedBy = $this->updated_by;
        $department->createdAt = $this->created_at->toDateString();
        $department->updatedAt = $this->updated_at->toDateString();
        $department->head = $this->getHeadOf('department');
        return $department;
    }
}
