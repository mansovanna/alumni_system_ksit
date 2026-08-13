<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    //

    public function getAll()
    {
        $data = Major::all();

        return response()->json([
            'message' => 'Deparment Major',
            'data' => $data
        ]);
    }


    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = (int) $request->input('per_page', 15);
        $page = (int) $request->input('page', 1);


        $query = Major::query()->when($search, function ($q, $search) {
            $q->where('title', 'like', "%{$search}%")->orWhere('body', 'like', "%{$search}%");
        })->latest()->paginate($perPage, ['*'], 'page', $page)->withQueryString();

        return response()->json(
            [
                'message' => 'Major',
                'data' => $query
            ]
        );

    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'nullable|string',
            'color_from' => 'nullable|string',
            'color_to' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $data = Major::create([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'color_from' => $request->input('color_from', '#22c55e'),
            'color_to' => $request->input('color_to', '#15803d'),
            'icon' => $request->input('icon', 'academic-cap'),
        ]);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create major',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Create success',
            'data' => $data
        ], 201);
    }

    public function update(Request $request, $id)
    {


        $request->validate([
            'title' => 'required|string',
            'body' => 'nullable|string',
        ]);

        $data = Major::findOrFail($id);

        $data->title = $request->input('title', $data->title);
        $data->body = $request->input('body', $data->body);
        $data->color_from = $request->input('color_from', $data->color_from);
        $data->color_to = $request->input('color_to', $data->color_to);
        $data->icon = $request->input('icon', $data->icon);

        $data->save();

        return response()->json([
            'message' => 'Update success',
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        Major::find($id)->delete();

        return response()->json([
            'message' => 'delete success',
        ], 204);
    }
}
