<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Doctor;
use Spatie\Permission\Models\Role;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        // تأكد من وجود role باسم "doctor"
        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);

        $commonPassword = Hash::make('123456');

        $doctorsData = [
            [
                'name' => 'د. أحمد محمد',
                'email' => 'ahmed@example.com',
                'specialty' => 'طب القلب',
                'phone' => '01012345678',
                'bio' => 'أخصائي أمراض القلب وقسطرة الشرايين، خبرة أكثر من 10 سنوات.',
            ],
            [
                'name' => 'د. سارة علي',
                'email' => 'sara@example.com',
                'specialty' => 'الجلدية',
                'phone' => '01023456789',
                'bio' => 'طبيبة جلدية متخصصة في علاج حب الشباب والأمراض الجلدية المزمنة.',
            ],
            [
                'name' => 'د. خالد حسن',
                'email' => 'khaled@example.com',
                'specialty' => 'العظام',
                'phone' => '01034567890',
                'bio' => 'أخصائي جراحة العظام وعلاج الإصابات الرياضية.',
            ],
            [
                'name' => 'د. منى عبد الرحمن',
                'email' => 'mona@example.com',
                'specialty' => 'النساء والتوليد',
                'phone' => '01045678901',
                'bio' => 'أخصائية نساء وتوليد، خبرة في الولادات الطبيعية والقيصرية.',
            ],
            [
                'name' => 'د. عمر فؤاد',
                'email' => 'omar@example.com',
                'specialty' => 'الأنف والأذن والحنجرة',
                'phone' => '01056789012',
                'bio' => 'استشاري أمراض الأنف والأذن والحنجرة، متخصص في جراحات التجميل الوظيفي.',
            ],
        ];

        foreach ($doctorsData as $data) {
            // إنشاء المستخدم عبر Eloquent (ضروري لـ Spatie)
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'email_verified_at' => Carbon::now(),
                'password' => $commonPassword,
            ]);

            // تعيين الدور "doctor"
            $user->assignRole('doctor');

            // إنشاء سجل الدكتور
            Doctor::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'specialty' => $data['specialty'],
                'phone' => $data['phone'],
                'bio' => $data['bio'],
            ]);
        }
    }
}