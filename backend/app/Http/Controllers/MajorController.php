<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MajorController extends Controller
{
    /**
     * Display a listing of majors.
     *
     * GET /api/majors
     * GET /api/majors?search=computer
     * GET /api/majors?search=computer&page=2&per_page=10
     * GET /api/majors?sort_by=name&sort_order=asc
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $perPage = $request->integer('per_page', 10);

        // Prevent unreasonable pagination values
        $perPage = min(max($perPage, 1), 100);

        $sortBy = $request->input('sort_by', 'created_at');

        $sortOrder = strtolower(
            $request->input('sort_order', 'desc')
        );

        // Allowed sorting columns
        $allowedSortColumns = [
            'id',
            'name',
            'created_at',
            'updated_at',
        ];

        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query = Major::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== null && trim($search) !== '') {
            $search = trim($search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $query->orderBy($sortBy, $sortOrder);

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $majors = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Majors retrieved successfully.',

            'data' => $majors->items(),

            'pagination' => [
                'current_page' => $majors->currentPage(),
                'last_page' => $majors->lastPage(),
                'per_page' => $majors->perPage(),
                'total' => $majors->total(),
                'from' => $majors->firstItem(),
                'to' => $majors->lastItem(),
            ],

            'filters' => [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
        ]);
    }

    /**
     * Store a newly created major.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:majors,name',
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $major = Major::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Major created successfully.',
            'data' => $major,
        ], 201);
    }

    /**
     * Display the specified major.
     */
    public function show(Major $major)
    {
        return response()->json([
            'success' => true,
            'message' => 'Major retrieved successfully.',
            'data' => $major,
        ]);
    }

    /**
     * Update the specified major.
     */
    public function update(Request $request, Major $major)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('majors', 'name')->ignore($major->id),
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $major->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Major updated successfully.',
            'data' => $major->fresh(),
        ]);
    }

    /**
     * Remove the specified major.
     */
    public function destroy(Major $major)
    {
        $major->delete();

        return response()->json([
            'success' => true,
            'message' => 'Major deleted successfully.',
        ]);
    }



    public function majorItems()
    {
        $data = Major::all();

        return response()->json([
            'data' => $data
        ]);
    }
}
