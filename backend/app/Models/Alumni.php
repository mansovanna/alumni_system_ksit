<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    //

    protected $table = 'alumnis';


    protected $fillable = [
        'user_id',
        'major_id',
        'graduation_year',
        'gpa',
        'gender',
        'dob',
        'address',
        'bio',
        'linkedin_url',
        'facebook_url',
        'profile_photo',
        'employment_status'
    ];





    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function employment()
    {
        return $this->hasOne(Employments::class);
    }
    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistrations::class);
    }


}
