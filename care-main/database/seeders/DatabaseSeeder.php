<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        

        $this->call([
            RolesSeeder::class,
            UsersSeeder::class,
            DoctorsSeeder::class,
            PatientsSeeder::class,
            WorkingHoursSeeder::class,
            ServicesSeeder::class,
            AppointmentsSeeder::class,
            InvoicesSeeder::class,
            PaymentsSeeder::class,
        ]);
    }
}
