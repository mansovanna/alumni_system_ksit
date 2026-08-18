<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            [
                'title' => 'Computer Sience',
                'body' => 'Total alumni on depament',
                'color_from' => '#193CB8',
                'color_to' => '#2B7FFF',
                'icon' => 'computer'

            ],
            [
                'title' => 'Food of Technology',
                'body' => 'Total alumni on depament',
                'color_from' => '#C6185C',
                'color_to' => '#F6339A',
                'icon' => 'physicsIcon'

            ],
            [
                'title' => 'Electrical of Technology',
                'body' => 'Total alumni on depament',
                'color_from' => '#EC253F',
                'color_to' => '#E12AFB',
                'icon' => 'tools'

            ]
        ];

        foreach ($data as $item) {
            Major::create([
                'title' => $item['title'],
                'body' => $item['body'],
                'color_from' => $item['color_from'],
                'color_to' => $item['color_to'],
                'icon' => $item['icon']
            ]);
        }
    }
}
