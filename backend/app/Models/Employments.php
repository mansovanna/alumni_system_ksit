<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employments extends Model
{
    //

    protected $table = 'employments';

    protected $fillable = [
        'alumni_id',
        'company_name',
        'job_title',
        'employment_type',
        'industry',
        'location',
        'salary_range',
        'start_date',
        'end_date',
        'is_current',
    ];



    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }


}
