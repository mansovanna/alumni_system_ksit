<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    public function index()
    {

        $data = [];
        // total all alumni
        $users = User::query()->where('role', 'alumni');
        $data['total_alumni'] = $users->count();

        // alumni active
        $data['alumni_active'] = $users->where('status', 'approved')->count();

        // all major or department
        $data['major'] = Major::all()->count();

        // total user by major or deparments
        $data['majors'] = Major::withCount('users')->get();


        // Block graph show all alumni status work
        $data['work'] = UserInfo::get();


        // $alumniTotal = $users->where('role', 'alumni')->count();

        // almuni active
        // $alumniActive = $users->where('s')




        return response()->json([
            'message' => 'Dashboard',
            'data' => $data
        ]);
    }

    public function chartByStatusWork()
    {
        $statuses = [
            'employed',
            'unemployed',
            'seeking',
            'continuing_study'
        ];


        $counts = User::query()
            ->join('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->where('users.role', 'alumni')
            ->selectRaw('user_infos.work as status, COUNT(*) as total')
            ->groupBy('user_infos.work')
            ->pluck('total', 'status');


        $work = [];
        foreach ($statuses as $status) {
            $work[] = [
                'status' => $status,
                'total' => $counts[$status] ?? 0,
            ];
        }

        $totalUser = array_sum(array_column($work, 'total'));

        return response()->json([
            'message' => 'Chart By Status Work',
            'data' => [
                'work' => $work,
                'total_user' => $totalUser,
            ],
        ]);
    }

    public function chartByYear()
    {
        $statuses = [
            'employed',
            'unemployed',
            'seeking',
            'continuing_study'
        ];

        $raw = User::query()
            ->join('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->where('users.role', 'alumni')
            ->selectRaw('user_infos.last_year as year, user_infos.work as status, COUNT(*) as total')
            ->groupBy('year', 'status')
            ->orderBy('year')
            ->get();

        $years = $raw->pluck('year')->unique()->sort()->values();

        $series = [];
        foreach ($statuses as $status) {
            $series[$status] = $years->mapWithKeys(function ($year) use ($raw, $status) {
                $match = $raw->first(fn($item) => $item->year == $year && $item->status === $status);
                return [$year => $match->total ?? 0];
            });
        }

        return response()->json([
            'message' => 'Chart Work By Year',
            'data' => [
                'years' => $years,
                'series' => $series,
            ],
        ]);
    }


    public function calueUser($totalUser, $totalStatusWork)
    {
        $result = $totalStatusWork / $totalUser;
        return $result * 100;
    }
}


