<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ActivityLogs;
use App\Models\Alumni;
use App\Models\Employments;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $request->validate([
            'login' => [
                'required',
                'string',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
            ],
        ]);



        $user = User::with([
            'role',
            'alumni',
        ])
            ->where('email', $request->login)
            ->orWhere('mobile', $request->login)
            ->first();


        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'errors' => [
                    'login' => [
                        'The email or mobile number is incorrect.',
                    ],
                ],
            ], 401);
        }



        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password.',
                'errors' => [
                    'password' => [
                        'The password is incorrect.',
                    ],
                ],
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not active.',
            ], 403);
        }


        $user->tokens()->delete();


        $token = $user
            ->createToken('mobile-app')
            ->plainTextToken;



        return response()->json([
            'success' => true,
            'message' => 'Login successful.',

            'data' => [
                'user' => $this->userData($user),

                'token' => $token,

                'token_type' => 'Bearer',
            ],
        ], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | GET CURRENT USER
    |--------------------------------------------------------------------------
    */

    public function me(Request $request)
    {
        $user = $request
            ->user()
            ->load([
                'role',
                'alumniOne.major',
                'alumniOne.employment',
                'alumniOne.eventRegistrations'
            ]);



        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully.',

            'data' => [
                'user' => $this->userData($user),
            ],
        ], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
        ], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | LOGOUT ALL DEVICES
    |--------------------------------------------------------------------------
    */

    public function logoutAll(Request $request)
    {
        $request
            ->user()
            ->tokens()
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices.',
        ], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTER ALUMNI
    |--------------------------------------------------------------------------
    */





    public function register(Request $request)
    {
        $phoneRegex = '/^(0|\+855)[0-9]{7,10}$/';

        $validated = $request->validate([
            'full_name_kh' => [
                'nullable',
                'string',
                'max:255',
            ],

            'full_name_en' => [
                'required',
                'string',
                'max:255',
            ],

            'contact' => [
                'required',
                'string',
                'max:255',

                function ($attribute, $value, $fail) use ($phoneRegex) {
                    $isEmail = filter_var(
                        $value,
                        FILTER_VALIDATE_EMAIL
                    );

                    $isPhone = preg_match(
                        $phoneRegex,
                        $value
                    );

                    if (!$isEmail && !$isPhone) {
                        $fail(
                            'The contact field must be a valid email address or phone number.'
                        );
                    }
                },
            ],

            'major_id' => [
                'nullable',
                'exists:majors,id',
            ],

            'graduation_year' => [
                'nullable',
                'integer',
                'min:1980',
                'max:' . date('Y'),
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Determine Email / Phone
    |--------------------------------------------------------------------------
    */

        $isEmail = (bool) filter_var(
            $validated['contact'],
            FILTER_VALIDATE_EMAIL
        );

        $contactColumn = $isEmail
            ? 'email'
            : 'mobile';

        /*
    |--------------------------------------------------------------------------
    | Check Duplicate
    |--------------------------------------------------------------------------
    */

        if (
            User::where(
                $contactColumn,
                $validated['contact']
            )->exists()
        ) {
            throw ValidationException::withMessages([
                'contact' =>
                'This ' .
                    ($isEmail ? 'email' : 'phone number') .
                    ' is already registered.',
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Get Alumni Role
    |--------------------------------------------------------------------------
    */

        $role = Role::where('name', 'alumni')
            ->where('guard_name', 'web')
            ->first();

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Alumni role not found.',
            ], 500);
        }

        /*
    |--------------------------------------------------------------------------
    | Create User + Alumni
    |--------------------------------------------------------------------------
    */

        $user = DB::transaction(function () use (
            $validated,
            $isEmail,
            $role
        ) {
            $user = User::create([
                'name_khmer' => $validated['full_name_kh'] ?? null,

                'name_english' => $validated['full_name_en'],

                /*
             * Your custom role_id
             */
                'role_id' => $role->id,

                'email' => $isEmail
                    ? $validated['contact']
                    : null,

                'mobile' => !$isEmail
                    ? $validated['contact']
                    : null,

                'status' => 'active',

                'password' => Hash::make(
                    $validated['password']
                ),
            ]);

            /*
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | Assign Spatie Role
        |--------------------------------------------------------------------------
        */

            $user->assignRole($role);

            /*
        |--------------------------------------------------------------------------
        | Create Alumni Profile
        |--------------------------------------------------------------------------
        */

            if (
                !empty($validated['major_id']) ||
                !empty($validated['graduation_year'])
            ) {
                Alumni::create([
                    'user_id' => $user->id,

                    'major_id' =>
                    $validated['major_id'] ?? null,

                    'graduation_year' =>
                    $validated['graduation_year'] ?? null,
                ]);
            }

            return $user;
        });

        /*
    |--------------------------------------------------------------------------
    | Load Relationships
    |--------------------------------------------------------------------------
    */

        $user->load([
            'role',
            'alumniOne',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,

            'message' => 'Registration successful.',

            'data' => [
                'user' => $this->userData($user),
            ],
        ], 201);
    }




    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT USER RESPONSE
    |--------------------------------------------------------------------------
    */

    private function userData(User $user): array
    {
        return [
            'id' => $user->id,

            'name_khmer' => $user->name_khmer,

            'name_english' => $user->name_english,

            'email' => $user->email,

            'mobile' => $user->mobile,

            'status' => $user->status,

            'avatar' => $user->avatar,

            'role' => $user->role
                ? [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                ]
                : null,

            'alumni' => $user->role->name == 'alumni' ? $user->alumniOne : null,
        ];
    }




    /*
    |------------------------------------------------------------------------------
    |------------------------------------------------------------------------------
    */

    public function alumniIndex(Request $request)
    {
        $query = Alumni::query()
            ->with([
                'user',
                'major',
                'employment',
            ]);

        // 1. Unified Search Input
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name_english', 'like', "%{$search}%")
                        ->orWhere('name_khmer', 'like', "%{$search}%");
                })
                    ->orWhere('bio', 'like', "%{$search}%")
                    ->orWhere('graduation_year', 'like', "%{$search}%"); // Added search by year
            });
        }

        // 2. Filter by Major
        if ($request->filled('major_id') && (int) $request->major_id !== 0) {
            $query->where('major_id', $request->major_id);
        }

        // 3. Filter by Employment / Work Status
        if ($request->filled('employment_status')) {
            $query->where('employment_status', $request->employment_status);
        }



        $alumni = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'message' => 'Alumni list retrieved successfully.',
            'data' => $alumni,
        ], 200);
    }

    public function alumniShow($id)
    {
        $alumni = Alumni::query()
            ->with([
                'user',
                'major',
                'employment.company',
                'eventRegistrations.event',
            ])
            ->findOrFail($id);


        $statusHistory = ActivityLogs::where('subject_type', Alumni::class)
            ->where('subject_id', $alumni->id)
            ->where('log_name', 'employment_status')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Alumni profile retrieved successfully.',
            'data' => $alumni,
        ], 200);
    }

    public function alumniStore(Request $request)
    {
        // =========================================================
        // 1. Validate Request
        // =========================================================

        $validated = $request->validate([

            // -----------------------------------------------------
            // User
            // -----------------------------------------------------

            'name_khmer' => [
                'required',
                'string',
                'max:255',
            ],

            'name_english' => [
                'required',
                'string',
                'max:255',
            ],

            // Email OR Mobile
            'email' => [
                'nullable',
                'email',
                'unique:users,email',
                'required_without:mobile',
            ],

            'mobile' => [
                'nullable',
                'string',
                'max:30',
                'required_without:email',
                'unique:users,mobile',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            // -----------------------------------------------------
            // Alumni
            // -----------------------------------------------------

            'major_id' => [
                'required',
                'exists:majors,id',
            ],

            'graduation_year' => [
                'required',
                'integer',
                'between:1900,2100',
            ],

            'gpa' => [
                'nullable',
                'numeric',
                'min:0',
                'max:4',
            ],

            'gender' => [
                'required',
                'string',
                'in:male,female',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'bio' => [
                'nullable',
                'string',
            ],

            // -----------------------------------------------------
            // Social Links
            // -----------------------------------------------------

            'linkedin_url' => [
                'nullable',
                'url',
            ],

            'facebook_url' => [
                'nullable',
                'url',
            ],

            // -----------------------------------------------------
            // Employment Status
            // -----------------------------------------------------

            'employment_status' => [
                'required',
                'string',
                'in:employed,unemployed,self_employed,studying,unknown',
            ],

            // -----------------------------------------------------
            // Employment
            // -----------------------------------------------------

            'company_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'job_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'employment_type' => [
                'nullable',
                'string',
                'in:full_time,part_time,contract,internship,self_employed',
            ],

            'industry' => [
                'nullable',
                'string',
                'max:255',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'salary_range' => [
                'nullable',
                'string',
                'max:100',
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'is_current' => [
                'nullable',
                'boolean',
            ],

            // -----------------------------------------------------
            // Profile Image
            // -----------------------------------------------------

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        // =========================================================
        // 2. Find Alumni Role
        // =========================================================

        $role = Role::where('name', 'alumni')
            ->where('guard_name', 'web')
            ->first();

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Alumni role not found.',
            ], 404);
        }

        // =========================================================
        // 3. Handle Profile Image
        // =========================================================

        $imagePath = null;

        if ($request->hasFile('image')) {

            $image = $request->file('image');

            $uploadDirectory = public_path('uploads/alumni');

            // Create directory if it does not exist
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0755, true);
            }

            $filename = uniqid('alumni_')
                . '.'
                . $image->getClientOriginalExtension();

            $image->move(
                $uploadDirectory,
                $filename
            );

            $imagePath = 'uploads/alumni/' . $filename;
        }

        // =========================================================
        // 4. Database Transaction
        // =========================================================

        DB::beginTransaction();

        try {

            // =====================================================
            // 4.1 Create User
            // =====================================================

            $user = User::create([
                'name_khmer' => $request->name_khmer,

                'name_english' => $request->name_english,

                'email' => $request->filled('email')
                    ? $request->email
                    : null,

                'mobile' => $request->filled('mobile')
                    ? $request->mobile
                    : null,

                'password' => Hash::make(
                    $request->password
                ),

                'role_id' => $role->id,

                'status' => 'active',
            ]);

            $user->assignRole($role);


            // =====================================================
            // 4.2 Create Alumni
            // =====================================================

            $alumni = Alumni::create([
                'user_id' => $user->id,

                'major_id' => $request->major_id,

                'graduation_year' => $request->graduation_year,

                'gpa' => $request->gpa,

                'gender' => $request->gender,

                'dob' => $request->date_of_birth,

                'address' => $request->address,

                'bio' => $request->bio,

                'linkedin_url' => $request->linkedin_url,

                'facebook_url' => $request->facebook_url,

                'profile_photo' => $imagePath,

                'employment_status' => $request->employment_status,
            ]);

            // =====================================================
            // 4.3 Check Employment Data
            //
            // company_name is used instead of company_id
            // =====================================================

            $hasEmploymentData =
                $request->filled('company_name') ||
                $request->filled('job_title') ||
                $request->filled('employment_type') ||
                $request->filled('industry') ||
                $request->filled('location') ||
                $request->filled('salary_range') ||
                $request->filled('start_date') ||
                $request->filled('end_date') ||
                $request->boolean('is_current');

            // =====================================================
            // 4.4 Create Employment
            //
            // Only create employment when:
            // - employment_status = employed
            // - At least one employment field exists
            // =====================================================

            if (
                $request->employment_status === 'employed'
                && $hasEmploymentData
            ) {

                Employments::create([

                    'alumni_id' => $alumni->id,

                    // Company name instead of company_id
                    'company_name' => $request->company_name,

                    'job_title' => $request->job_title,

                    'employment_type' => $request->employment_type,

                    'industry' => $request->industry,

                    'location' => $request->location,

                    'salary_range' => $request->salary_range,

                    'start_date' => $request->start_date,

                    // If current employment:
                    // end_date must be NULL
                    'end_date' => $request->boolean('is_current')
                        ? null
                        : $request->end_date,

                    'is_current' => $request->boolean('is_current'),
                ]);
            }

            // =====================================================
            // 5. Commit Transaction
            // =====================================================

            DB::commit();

            // =====================================================
            // 6. Load Relationships
            // =====================================================

            $alumni->load([
                'user.role',
                'major',
                'employment',
            ]);

            // =====================================================
            // 7. Response
            // =====================================================

            return response()->json([
                'success' => true,

                'message' => 'Alumni created successfully.',

                'data' => $alumni,
            ], 201);
        } catch (\Throwable $e) {

            // =====================================================
            // Rollback Transaction
            // =====================================================

            DB::rollBack();

            // =====================================================
            // Delete Uploaded Image
            // =====================================================

            if (
                $imagePath &&
                file_exists(public_path($imagePath))
            ) {
                unlink(public_path($imagePath));
            }

            // =====================================================
            // Error Response
            // =====================================================

            return response()->json([
                'success' => false,

                'message' => 'Failed to create alumni.',

                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function alumniUpdate(Request $request, $id) {}

    /*
    |---------------------------------------
    |---------------------------------------
    */


    public function alumniUpdateState(Request $request, $id)
    {
        // 1. Find Alumni Record
        $alumni = Alumni::with([
            'user.role',
            'major',
            'employment',
        ])->find($id);

        if (!$alumni) {
            return response()->json([
                'status' => 'error',
                'message' => 'Alumni not found.'
            ], 404);
        }

        // 2. Validate Input Data
        $validator = Validator::make($request->all(), [
            'employment_status' => 'required|string|in:employed,unemployed,self_employed,studying,unknown',
            'bio' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        // 3. Update Record using Transaction
        try {
            DB::beginTransaction();

            $alumni->update([
                'employment_status' => $request->employment_status,
                'bio' => $request->bio ?? $alumni->bio,
            ]);

            // (Optional) បើសិនជាអ្នកមាន table សម្រាប់រក្សាទុក History
            /*
            if ($request->filled('notes')) {
                $alumni->statusHistories()->create([
                    'status' => $request->employment_status,
                    'notes' => $request->notes,
                    'changed_by' => auth()->user()->name ?? 'System',
                ]);
            }
            */

            DB::commit();

            // 4. Return Updated Data Response
            return response()->json([
                'status' => 'success',
                'message' => 'Alumni status updated successfully.',
                'data' => $alumni->fresh([
                    'user.role',
                    'major',
                    'employment'
                ])
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update alumni status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function alumniDestroy($id)
    {
        try {
            $alumni = Alumni::find($id);

            if (!$alumni) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alumni not found.',
                ], 404);
            }

            $user = $alumni->user;

            $alumni->delete();

            if ($user) {
                $user->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Alumni deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete alumni.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
