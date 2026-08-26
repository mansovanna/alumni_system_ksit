<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Notifications;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $perPage = $request->perPage ?? 15;

        $query = Notifications::query();

       $totalAlumni = Alumni::count();

        $years = Alumni::query()
            ->whereNotNull('graduation_year')
            ->distinct()
            ->orderByDesc('graduation_year')
            ->pluck('graduation_year');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $notifications = $query
            ->latest('created_at')
            ->paginate($perPage);

       return response()->json([
            'success' => true,
            'message' => 'Notifications retrieved successfully.',
            'data' => $notifications,
            'totalAlumni' => $totalAlumni,
            'years' =>$years
        ]);
    }


    /**
     * Store a newly created notification.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'message' => [
                'required',
                'string',
            ],

            'type' => [
                'required',
                'string',
                'max:100',
            ],

            'filters' => [
                'nullable',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create notification
        |--------------------------------------------------------------------------
        */

        $notification = Notifications::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'],
            'filters' => $validated['filters'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification created successfully.',
            'data' => $notification,
        ], 201);
    }


    /**
     * Display the specified notification.
     */
    public function show($id)
    {
        $notification = Notifications::find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification retrieved successfully.',
            'data' => $notification,
        ]);
    }


    /**
     * Update the specified notification.
     */
    public function update(Request $request, $id)
    {
        $notification = Notifications::find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        $validated = $request->validate([
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'message' => [
                'sometimes',
                'required',
                'string',
            ],

            'type' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],

            'filters' => [
                'nullable',
            ],
        ]);

        $notification->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Notification updated successfully.',
            'data' => $notification->fresh(),
        ]);
    }


    /**
     * Mark notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Notifications::find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->update([
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read successfully.',
            'data' => $notification->fresh(),
        ]);
    }


    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Notifications::query()->update([
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read successfully.',
        ]);
    }


    /**
     * Remove the specified notification.
     */
    public function destroy($id)
    {
        $notification = Notifications::find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully.',
        ]);
    }
}
