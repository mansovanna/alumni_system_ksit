<?php

namespace App\Http\Controllers;

use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    //


    /* ------------------- List ------------------- */
    public function index(Request $request)
    {
        $query = Events::query()->withCount(['registrations']);

        if ($status = $request->query('status')) {
            $query->status($status);
        }

        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $events = $query
            ->orderBy('start_date', 'desc')
            ->paginate($request->query('per_page', 12));

        return response()->json([
            'success' => true,
            'message' => 'Events retrieved successfully.',
            'data' => $events,
        ]);
    }

    /* ------------------- Show ------------------- */
    public function show(Events $event)
    {

        $event->load([
            'creator',
            'agendas',
            'speakers',
        ]);

        $event->loadCount('registrations');

        return response()->json([
            'success' => true,
            'message' => 'Event retrieved successfully.',
            'data' => $event,
        ]);
    }

    /* ------------------- Store ------------------- */
    public function store(Request $request)
    {
        $validated = $request->validate([
            /* -------------------------------------------------- */
            /* Basic Event Information                           */
            /* -------------------------------------------------- */

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'event_type' => [
                'required',
                Rule::in([
                    'job_fair',
                    'reunion',
                    'workshop',
                    'seminar',
                    'other',
                ]),
            ],

            'banner_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:5120',
            ],

            'location' => [
                'required',
                'string',
                'max:255',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],

            /* -------------------------------------------------- */
            /* Registration Settings                             */
            /* -------------------------------------------------- */

            'registration_deadline' => [
                'nullable',
                'date',
                'before_or_equal:start_date',
            ],

            'max_participants' => [
                'required',
                'integer',
                'min:1',
            ],

            'external_link' => [
                'nullable',
                'url',
                'max:2048',
            ],

            /* -------------------------------------------------- */
            /* Status                                             */
            /* -------------------------------------------------- */

            'status' => [
                'nullable',
                Rule::in([
                    'draft',
                    'published',
                    'ongoing',
                    'completed',
                    'cancelled',
                ]),
            ],

            /* -------------------------------------------------- */
            /* Agenda                                             */
            /* -------------------------------------------------- */

            'agenda' => [
                'nullable',
                'array',
            ],

            'agenda.*.time' => [
                'required',
                'date_format:H:i',
            ],

            'agenda.*.title' => [
                'required',
                'string',
                'max:255',
            ],

            /* -------------------------------------------------- */
            /* Speakers                                           */
            /* -------------------------------------------------- */

            'speakers' => [
                'nullable',
                'array',
            ],

            'speakers.*.name' => [
                'required',
                'string',
                'max:255',
            ],

            'speakers.*.title' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        try {
            $event = DB::transaction(function () use ($request, $validated) {

                /* ---------------------------------------------- */
                /* Created By                                     */
                /* ---------------------------------------------- */

                $validated['created_by'] = $request->user()->id;

                $validated['status'] = $validated['status'] ?? 'draft';

                /* ---------------------------------------------- */
                /* Banner Image                                   */
                /* ---------------------------------------------- */

                if ($request->hasFile('banner_image')) {
                    $file = $request->file('banner_image');

                    $fileName = time()
                        . '_'
                        . uniqid()
                        . '.'
                        . $file->getClientOriginalExtension();

                    $file->move(
                        public_path('events'),
                        $fileName
                    );

                    $validated['banner_image'] = 'events/' . $fileName;
                }

                /* ---------------------------------------------- */
                /* Remove Nested Data Before Event::create()     */
                /* ---------------------------------------------- */

                $agenda = $validated['agenda'] ?? [];
                $speakers = $validated['speakers'] ?? [];

                unset(
                    $validated['agenda'],
                    $validated['speakers']
                );

                /* ---------------------------------------------- */
                /* Create Event                                  */
                /* ---------------------------------------------- */

                $event = Events::create($validated);

                /* ---------------------------------------------- */
                /* Create Agenda                                 */
                /* ---------------------------------------------- */

                if (!empty($agenda)) {
                    foreach ($agenda as $item) {
                        $event->agendas()->create([
                            'time' => $item['time'],
                            'title' => $item['title'],
                        ]);
                    }
                }

                /* ---------------------------------------------- */
                /* Create Speakers                               */
                /* ---------------------------------------------- */

                if (!empty($speakers)) {
                    foreach ($speakers as $speaker) {
                        $event->speakers()->create([
                            'name' => $speaker['name'],
                            'professional_title' => $speaker['title'],
                        ]);
                    }
                }

                return $event->load([
                    'creator',
                    'agendas',
                    'speakers',
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Event created successfully.',
                'data' => $event,
            ], 201);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to create event.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /* ------------------- Update ------------------- */
    /* ------------------- Update ------------------- */
    public function update(Request $request, Events $event)
    {
        $validated = $request->validate([
            /* -------------------------------------------------- */
            /* Basic Information                                  */
            /* -------------------------------------------------- */

            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'event_type' => [
                'nullable',
                Rule::in([
                    'job_fair',
                    'reunion',
                    'workshop',
                    'seminar',
                    'other',
                ]),
            ],

            'banner_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:5120',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            /* -------------------------------------------------- */
            /* Date & Time                                        */
            /* -------------------------------------------------- */

            'start_date' => [
                'sometimes',
                'required',
                'date',
            ],

            'end_date' => [
                'sometimes',
                'required',
                'date',
                'after_or_equal:start_date',
            ],

            'registration_deadline' => [
                'nullable',
                'date',
                'before_or_equal:start_date',
            ],

            /* -------------------------------------------------- */
            /* Registration                                      */
            /* -------------------------------------------------- */

            'max_participants' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
            ],

            'external_link' => [
                'nullable',
                'url',
                'max:2048',
            ],

            /* -------------------------------------------------- */
            /* Status                                             */
            /* -------------------------------------------------- */

            'status' => [
                'nullable',
                Rule::in([
                    'draft',
                    'published',
                    'ongoing',
                    'completed',
                    'cancelled',
                ]),
            ],

            /* -------------------------------------------------- */
            /* Agenda                                             */
            /* -------------------------------------------------- */

            'agenda' => [
                'nullable',
                'array',
            ],

            'agenda.*.time' => [
                'required',
                'date_format:H:i',
            ],

            'agenda.*.title' => [
                'required',
                'string',
                'max:255',
            ],

            /* -------------------------------------------------- */
            /* Speakers                                           */
            /* -------------------------------------------------- */

            'speakers' => [
                'nullable',
                'array',
            ],

            'speakers.*.name' => [
                'required',
                'string',
                'max:255',
            ],

            'speakers.*.professional_title' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        try {
            $event = DB::transaction(function () use ($request, $event, $validated) {

                /* ---------------------------------------------- */
                /* Banner Image                                   */
                /* ---------------------------------------------- */

                if ($request->hasFile('banner_image')) {

                    // Delete old image
                    if ($event->banner_image) {
                        $oldImage = public_path($event->banner_image);

                        if (file_exists($oldImage)) {
                            unlink($oldImage);
                        }
                    }

                    $file = $request->file('banner_image');

                    $fileName = time()
                        . '_'
                        . uniqid()
                        . '.'
                        . $file->getClientOriginalExtension();

                    $file->move(
                        public_path('events'),
                        $fileName
                    );

                    $validated['banner_image'] = 'events/' . $fileName;
                }

                /* ---------------------------------------------- */
                /* Nested Data                                    */
                /* ---------------------------------------------- */

                $agenda = $validated['agenda'] ?? null;
                $speakers = $validated['speakers'] ?? null;

                unset(
                    $validated['agenda'],
                    $validated['speakers']
                );

                /* ---------------------------------------------- */
                /* Update Event                                   */
                /* ---------------------------------------------- */

                $event->update($validated);

                /* ---------------------------------------------- */
                /* Update Agenda                                  */
                /* ---------------------------------------------- */

                if ($agenda !== null) {

                    // Delete old agenda
                    $event->agendas()->delete();

                    // Create new agenda
                    foreach ($agenda as $item) {

                        $event->agendas()->create([
                            'time' => $item['time'],
                            'title' => $item['title'],
                        ]);
                    }
                }

                /* ---------------------------------------------- */
                /* Update Speakers                                */
                /* ---------------------------------------------- */

                if ($speakers !== null) {

                    // Delete old speakers
                    $event->speakers()->delete();

                    // Create new speakers
                    foreach ($speakers as $speaker) {

                        $event->speakers()->create([
                            'name' => $speaker['name'],
                            'professional_title' => $speaker['professional_title'],
                        ]);
                    }
                }

                /* ---------------------------------------------- */
                /* Return Fresh Data                               */
                /* ---------------------------------------------- */

                return $event->fresh()->load([
                    'creator',
                    'agendas',
                    'speakers',
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully.',
                'data' => $event,
            ], 200);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update event.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /* ------------------- Delete ------------------- */
    public function destroy(Events $event)
    {
        // Delete banner image
        if ($event->banner_image) {
            $imagePath = public_path($event->banner_image);

            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        // Delete event
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully.',
        ]);
    }
}
