<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Carbon;

/**
 * Class ActivityBlockDirectorate
 * @package App\Models
 * @property int id
 * @property int activity_block_id
 * @property int directorate_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ActivityBlockDirectorate extends EloquentModel
{
    protected $table = 'activity_block_directorates';

    protected $guarded = [];

    public function activityBlock()
    {
        return $this->belongsTo(ActivityBlock::class, 'activity_block_id');
    }

}
