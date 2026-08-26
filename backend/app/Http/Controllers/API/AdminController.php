<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = (int) $request->input('per_page', 15);

        $query = User::query()
            ->with('role')
            ->whereHas('role', function ($q) {
                // Take every role except alumni
                $q->where('name', '!=', 'alumni');
            });

        // Search
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name_english', 'LIKE', "%{$search}%")
                    ->orWhere('name_khmer', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('mobile', 'LIKE', "%{$search}%");
            });
        }

        $data = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'message' => 'Staff list retrieved successfully',
            'data' => $data,
        ]);
    }
    //
    public function store(Request $request)
    {
        // ---------------------------------------------------------
        // Validation
        // ---------------------------------------------------------
        $request->validate([
            'name_khmer' => 'required|string|max:255',
            'name_english' => 'required|string|max:255',
            'login' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:staff,admin',
        ]);

        // ---------------------------------------------------------
        // Detect login type
        // Email OR Mobile
        // ---------------------------------------------------------
        $input = trim($request->login);

        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL) !== false;

        // ---------------------------------------------------------
        // Validate email / mobile uniqueness
        // ---------------------------------------------------------
        if ($isEmail) {
            $request->validate([
                'login' => 'email|unique:users,email',
            ]);
        } else {
            $request->validate([
                'login' => [
                    'regex:/^[0-9]{9,10}$/',
                    'unique:users,mobile',
                ],
            ]);
        }

        // ---------------------------------------------------------
        // Find Role
        // ---------------------------------------------------------
        $role = Role::where('name', $request->role)->first();

        if (!$role) {
            return response()->json([
                'message' => 'Role not found.',
            ], 422);
        }

        // ---------------------------------------------------------
        // Create User
        // ---------------------------------------------------------
        $user = User::create([
            'name_khmer' => $request->name_khmer,
            'name_english' => $request->name_english,

            'email' => $isEmail ? $input : null,
            'mobile' => $isEmail ? null : $input,

            'password' => Hash::make($request->password),

            'role_id' => $role->id,

            'status' => 'active',
        ]);

        // ---------------------------------------------------------
        // Load Role
        // ---------------------------------------------------------
        $user->load('role');

        return response()->json([
            'message' => 'User created successfully.',
            'data' => $user,
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


        $role = Role::where('name', $request->role)->first();

        if (!$role) {
            return response()->json([
                'message' => 'Role not found.',
            ], 422);
        }


        $user->update([
            'name_khmer' => $request->input('name_khmer', $user->name_khmer),
            'name_english' => $request->input('name_english', $user->name_english),
            'role_id' => $role->id,
        ]);

        return response()->json([
            'message' => 'Update Success',
            'data' => $user->load('role')
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
