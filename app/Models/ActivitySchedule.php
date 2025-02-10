<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function MasterEmployees(): HasMany
    {
        return $this->hasMany(MasterEmployee::class);
    }

   
    
}
