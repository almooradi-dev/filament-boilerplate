<?php

namespace Database\Seeders;

use App\Models\Core\UserStatus;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserStatusSeeder::class,
        ]);

        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $superAdminUser = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'super_admin@example.com',
            'password' => Hash::make('12345678'),
            'status_id' => UserStatus::where('key', 'active')->first()?->id
        ]);
        $superAdminUser->assignRole($superAdminRole->id);
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678'),
            'status_id' => UserStatus::where('key', 'active')->first()?->id
        ]);
    }
}
