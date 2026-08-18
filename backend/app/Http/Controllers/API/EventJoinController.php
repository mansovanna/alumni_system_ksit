<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventJoinController extends Controller
{
    //


    public function store(Request $request)
    {

        $request->validate([
            'event_id' => 'required'
        ]);


        $user = $request->user();

        return response()->json([
            'message' => 'Join',
            'data' => $user
        ]);

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required'
        ]);


        $data = Event::findOrFail($id);

        $data->update([
            'status' => $request->input('status', 'active')
        ]);


        return response()->json([
            'message' => 'update success',
            'data' => $data
        ]);
    }
}
