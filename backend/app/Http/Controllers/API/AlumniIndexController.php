<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Employments;
use App\Models\Event;
use App\Models\EventRegistrations;
use App\Models\Events;
use App\Models\Notification;
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlumniIndexController extends Controller
{
    /**
     * Alumni Dashboard
     *
     * GET /api/v1/alumni
     */
    public function index(Request $request)
    {
        $user = $request->user()->load([
            'alumniOne.major',
            'alumniOne.employment',
            'alumniOne.eventRegistrations',
        ]);

        $alumniInfo = $user->alumniOne;

        $events = Events::withExists([
            'registrations as is_registered' => function ($query) use ($alumniInfo) {
                $query->where('alumni_id', $alumniInfo?->id);
            }
        ])
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->limit(4)
            ->get();

        $notifications = Notifications::query()
            ->where(function ($query) use ($alumniInfo) {
                // Untargeted broadcasts (no filters set) always show
                $query->whereNull('filters')
                    ->orWhere('filters', '[]')
                    ->orWhere('filters', '{}');

                if ($alumniInfo) {
                    if ($alumniInfo->graduation_year) {
                        $query->orWhereJsonContains('filters->graduation_year', (string) $alumniInfo->graduation_year);
                    }

                    if ($alumniInfo->major) {
                        $query->orWhereJsonContains('filters->major', $alumniInfo->major->name);
                    }

                    if ($alumniInfo->employment_status) {
                        $query->orWhereJsonContains('filters->employment_status', $alumniInfo->employment_status);
                    }
                }
            })
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Alumni dashboard retrieved successfully.',
            'data' => [
                'alumni' => [
                    'id' => $user->id,
                    'name_khmer' => $user->name_khmer,
                    'name_english' => $user->name_english,
                    'mobile' => $user->mobile,
                    'email' => $user->email,
                    'status' => $user->status,
                    'avatar' => $user->avatar ?? null,
                    'profile_url' => $user->profile_url ?? null,
                    'alumni_info' => $alumniInfo,
                ],
                'notifications' => $notifications,
                'events' => $events,
            ]
        ]);
    }

    /**
     * Get Alumni Career Status
     *
     * GET /api/v1/alumni/update-status
     */
    public function updateStatus(Request $request, $id)
    {
        $alumni = Alumni::findOrFail($id);

        $validated = $request->validate([
            'bio'              => 'nullable|string',
            'company_name'     => 'nullable|integer',
            'job_title'        => 'nullable|string|max:255',
            'employment_type'  => 'nullable|in:full_time,part_time,contract,internship,self_employed',
            'industry'         => 'nullable|string|max:255',
            'location'         => 'nullable|string|max:255',
            'salary_range'     => 'nullable|string|max:255',
            'is_current'       => 'nullable|boolean',
        ]);

        $alumni->update([
            'bio' => $validated['bio'] ?? $alumni->bio,
        ]);

        $employment = $alumni->employment; // hasOne, alumni_id is unique
        $employmentFields = collect($validated)->except('bio')->toArray(); // keep nulls this time

        if ($employment) {
            $isJobChange = ($validated['company_name'] ?? null) !== $employment->company_name
                || ($validated['job_title'] ?? null) !== $employment->job_title;

            if ($isJobChange) {
                $employmentFields['start_date'] = now();
                $employmentFields['end_date'] = null;
                $employmentFields['is_current'] = true;
            }

            $employment->update($employmentFields);
        } else {
            // no employment row yet for this alumni — create one, even if all fields are null
            $alumni->employment()->create($employmentFields);
        }

        $alumni->load('employment');

        return response()->json([
            'success' => true,
            'message' => 'Alumni status updated successfully.',
            'data' => [
                'id' => $alumni->id,
                'bio' => $alumni->bio,
                'employment' => $alumni->employment,
            ],
        ]);
    }

    /**
     * Event Details
     *
     * GET /api/v1/alumni/events/{id}
     */
    public function eventDetails(Request $request, int $id)
    {
        /*
    |--------------------------------------------------------------------------
    | Get Current User
    |--------------------------------------------------------------------------
    */

        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Get Alumni Profile
    |--------------------------------------------------------------------------
    */

        $alumni = $user->alumni()->first();

        if (!$alumni) {
            return response()->json([
                'success' => false,
                'message' => 'Alumni profile not found.',
            ], 404);
        }

        $alumniId = $alumni->id;

        /*
    |--------------------------------------------------------------------------
    | Get Event
    |--------------------------------------------------------------------------
    */

        $event = Events::query()
            ->with([
                'creator',
                'agendas',
                'registrations' => function ($query) use ($alumniId) {
                    $query->where('alumni_id', $alumniId);
                },
            ])
            ->where('status', '!=', 'draft')
            ->find($id);

        /*
    |--------------------------------------------------------------------------
    | Event Not Found
    |--------------------------------------------------------------------------
    */

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found.',
            ], 404);
        }

        /*
    |--------------------------------------------------------------------------
    | Check Registration
    |--------------------------------------------------------------------------
    */

        $isRegistered = $event->registrations->isNotEmpty();

        /*
    |--------------------------------------------------------------------------
    | Get Registration Data
    |--------------------------------------------------------------------------
    */

        $registration = $event->registrations->first();

        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,
            'message' => 'Event retrieved successfully.',

            'data' => [
                'id' => $event->id,
                'created_by' => $event->created_by,

                'title' => $event->title,
                'description' => $event->description,
                'event_type' => $event->event_type,

                'banner_image' => $event->banner_image
                    ? asset($event->banner_image)
                    : null,

                'location' => $event->location,

                'start_date' => $event->start_date,
                'end_date' => $event->end_date,

                'registration_deadline' =>
                $event->registration_deadline,

                'max_participants' =>
                $event->max_participants,

                'external_link' =>
                $event->external_link,

                'status' => $event->status,

                /*
            |--------------------------------------------------------------------------
            | Registration
            |--------------------------------------------------------------------------
            */

                'is_registered' => $isRegistered,

                'registration' => $registration
                    ? [
                        'id' => $registration->id,
                        'event_id' => $registration->event_id,
                        'alumni_id' => $registration->alumni_id,
                        'registered_at' => $registration->registered_at,
                        'attendance_status' => $registration->attendance_status,
                    ]
                    : null,

                /*
            |--------------------------------------------------------------------------
            | Agenda
            |--------------------------------------------------------------------------
            */

                'agendas' => $event->agendas,

                /*
            |--------------------------------------------------------------------------
            | Creator
            |--------------------------------------------------------------------------
            */

                'creator' => $event->creator,

                /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

                'created_at' => $event->created_at,
                'updated_at' => $event->updated_at,
            ],
        ]);
    }

    /**
     * Register Alumni for Event
     *
     * POST /api/v1/alumni/events-register/{id}
     */


    /**
     * Notification Details
     *
     * GET /api/v1/alumni/notification/{id}
     */
    public function notificationDetails(
        Request $request,
        int $id
    ) {
        $notification = Notifications::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Mark as Read
        |--------------------------------------------------------------------------
        */

        if (
            isset($notification->is_read) &&
            !$notification->is_read
        ) {
            $notification->update([
                'is_read' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification retrieved successfully.',

            'data' => [
                'id' => $notification->id,
                'title' => $notification->title ?? null,
                'message' => $notification->message ?? null,
                'is_read' => $notification->is_read ?? false,
                'created_at' => $notification->created_at,
                'updated_at' => $notification->updated_at,
            ],
        ]);
    }

    /**
     * Calculate Network Strength
     */
    private function calculateNetworkStrength(
        int $connections,
        int $mentoring
    ): int {
        /*
        |--------------------------------------------------------------------------
        | Simple calculation
        |--------------------------------------------------------------------------
        |
        | You can change this formula later when the real
        | connection / mentoring system is implemented.
        |
        */

        $score = ($connections * 0.5) + ($mentoring * 5);

        return min(100, (int) round($score));
    }

    /**
     * User Data
     */
    private function userData($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ?? null,

            'role' => $user->role?->name,

            'created_at' => $user->created_at,
        ];
    }





    public function alumniDirectory(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

        $graduationYear = $request->input('year');
        $majorId = $request->input('major_id');
        $search = $request->input('search');
        $perPage = (int) $request->input('perPage', 15);

        /*
    |--------------------------------------------------------------------------
    | Current User
    |--------------------------------------------------------------------------
    */

        $userId = $request->user()->id;

        /*
    |--------------------------------------------------------------------------
    | Alumni Query
    |--------------------------------------------------------------------------
    */

        $query = Alumni::query()
            ->with([
                'user',
                'major',
                'employment.company',
            ])
            ->where('user_id', '!=', $userId);

        if ($graduationYear) {
            $query->where('graduation_year', $graduationYear);
        }

        if ($majorId) {
            $query->where('major_id', $majorId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name_english', 'like', "%{$search}%");
                })
                    ->orWhereHas('employment', function ($e) use ($search) {
                        $e->where('job_title', 'like', "%{$search}%");
                    })
                    ->orWhereHas('employment.company', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $alumni = $query
            ->orderBy('graduation_year', 'desc')
            ->paginate($perPage, ['*'], 'page')
            ->withQueryString();

        $graduationYears = Alumni::query()
            ->whereNotNull('graduation_year')
            ->where('user_id', '!=', $userId)
            ->distinct()
            ->orderBy('graduation_year', 'desc')
            ->pluck('graduation_year')
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Alumni directory retrieved successfully.',

            'data' => [
                'graduation_years' => $graduationYears,
                'alumni' => $alumni,
            ],
        ]);
    }






    public function eventListAll(Request $request)
    {
        $filter = $request->input('filter', 'upcoming');
        $perPage = min((int) $request->input('per_page', 6), 50);

        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Get Alumni
    |--------------------------------------------------------------------------
    */

        $alumni = $user->alumni()->first();

        $alumniId = $alumni->id;


        /*
    |--------------------------------------------------------------------------
    | Events Query
    |--------------------------------------------------------------------------
    */

        $query = Events::query()
            ->with([
                'registrations' => function ($query) use ($alumniId) {
                    $query->where('alumni_id', $alumniId);
                }
            ])
            ->where('status', '!=', 'draft');

        /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

        switch ($filter) {

            case 'upcoming':
                $query
                    ->where('start_date', '>=', now())
                    ->orderBy('start_date', 'asc');
                break;

            case 'past':
                $query
                    ->where('start_date', '<', now())
                    ->orderBy('start_date', 'desc');
                break;

            case 'all':
                $query->orderBy('start_date', 'desc');
                break;

            default:
                $query
                    ->where('start_date', '>=', now())
                    ->orderBy('start_date', 'asc');
                break;
        }

        /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

        $events = $query->paginate(
            $perPage,
            ['*'],
            'page',
            $request->input('page', 1)
        );

        /*
    |--------------------------------------------------------------------------
    | Add Registration Status
    |--------------------------------------------------------------------------
    */

        $events->getCollection()->transform(function ($event) {

            $event->is_registered =
                $event->registrations->isNotEmpty();

            unset($event->registrations);

            return $event;
        });

        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,
            'message' => 'Events retrieved successfully.',
            'data' => $events->items(),

            'pagination' => [
                'current_page' => $events->currentPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
                'last_page' => $events->lastPage(),
                'from' => $events->firstItem(),
                'to' => $events->lastItem(),
                'has_more' => $events->hasMorePages(),
            ],
        ]);
    }

    public function eventRegister(Request $request, int $id)
    {
        $user = $request->user();

        // Get alumni profile
        $alumni = $user->alumni()->first();

        if (!$alumni) {
            return response()->json([
                'success' => false,
                'message' => 'Alumni profile not found.',
            ], 404);
        }

        // Find event
        $event = Events::find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found.',
            ], 404);
        }

        /*
    |--------------------------------------------------------------------------
    | Prevent Duplicate Registration
    |--------------------------------------------------------------------------
    */

        $alreadyRegistered = DB::table('event_registrations')
            ->where('alumni_id', $alumni->id)
            ->where('event_id', $event->id)
            ->exists();

        if ($alreadyRegistered) {
            return response()->json([
                'success' => false,
                'message' => 'You have already registered for this event.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */

        DB::table('event_registrations')->insert([
            'alumni_id' => $alumni->id,
            'event_id' => $event->id,
            'registered_at' => now(),
            'attendance_status' => 'registered',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully registered for the event.',
            'data' => [
                'event_id' => $event->id,
                'alumni_id' => $alumni->id,
                'registered_at' => now(),
                'attendance_status' => 'registered',
            ],
        ], 201);
    }




    public function profile(Request $request)
    {
        $userId = $request->user()->id;

        $alumni = Alumni::with(['user', 'major', 'employment', 'eventRegistrations.event.agendas', 'eventRegistrations.event.speakers'])->where('user_id', $userId)->first();

        if (!$alumni) {
            return response()->json([
                'message' => 'Profile not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Profile',
            'data' => $alumni
        ]);
    }

    public function unregister($id)
    {
        $event = EventRegistrations::find($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event registration not found'
            ], 404);
        }

        $event->deleteOrFail();

        return response()->json([
            'message' => 'Unregistered successfully'
        ]);
    }


    public function getProfielID($id)
    {
        $alumni = Alumni::with(['user', 'major', 'employment'])->find($id);

        return response()->json([
            'message' => 'Profile User',
            'data' => $alumni
        ]);
    }

    public function updateProfielID(Request $request, $id)
    {
        $alumni = Alumni::findOrFail($id);

        $validated = $request->validate([
            'address'        => ['nullable', 'string', 'max:255'],
            'bio'            => ['nullable', 'string'],
            'graduation_year' => ['nullable', 'string', 'max:4'],
            'linkedin_url'   => ['nullable', 'string', 'max:255'],
            'facebook_url'   => ['nullable', 'string', 'max:255'],
        ]);

        // Update Alumni profile
        $alumni->update($validated);

        $employment = Employments::where('alumni_id', $alumni->id)->first();

        if (!$employment) {
            $employment = Employments::create([
                'alumni_id' => $alumni->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated',
            'data' => $alumni->load([
                'user',
                'major',
                'employment',
            ]),
        ]);
    }

    public function updateCareer(Request $request)
    {
        $user = $request->user();

        // Get first alumni record
        $alumni = $user->alumni()->first();

        if (!$alumni) {
            return response()->json([
                'success' => false,
                'message' => 'Alumni profile not found.',
            ], 404);
        }

        $validated = $request->validate([
            'employment_type' => [
                'nullable',
                'in:full_time,part_time,contract,internship,self_employed',
            ],

            'job_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'company_name' => [
                'nullable',
                'string',
                'max:255',
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
                'max:255',
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
                'required',
                'boolean',
            ],
        ]);

        // If current job, remove end date
        if ($validated['is_current']) {
            $validated['end_date'] = null;
        }

        try {

            $employment = Employments::updateOrCreate(
                [
                    'alumni_id' => $alumni->id,
                ],
                [
                    'employment_type' => $validated['employment_type'] ?? null,
                    'job_title'      => $validated['job_title'] ?? null,
                    'company_name'   => $validated['company_name'] ?? null,
                    'industry'       => $validated['industry'] ?? null,
                    'location'       => $validated['location'] ?? null,
                    'salary_range'   => $validated['salary_range'] ?? null,
                    'start_date'     => $validated['start_date'] ?? null,
                    'end_date'       => $validated['end_date'] ?? null,
                    'is_current'     => $validated['is_current'],
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Career information updated successfully.',
                'data' => [
                    'employment' => $employment,
                ],
            ], 200);
        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update career information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
