<?php

namespace Database\Seeders;

use App\Models\Core\UserStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserStatus::updateOrCreate(
            ['key' => 'active'],
            [
                'name' => [
                    'en' => 'Active',
                    'ar' => 'فعّال'
                ],
                'color' => '#28b463',
                'is_active' => 1
            ]
        );

        UserStatus::updateOrCreate(
            ['key' => 'inactive'],
            [
                'name' => [
                    'en' => 'Inactive',
                    'ar' => 'غير فعّال'
                ],
                'color' => '#cb4335',
                'is_active' => 1
            ]
        );
    }
}
