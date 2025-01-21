<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivitySchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'activity_date',
        'activity_time',
        'No_OP',
        'order',
        'customer_name',
        'customer_phone',
        'category_id',
        'subcategory_id',
        'status',
        'user_id',
        'package',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employees()
    {
        return $this->belongsToMany(User::class, 'activity_schedule_employee', 'activity_schedule_id', 'user_id')
        ->using(ActivityScheduleEmployee::class)
        ->withPivot('crew', 'notes');
    }
    
}
