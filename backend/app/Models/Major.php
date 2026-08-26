<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    //

    protected $table = 'majors';

    protected $fillable = [
        'name',
        'description'
    ];


    public function alumnis()
    {
        return $this->hasMany(Alumni::class);
    }
}
