<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\Major;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Laravel\Mcp\Request;

class AlumniSeeder extends Seeder
{
    public function run(): void
    {
        $majorIds = Major::pluck('id');

        if ($majorIds->isEmpty()) {
            $this->command->warn('No majors found. Run MajorSeeder first.');
            return;
        }

        $statuses = ['employed', 'unemployed', 'self_employed', 'studying', 'unknown'];
        $years = ['2022', '2023', '2024', '2025', '2026'];

        for ($i = 1; $i <= 30; $i++) {
            $user = User::create([
                'name_english' => "Alumni Test {$i}",
                'role_id' => 3,
                'name_khmer' => "និស្សិត {$i}",
                'email' => "alumni{$i}@test.com",
                'mobile' => '0' . rand(70000000, 99999999),
                'password' => Hash::make('password'),
                'status' => 'active',
            ]);

            $user->assignRole('alumni');

            Alumni::create([
                'user_id' => $user->id,
                'major_id' => $majorIds->random(),
                'graduation_year' => $years[array_rand($years)],
                'employment_status' => $statuses[array_rand($statuses)],
                'gender' => ['male', 'female'][array_rand(['male', 'female'])],
            ]);
        }
    }




    /*
    |---------------------------------------------------------------------
    |---------------------------------------------------------------------
    */


}
