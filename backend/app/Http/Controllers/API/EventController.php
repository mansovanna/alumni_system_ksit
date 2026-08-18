<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    //

    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);


        $qurey = Event::query();


        if ($search) {
            $qurey->where(function ($q) use ($search) {
                $q->where('title', 'LIEK', "%{$search}%")->orWhere('created_at', 'LIKE', "%{$search}%");
            });
        }

        $data = $qurey->latest()->paginate($perPage, ['*'], 'page', $page)->withQueryString();

        return response()->json([
            'message' => 'Event all',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $data = Event::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data not found',
            ], 404);
        }
        return response()->json([
            'message' => 'Event Show',
            'data' => $data
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string'
        ]);


        $data = Event::create([
            'title' => $request->input('title', ''),
            'body' => $request->input('body', '')
        ]);


        if (!$data) {
            return response()->json([
                'message' => 'Failse create event',
            ], 401);
        }


        return response()->json([
            'message' => $data
        ]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string'
        ]);


        $data = Event::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data not found',
            ], 404);
        }

        $data->update([
            'title' => $request->input('title', $data->title),
            'body' => $request->input('body', $data->body)
        ]);


        return response()->json([
            'message' => 'Udate success',
            'data' => $data
        ]);
    }


    public function destroy($id)
    {
        Event::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Delete success',
        ], 204);
    }
}
