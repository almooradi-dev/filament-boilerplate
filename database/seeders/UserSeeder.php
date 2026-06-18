<?php

namespace Database\Seeders;

use App\Models\Core\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = UserStatus::where('key', 'active')->first()?->id;

        // Super Admin
        $superAdminUser = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'super_admin@example.com',
            'password' => Hash::make('12345678'),
            'status_id' => $activeStatusId,
        ]);
        $superAdminUser->assignRole('super_admin');

        // Admin
        $adminUser = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678'),
            'status_id' => $activeStatusId,
        ]);
        $adminUser->assignRole('admin');

        // Dummy User
        $dummyUser = User::create([
            'first_name' => 'Dummy',
            'last_name' => 'User',
            'email' => 'dummy@example.com',
            'password' => Hash::make('12345678'),
            'status_id' => $activeStatusId,
        ]);
        $dummyUser->assignRole('user');
    }
}
