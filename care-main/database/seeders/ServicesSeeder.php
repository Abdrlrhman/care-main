<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Consultation', 'price' => 100],
            ['name' => 'X-Ray', 'price' => 200],
            ['name' => 'Blood Test', 'price' => 150],
        ];

        foreach ($services as $s) {
            Service::updateOrCreate(['name' => $s['name']], $s);
        }
    }
}
