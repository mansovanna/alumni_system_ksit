<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function employmentReport(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. YEAR RANGE
        |--------------------------------------------------------------------------
        | Default:
        | Current year = 2026
        | Start year   = 2024
        | End year     = 2026
        |
        | Example:
        | 2024 - 2026
        |--------------------------------------------------------------------------
        */

        $currentYear = (int) date('Y');

        $yearStart = $request->query(
            'year_start',
            $currentYear - 2
        );

        $yearEnd = $request->query(
            'year_end',
            $currentYear
        );

        $yearStart = (int) $yearStart;
        $yearEnd = (int) $yearEnd;

        /*
        |--------------------------------------------------------------------------
        | 2. OTHER FILTERS
        |--------------------------------------------------------------------------
        */

        $majorFilter = $request->query('major_id', 'all');
        $statusFilter = $request->query('status', 'all');

        /*
        |--------------------------------------------------------------------------
        | 3. BASE QUERY
        |--------------------------------------------------------------------------
        */

        $baseQuery = Alumni::query()
            ->whereBetween('graduation_year', [
                $yearStart,
                $yearEnd,
            ]);

        /*
        | Major filter
        */

        if ($majorFilter !== 'all' && $majorFilter !== null) {
            $baseQuery->where(
                'major_id',
                $majorFilter
            );
        }

        /*
        | Employment status filter
        */

        if ($statusFilter !== 'all' && $statusFilter !== null) {
            $baseQuery->where(
                'employment_status',
                $statusFilter
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. CURRENT STATISTICS
        |--------------------------------------------------------------------------
        */

        $totalGraduates = (clone $baseQuery)->count();

        $statusCounts = (clone $baseQuery)
            ->select(
                'employment_status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('employment_status')
            ->pluck(
                'total',
                'employment_status'
            );

        /*
        | Employed
        */

        $employedCount =
            ($statusCounts['employed'] ?? 0) +
            ($statusCounts['self_employed'] ?? 0);

        /*
        | Studying
        */

        $studyingCount =
            $statusCounts['studying'] ?? 0;

        /*
        | Unemployed
        */

        $unemployedCount =
            $statusCounts['unemployed'] ?? 0;

        /*
        | Seeking
        |
        | Your database currently appears to use "unknown"
        | for seeking.
        */

        $seekingCount =
            $statusCounts['unknown'] ?? 0;

        /*
        |--------------------------------------------------------------------------
        | 5. RATES
        |--------------------------------------------------------------------------
        */

        $employmentRate = $totalGraduates > 0
            ? round(
                ($employedCount / $totalGraduates) * 100,
                1
            )
            : 0;

        $studyingRate = $totalGraduates > 0
            ? round(
                ($studyingCount / $totalGraduates) * 100,
                1
            )
            : 0;

        $seekingRate = $totalGraduates > 0
            ? round(
                ($seekingCount / $totalGraduates) * 100,
                1
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | 6. PREVIOUS YEAR
        |--------------------------------------------------------------------------
        |
        | If selected:
        | 2024 - 2026
        |
        | Previous year for comparison:
        | 2023
        |--------------------------------------------------------------------------
        */

        $previousYearStart = $yearStart - 1;
        $previousYearEnd = $yearEnd - 1;

        $prevBase = Alumni::query()
            ->whereBetween(
                'graduation_year',
                [
                    $previousYearStart,
                    $previousYearEnd,
                ]
            );

        /*
        | Apply same major filter
        */

        if ($majorFilter !== 'all' && $majorFilter !== null) {
            $prevBase->where(
                'major_id',
                $majorFilter
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Previous statistics
        |--------------------------------------------------------------------------
        */

        $prevTotal = (clone $prevBase)->count();

        $prevCounts = (clone $prevBase)
            ->select(
                'employment_status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('employment_status')
            ->pluck(
                'total',
                'employment_status'
            );

        $prevEmployed =
            ($prevCounts['employed'] ?? 0) +
            ($prevCounts['self_employed'] ?? 0);

        $prevStudying =
            $prevCounts['studying'] ?? 0;

        $prevUnemployed =
            $prevCounts['unemployed'] ?? 0;

        $prevEmploymentRate = $prevTotal > 0
            ? round(
                ($prevEmployed / $prevTotal) * 100,
                1
            )
            : 0;

        $prevStudyingRate = $prevTotal > 0
            ? round(
                ($prevStudying / $prevTotal) * 100,
                1
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | 7. TREND CALCULATION
        |--------------------------------------------------------------------------
        */

        $calcDiff = function ($current, $previous) {
            return round(
                $current - $previous,
                1
            );
        };

        $totalDiff = $calcDiff(
            $totalGraduates,
            $prevTotal
        );

        $employmentDiff = $calcDiff(
            $employmentRate,
            $prevEmploymentRate
        );

        $studyingDiff = $calcDiff(
            $studyingRate,
            $prevStudyingRate
        );

        /*
        |--------------------------------------------------------------------------
        | 8. STAT CARDS
        |--------------------------------------------------------------------------
        */

        $stats = [

            'total_graduates' => [
                'value' => number_format(
                    $totalGraduates
                ),

                'trend' =>
                    ($totalDiff >= 0 ? '+' : '') .
                    $totalDiff .
                    '% vs previous period',

                'up' =>
                    $totalGraduates >= $prevTotal,
            ],

            'overall_employment' => [
                'value' =>
                    $employmentRate . '%',

                'trend' =>
                    ($employmentDiff >= 0 ? '+' : '') .
                    $employmentDiff .
                    '% vs previous period',

                'up' =>
                    $employmentRate >= $prevEmploymentRate,
            ],

            'continuing_education' => [
                'value' =>
                    $studyingRate . '%',

                'trend' =>
                    ($studyingDiff >= 0 ? '+' : '') .
                    $studyingDiff .
                    '% vs previous period',

                'up' =>
                    $studyingRate >= $prevStudyingRate,
            ],

            'unemployed' => [
                'value' =>
                    number_format(
                        $unemployedCount
                    ),

                'trend' =>
                    number_format(
                        $unemployedCount
                    ) .
                    ' current period',

                /*
                | Less unemployment = positive
                */

                'up' =>
                    $unemployedCount <= $prevUnemployed,
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | 9. EMPLOYMENT TREND CHART
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | 2024
        | 2025
        | 2026
        |--------------------------------------------------------------------------
        */

        $yearsRange = range(
            $yearStart,
            $yearEnd
        );

        $yearlyStats = Alumni::query()

            ->when(
                $majorFilter !== 'all' && $majorFilter !== null,
                fn($query) =>
                    $query->where(
                        'major_id',
                        $majorFilter
                    )
            )

            ->whereBetween(
                'graduation_year',
                [
                    $yearStart,
                    $yearEnd,
                ]
            )

            ->select(
                'graduation_year',

                DB::raw(
                    'COUNT(*) as total'
                ),

                DB::raw(
                    "
                    SUM(
                        CASE
                            WHEN employment_status IN
                            ('employed', 'self_employed')
                            THEN 1
                            ELSE 0
                        END
                    ) as employed_count
                    "
                )
            )

            ->groupBy(
                'graduation_year'
            )

            ->get()

            ->keyBy(
                'graduation_year'
            );

        $trendChart = [

            'categories' =>
                array_map(
                    'strval',
                    $yearsRange
                ),

            'rates' =>
                array_map(
                    function ($year) use (
                        $yearlyStats
                    ) {

                        $item =
                            $yearlyStats->get(
                                $year
                            );

                        if (
                            !$item ||
                            $item->total == 0
                        ) {
                            return 0;
                        }

                        return round(
                            (
                                $item->employed_count /
                                $item->total
                            ) * 100
                        );
                    },
                    $yearsRange
                ),
        ];

        /*
        |--------------------------------------------------------------------------
        | 10. STATUS DISTRIBUTION
        |--------------------------------------------------------------------------
        */

        $statusDistribution = [

            'total' =>
                number_format(
                    $totalGraduates
                ),

            'series' => [
                $employmentRate,
                $studyingRate,
                $seekingRate,
            ],

            'labels' => [
                'Employed',
                'Studying',
                'Seeking',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | 11. MAJOR REPORT
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | We don't use Major::withCount('alumnis')
        | because your Major model currently doesn't have
        | an alumnis() relationship.
        |
        | Instead, we query Alumni directly.
        |--------------------------------------------------------------------------
        */

        $majorQuery = Alumni::query()
            ->whereBetween(
                'graduation_year',
                [
                    $yearStart,
                    $yearEnd,
                ]
            );

        /*
        | Apply status filter if selected
        */

        if (
            $statusFilter !== 'all' &&
            $statusFilter !== null
        ) {
            $majorQuery->where(
                'employment_status',
                $statusFilter
            );
        }

        /*
        | Get grouped data by major
        */

        $majorStats = (clone $majorQuery)

            ->select(
                'major_id',

                DB::raw(
                    'COUNT(*) as total'
                ),

                DB::raw(
                    "
                    SUM(
                        CASE
                            WHEN employment_status IN
                            ('employed', 'self_employed')
                            THEN 1
                            ELSE 0
                        END
                    ) as employed
                    "
                ),

                DB::raw(
                    "
                    SUM(
                        CASE
                            WHEN employment_status = 'studying'
                            THEN 1
                            ELSE 0
                        END
                    ) as cont
                    "
                ),

                DB::raw(
                    "
                    SUM(
                        CASE
                            WHEN employment_status = 'unemployed'
                            THEN 1
                            ELSE 0
                        END
                    ) as unemployed
                    "
                ),

                DB::raw(
                    "
                    SUM(
                        CASE
                            WHEN employment_status = 'unknown'
                            THEN 1
                            ELSE 0
                        END
                    ) as seeking
                    "
                )
            )

            ->groupBy(
                'major_id'
            )

            ->get()

            ->keyBy(
                'major_id'
            );

        /*
        |--------------------------------------------------------------------------
        | Get all majors
        |--------------------------------------------------------------------------
        */

        $majorsReport = Major::query()

            ->orderBy('name')

            ->get()

            ->map(
                function ($major) use (
                    $majorStats
                ) {

                    $stat =
                        $majorStats->get(
                            $major->id
                        );

                    $total =
                        $stat->total ?? 0;

                    $employed =
                        $stat->employed ?? 0;

                    $cont =
                        $stat->cont ?? 0;

                    $unemployed =
                        $stat->unemployed ?? 0;

                    $seeking =
                        $stat->seeking ?? 0;

                    $rate =
                        $total > 0
                            ? round(
                                (
                                    $employed /
                                    $total
                                ) * 100,
                                1
                            )
                            : 0;

                    return [

                        'dept' =>
                            $major->name,

                        'total' =>
                            (int) $total,

                        'employed' =>
                            (int) $employed,

                        'cont' =>
                            (int) $cont,

                        'unemployed' =>
                            (int) $unemployed,

                        'seeking' =>
                            (int) $seeking,

                        'rate' =>
                            $rate,
                    ];
                }
            );

        /*
        |--------------------------------------------------------------------------
        | 12. FILTER OPTIONS
        |--------------------------------------------------------------------------
        */

        $availableYears = Alumni::query()

            ->select('graduation_year')

            ->distinct()

            ->orderByDesc(
                'graduation_year'
            )

            ->pluck(
                'graduation_year'
            );

        $availableMajors = Major::query()

            ->select(
                'id',
                'name'
            )

            ->orderBy('name')

            ->get();

        /*
        |--------------------------------------------------------------------------
        | 13. RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'data' => [

                /*
                | Current selected range
                */

                'year_range' => [
                    'start' => $yearStart,
                    'end' => $yearEnd,
                ],

                'stats' =>
                    $stats,

                'trend_chart' =>
                    $trendChart,

                'status_distribution' =>
                    $statusDistribution,

                'rows' =>
                    $majorsReport,

                'filters' => [

                    'years' =>
                        $availableYears,

                    'majors' =>
                        $availableMajors,
                ],
            ],
        ]);
    }
}
