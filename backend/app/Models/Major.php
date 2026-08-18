<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    //
    protected $table = 'majors';

    protected $fillable = [
        'title',
        'body',
        'color_from',
        'color_to',
        'icon',
    ];

    public function users (){
        return $this->hasMany(UserInfo::class);
    }
}
