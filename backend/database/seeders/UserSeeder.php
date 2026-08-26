<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ស្វែងរក Roles តាមរយៈ Spatie Model
        $adminRole = Role::where('name', 'admin')->first();
        $staffRole = Role::where('name', 'staff')->first();
        $alumniRole = Role::where('name', 'alumni')->first();

        // 1. Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@alumni.com'],
            [
                'name_khmer' => 'អ្នកគ្រប់គ្រង',
                'name_english' => 'System Administrator',
                'role_id' => $adminRole?->id,
                'mobile' => '012000001',
                'status' => 'active',
                'password' => Hash::make('password'),
                'avatar' => null,
            ]
        );
        // Assign Role ចូល Spatie Pivot Table (model_has_roles)
        if ($adminRole) {
            $admin->syncRoles([$adminRole->name]);
        }

        // 2. Staff
        $staff = User::updateOrCreate(
            ['email' => 'staff@alumni.com'],
            [
                'name_khmer' => 'បុគ្គលិក',
                'name_english' => 'System Staff',
                'role_id' => $staffRole?->id,
                'mobile' => '012000002',
                'status' => 'active',
                'password' => Hash::make('password'),
                'avatar' => null,
            ]
        );
        if ($staffRole) {
            $staff->syncRoles([$staffRole->name]);
        }

        // 3. Alumni
        $alumni = User::updateOrCreate(
            ['email' => 'alumni@alumni.com'],
            [
                'name_khmer' => 'សិស្សចាស់',
                'name_english' => 'John Alumni',
                'role_id' => $alumniRole?->id,
                'mobile' => '012000003',
                'status' => 'active',
                'password' => Hash::make('password'),
                'avatar' => null,
            ]
        );
        if ($alumniRole) {
            $alumni->syncRoles([$alumniRole->name]);
        }
    }
}
