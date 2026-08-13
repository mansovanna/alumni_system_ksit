<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    //
    protected $table = 'user_infos';


    protected $fillable = [
        'user_id',
        'major_id',
        'date_of_birth',
        'address',
        'work',
        'work_address',
        'last_year',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }
}
