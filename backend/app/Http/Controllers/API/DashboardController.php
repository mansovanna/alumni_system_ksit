<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Major;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //

    public function index(Request $request)
    {
        $year = $request->query('year');
        $majorId = $request->query('major');

        $base = Alumni::query();
        if ($year && $year !== 'all') {
            $base->where('graduation_year', $year);
        }
        if ($majorId && $majorId !== 'all') {
            $base->where('major_id', $majorId);
        }

        // --- Status counts (this period) ---
        $counts = (clone $base)
            ->select('employment_status', DB::raw('count(*) as total'))
            ->groupBy('employment_status')
            ->pluck('total', 'employment_status');

        $employed = ($counts['employed'] ?? 0) + ($counts['self_employed'] ?? 0);
        $seeking = $counts['unemployed'] ?? 0;
        $studying = $counts['studying'] ?? 0;
        $unemployed = $counts['unknown'] ?? 0;

        // --- Same counts, last year (for % change) ---
        $lastYear = (int) date('Y') - 1;
        $prevBase = Alumni::query()->where('graduation_year', $lastYear);
        if ($majorId && $majorId !== 'all') {
            $prevBase->where('major_id', $majorId);
        }
        $prevCounts = $prevBase
            ->select('employment_status', DB::raw('count(*) as total'))
            ->groupBy('employment_status')
            ->pluck('total', 'employment_status');

        $pctChange = function ($current, $prev) {
            if (!$prev)
                return null;
            return round((($current - $prev) / $prev) * 100, 1);
        };

        $summary = [
            'employed' => $employed,
            'seeking' => $seeking,
            'studying' => $studying,
            'unemployed' => $unemployed,
            'employed_change' => $pctChange($employed, ($prevCounts['employed'] ?? 0) + ($prevCounts['self_employed'] ?? 0)),
            'seeking_change' => $pctChange($seeking, $prevCounts['unemployed'] ?? 0),
            'studying_change' => $pctChange($studying, $prevCounts['studying'] ?? 0),
            'unemployed_change' => $pctChange($unemployed, $prevCounts['unknown'] ?? 0),
        ];

        // --- Employment by graduation year (for bar chart) ---
        $employedByYear = (clone $base)
            ->whereIn('employment_status', ['employed', 'self_employed'])
            ->select('graduation_year', DB::raw('count(*) as total'))
            ->groupBy('graduation_year')
            ->orderBy('graduation_year')
            ->get()
            ->map(fn($r) => ['year' => $r->graduation_year, 'count' => $r->total]);

        // --- Employment rate trend, last 5 years ---
        $currentYear = (int) date('Y');
        $totalByYear = (clone $base)
            ->select('graduation_year', DB::raw('count(*) as total'))
            ->groupBy('graduation_year')
            ->pluck('total', 'graduation_year');
        $employedByYearMap = (clone $base)
            ->whereIn('employment_status', ['employed', 'self_employed'])
            ->select('graduation_year', DB::raw('count(*) as total'))
            ->groupBy('graduation_year')
            ->pluck('total', 'graduation_year');

        $trend = [];
        for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
            $emp = $employedByYearMap[$y] ?? 0;
            $tot = $totalByYear[$y] ?? 0;
            $trend[] = [
                'year' => $y,
                'rate' => $tot > 0 ? round(($emp / $tot) * 100, 1) : 0,
            ];
        }

        // --- Recent updates ---
        $recentUpdates = (clone $base)
            ->with(['user:id,name_english', 'major:id,name'])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get(['id', 'user_id', 'major_id', 'employment_status', 'updated_at'])
            ->map(fn($a) => [
                'name' => $a->user->name_english ?? 'Unknown',
                'degree' => $a->major->name ?? '',
                'status' => ucfirst(str_replace('_', ' ', $a->employment_status)),
            ]);

        // --- Filter options (for dropdowns) ---
        $availableYears = Alumni::query()
            ->select('graduation_year')
            ->distinct()
            ->orderByDesc('graduation_year')
            ->pluck('graduation_year');

        $majors = Major::select('id', 'name as title')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard overview retrieved successfully.',
            'data' => [
                'summary' => $summary,
                'employment_by_year' => $employedByYear,
                'employment_rate_trend' => $trend,
                'recent_updates' => $recentUpdates,
                'available_years' => $availableYears,
                'majors' => $majors,
                'total_alumni' => Alumni::count(),
            ],
        ]);

    }
}


