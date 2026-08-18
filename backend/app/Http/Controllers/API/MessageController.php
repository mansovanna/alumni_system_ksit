<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    //

    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);


        $querys = Message::query();


        if ($search) {
            $querys->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")->orWhere('created_at', 'LIKE', "%{$search}%");
            });
        }

        $data = $querys->latest()->paginate($perPage, ['*'], 'page', $page)->withQueryString();


        return response()->json([
            'message' => "Message all",
            'data' => $data
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string'
        ]);

        $message = Message::create([
            'title' => $request->input('title', ''),
            'body' => $request->input('body', '')
        ]);

        if (!$message) {
            return response()->json([
                'message' => 'Failse create',
            ], 401);
        }

        return response()->json([
            'message' => 'create success',
            'data' => $message
        ]);
    }


    public function show($id)
    {
        $message = Message::findOrFail($id);


        if (!$message) {
            return response()->json([
                'message' => 'Data is not found',
            ], 403);
        }

        return response()->json([
            'message' => 'Messages',
            'data' => $message
        ]);
    }



    public function update(Request $request, $id)
    {

        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string'
        ]);

        $data = Message::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => 'Message not found!'
            ], 404);
        }

        $data->update([
            'title' => $request->input('title', ''),
            'body' => $request->input('body', '')
        ]);

        return response()->json([
            'message' => 'Message updated',
            'data' => $data
        ]);

    }


    public function destroy($id)
    {
        Message::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Delete success',
        ], 204);
    }
}
