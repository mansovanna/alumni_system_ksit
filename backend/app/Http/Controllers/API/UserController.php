<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    //
    public function getMe(Request $request)
    {
        $user = $request->user();


        return response()->json([
            'data' => $user->load('userInfo')
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required|string|min:6'
        ]);

        $current_user = User::with('userInfo')
            ->where('email', $request->login)
            ->orWhere('mobile', $request->login)
            ->first();


        if (!$current_user) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'login' => 'Invalid user not found'
                ]
            ], 404);
        }

        if ($current_user && !Hash::check($request->password, $current_user->password)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'password' => 'Invalid password'
                ]
            ], 404);
        }

        // Login success
        return response()->json([
            'success' => true,
            'message' => 'Login success',
            'user' => $current_user,
            'token' => $current_user->createToken('auth_token')->plainTextToken
        ], 200);
    }


    public function register(Request $request)
    {

        $request->validate(
            [
                'email' => 'required|unique:users,email',
                'name_english' => 'required|string',
                'password' => 'required|min:6|confirmed'
            ]
        );


        $user = User::create(
            [
                'name_english' => $request->input('name_english'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password'))
            ]
        );


        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register user',
            ], 500);
        }

        $userInfo = UserInfo::create([
            'user_id' => $user->id
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Register success',
            'data' => $user,
        ], 201);
    }


    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 1);
        $page = $request->input('page', 1);

        // filter more
        $major = $request->input('major', '');
        $status_work = $request->input('work', null);

        $query = User::with('userInfosOne.major')->where('role', 'alumni');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('mobile', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('name_english', 'LIKE', "%{$search}%")
                    ->orWhereHas('userInfosOne', function ($sub) use ($search) {
                        $sub->where('last_year', 'LIKE', "%{$search}%");
                    });
            });
        }

        $major = ($major === null || $major === '' || $major === 'null') ? null : $major;
        $status_work = ($status_work === null || $status_work === '' || $status_work === 'null') ? null : $status_work;



        // filter by major
        if ($major) {
            $query->whereHas('userInfosOne.major', function ($q) use ($major) {
                $q->where('id', $major);
            });
        }

        // filter by work status (in userInfosOne relation)
        if ($status_work !== null && $status_work !== '') {
            $query->whereHas('userInfosOne', function ($q) use ($status_work) {
                $q->where('work', $status_work);
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();

        return response()->json([
            'message' => 'Alumni',
            'data' => $users
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name_english' => 'required|string',
            'mobile' => 'required|unique:users,mobile',
            'email' => 'nullable|email|unique:users,email',
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'major_id' => 'required|integer',
            'address' => 'nullable|string',
            'work' => 'nullable|string',
            'last_year' => 'nullable|string',
            'work_address' => 'nullable|string',
            'password' => 'required|min:6|confirmed',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $file_url = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $fileName = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('uploads'), $fileName);

            $file_url = 'uploads/' . $fileName;
        }

        $user = User::create([
            'name_khmer' => $request->input('name_khmer'),
            'name_english' => $request->input('name_english'),
            'mobile' => $request->input('mobile'),
            'email' => $request->input('email'),
            'role' => $request->input('role', 'alumni'),
            'status' => $request->input('status', 'pending'),
            'password' => Hash::make($request->input('password')),
            'gender' => $request->input('gender', 'female'),
            'profile' => $file_url,
        ]);

        UserInfo::create([
            'user_id' => $user->id,
            'major_id' => $request->input('major_id'),
            'date_of_birth' => $request->input('date_of_birth'),
            'address' => $request->input('address'),
            'work' => $request->input('work', 'continuing_study'),
            'work_address' => $request->input('work_address'),
            'last_year' => $request->input('last_year'),
        ]);

        return response()->json([
            'message' => 'Add Alumni',
            'data' => $user->load('userInfosOne.major'),
        ], 201);
    }


    public function show($id)
    {
        $user = User::with('userInfo')->findOrFail($id);

        if (!$user) {
            return response()->json(
                [
                    'message' => 'Failse curren user'
                ],
                404
            );
        }

        return response()->json([
            'message' => 'curren user',
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Failed to find current user',
            ], 404);
        }

        // validate
        $request->validate([
            'name_english' => 'required|string',
            'name_khmer' => 'nullable|string',
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|string',
            'major_id' => 'required|integer',
            'address' => 'nullable|string',
            'work' => 'nullable|string',
            'work_address' => 'nullable|string',
            'mobile' => ['nullable', 'string', Rule::unique('users', 'mobile')->ignore($user->id)],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        // update user
        $user->update([
            'name_khmer' => $request->input('name_khmer', $user->name_khmer),
            'name_english' => $request->input('name_english'),
            'mobile' => $request->input('mobile', $user->mobile),
            'email' => $request->input('email', $user->email),
            'gender' => $request->input('gender', $user->gender),
            'role' => $request->input('role', $user->role),
            'status' => $request->input('status', $user->status),
        ]);

        // update
        UserInfo::updateOrCreate(
            ['user_id' => $user->id],
            [
                'major_id' => $request->input('major_id'),
                'date_of_birth' => $request->input('date_of_birth'),
                'address' => $request->input('address'),
                'work' => $request->input('work'),
                'work_address' => $request->input('work_address'),
                'last_year' => $request->input('last_year'),
            ]
        );

        return response()->json([
            'message' => 'Update success',
            'data' => $user->load('userInfosOne.major'),
        ]);
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $user = User::find($id);

        if ($user) {
            $user->update([
                'status' => $request->input('status', 'pending'),
            ]);
        }

        return response()->json([
            'message' => 'Status update success',
            'data' => $user->load('userInfosOne.major'),
        ]);
    }

    // delete
    public function destroy($id)
    {
        $user = User::find($id)->delete();

        if (!$user) {
            return response()->json([
                'message' => 'Failse delete',
            ], 401);
        }
        return response()->json([
            'message' => 'Delete success',
        ], 204);
    }



    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logouot scuccess',
        ], 404);
    }
}
