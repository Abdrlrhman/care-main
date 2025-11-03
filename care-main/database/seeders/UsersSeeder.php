<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تأكد من وجود الأدوار المطلوبة
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'staff']);

        // إنشاء حساب المشرف (Admin)
        $admin = User::firstOrCreate(
            ['email' => 'admin@care.com'],
            [
                'name' => 'Admin One',
                'password' => bcrypt('123456'),
            ]
        );
        $admin->assignRole('admin');

        // إنشاء حساب الموظف (Staff)
        $staff = User::firstOrCreate(
            ['email' => 'staff@care.com'],
            [
                'name' => 'Staff One',
                'password' => bcrypt('123456'),
            ]
        );
        $staff->assignRole('staff');
    }
}