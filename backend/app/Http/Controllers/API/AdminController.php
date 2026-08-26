<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $serach = $request->input('search', '');
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);


        $query = User::whereNot('role', 'alumni');

        if ($serach) {
            $query->where(function ($q) use ($serach) {
                $q->where('name_english', 'LIKE', "%{$serach}%")->orWhere('email', 'LIKE', "%{$serach}%")->orWhere('mobile', 'LIKE', "%{$serach}%");
            });
        }

        $data = $query->orderBy('created_at', 'desc')->latest()->paginate($perPage, ['*'], 'page', $page)->withQueryString();

        return response()->json([
            'message' => "Message all",
            'data' => $data
        ]);
    }
    //
    public function store(Request $request)
    {
        $request->validate([
            'name_khmer' => 'required|string',
            'name_english' => 'required|string',
            'login' => 'required|string',
            'password' => 'required|min:6|confirmed',
            'role' => 'nullable|string|in:staff,admin'
        ]);

        $input = $request->login;
        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL) !== false;

        $request->validate([
            'login' => $isEmail
                ? 'email|unique:users,email'
                : ['regex:/^[0-9]{9,10}$/', 'unique:users,mobile'],
        ]);


        $user = User::create([
            'name_khmer' => $request->name_khmer,
            'name_english' => $request->name_english,
            'email' => $isEmail ? $input : null,
            'mobile' => $isEmail ? null : $input,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'staff',
        ]);

        return response()->json([
            'message' => 'User create success',
            'data' => $user
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name_khmer' => 'nullable|string',
            'name_english' => 'nullable|string',
            'role' => 'nullable|string|in:staff,admin'
        ]);

        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        $user->update([
            'name_khmer' => $request->input('name_khmer', $user->name_khmer),
            'name_english' => $request->input('name_english', $user->name_english),
            'role' => $request->input('role', $user->role)
        ]);

        return response()->json([
            'message' => 'Update Success',
            'data' => $user
        ]);
    }


    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(
                [
                    'message' => 'Data not found!'
                ],
                404
            );
        }
        // Delete item
        $user->delete();

        // return confirm
        return response()->json([
            'message' => 'delete success',
        ], 204);
    }

    // block reset password
    public function restPass(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);


        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json([
                'message' => 'User Not found'
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        $user->tokens()->delete();


        return response()->json([
            'message' => 'Update success',
            'data' => $user
        ]);
    }

}
