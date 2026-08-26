<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    public function run(): void
    {
        $majors = [
            ['name' => 'Computer Science', 'description' => 'ជំនាញវិទ្យាសាស្ត្រកុំព្យូទ័រ'],
            ['name' => 'Information Technology', 'description' => 'ជំនាញព័ត៌មានវិទ្យា'],
            ['name' => 'Business Administration', 'description' => 'ជំនាញគ្រប់គ្រងពាណិជ្ជកម្ម'],
            ['name' => 'Accounting', 'description' => 'ជំនាញគណនេយ្យ'],
            ['name' => 'English Literature', 'description' => 'ជំនាញអក្សរសាស្ត្រអង់គ្លេស'],
        ];

        foreach ($majors as $major) {
            Major::firstOrCreate(['name' => $major['name']], $major);
        }
    }
}
