<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ActivityScheduleEmployee extends Pivot
{
    protected $table = 'activity_schedule_employee';

    protected $fillable = [
        'activity_schedule_id',
        'user_id',
        'crew',
        'notes',
    ];
}
