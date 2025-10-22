<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // مستخدم عادي
        $user = User::firstOrCreate(
            ['email' => 'user@care.com'],
            [
                'name' => 'User One',
                'password' => bcrypt('password'),
            ]
        );
        $user->assignRole('user');

        // طبيب
        $doctor = User::firstOrCreate(
            ['email' => 'doctor@care.com'],
            [
                'name' => 'Doctor One',
                'password' => bcrypt('password'),
            ]
        );
        $doctor->assignRole('doctor');

        // أدمن
        $admin = User::firstOrCreate(
            ['email' => 'admin@care.com'],
            [
                'name' => 'Admin One',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole('admin');

        // موظف
        $staff = User::firstOrCreate(
            ['email' => 'staff@care.com'],
            [
                'name' => 'Staff One',
                'password' => bcrypt('password'),
            ]
        );
        $staff->assignRole('staff');
    }
}
