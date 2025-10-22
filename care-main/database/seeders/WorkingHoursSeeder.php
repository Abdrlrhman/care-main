<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkingHoursSeeder extends Seeder
{
    public function run(): void
    {
        // IDs الأطباء (نفترض أنهم من 1 إلى 5)
        $doctorIds = [1, 2, 3, 4, 5];

        // أيام العمل: الأحد (0) إلى الخميس (4)
        $workdays = [0, 1, 2, 3, 4];

        foreach ($doctorIds as $doctorId) {
            foreach ($workdays as $day) {
                // ساعات عمل مختلفة بسيطة حسب اليوم (اختياري)
                if ($day == 0 || $day == 2 || $day == 4) {
                    // الأحد، الثلاثاء، الخميس: 9 صباحًا - 4 عصرًا
                    $start = '09:00:00';
                    $end = '16:00:00';
                } else {
                    // الإثنين، الأربعاء: 10 صباحًا - 6 مساءً
                    $start = '10:00:00';
                    $end = '18:00:00';
                }

                DB::table('working_hours')->insert([
                    'doctor_id' => $doctorId,
                    'weekday' => $day,
                    'start_time' => $start,
                    'end_time' => $end,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}