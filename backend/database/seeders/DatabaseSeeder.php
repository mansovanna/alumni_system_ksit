<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RoleSeeder::class,
            MajorSeeder::class,   // ← ត្រូវដាក់មុន AlumniSeeder
            UserSeeder::class,
            AlumniSeeder::class,
        ]);
    }
}
