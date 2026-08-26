<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffProfiles extends Model
{
    //

    protected $table = 'staff_profiles';

    protected $fillable = [
        'user_id',
        'position'
    ];
}
