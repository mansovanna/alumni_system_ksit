<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //

    public function login (Request $request)

    {
        $request->validate([
            'login'=> 'required',
            'password' => 'required|string|min:6'
        ]);
    }
}
