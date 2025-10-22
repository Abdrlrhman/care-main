<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Carbon\Carbon;

class AppointmentsSeeder extends Seeder
{
    public function run(): void
    {
        // جلب الأطباء والمرضى
        $doctors = Doctor::all();
        $patients = Patient::all();

        // التأكد من وجود بيانات
        if ($doctors->isEmpty() || $patients->isEmpty()) {
            $this->command->warn('لا يوجد أطباء أو مرضى في قاعدة البيانات. تأكد من تشغيل seeders الأطباء والمرضى أولًا.');
            return;
        }

        // تعريف بعض المواعيد الافتراضية (يمكنك زيادتها)
        $appointmentsData = [];

        // مثال: 10 مواعيد موزعة على الأطباء والمرضى
        for ($i = 0; $i < 10; $i++) {
            $doctor = $doctors->get($i % $doctors->count());
            $patient = $patients->get($i % $patients->count());

            // اختيار يوم عمل (الأحد = 0، الإثنين = 1، ... الخميس = 4)
            $workdays = [0, 1, 2, 3, 4];
            $randomDayIndex = array_rand($workdays);
            $weekday = $workdays[$randomDayIndex];

            // حساب تاريخ الموعد حسب اليوم المطلوب (نبدأ من اليوم)
            $nextDate = Carbon::now()->startOfDay();
            while ($nextDate->dayOfWeek !== $weekday) {
                $nextDate->addDay();
            }

            // إضافة يومين إضافيين علشان مايكونش اليوم الحالي
            $nextDate->addWeeks(intdiv($i, 5) + 1);

            // اختيار وقت داخل ساعات العمل (مثلاً بين 9 صباحًا و4 عصرًا)
            $startHour = rand(9, 15); // من 9 إلى 3 عصرًا
            $startsAt = $nextDate->copy()->setTime($startHour, rand(0, 45) % 60); // دقيقة عشوائية
            $endsAt = $startsAt->copy()->addHour();

            $appointmentsData[] = [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => 'scheduled',
                'notes' => "موعد مبدئي للتشخيص - مريض رقم {$patient->code}",
            ];
        }

        // إدخال المواعيد مع منع التكرار (بناءً على patient_id, doctor_id, starts_at)
        foreach ($appointmentsData as $data) {
            Appointment::updateOrCreate(
                [
                    'patient_id' => $data['patient_id'],
                    'doctor_id' => $data['doctor_id'],
                    'starts_at' => $data['starts_at'],
                ],
                [
                    'ends_at' => $data['ends_at'],
                    'status' => $data['status'],
                    'notes' => $data['notes'],
                ]
            );
        }
    }
}