<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Patient;
use Carbon\Carbon;

class PatientsSeeder extends Seeder
{
    public function run(): void
    {
        // تأكد من وجود role باسم "patient"
        $patientRole = Role::firstOrCreate(['name' => 'user']);

        $patientsData = [
            ['name' => 'Ali Hassan', 'gender' => 'male', 'phone' => '0111000001', 'birth_date' => '1985-05-10', 'address' => '123 Nile St, Cairo', 'notes' => null],
            ['name' => 'Mona Khaled', 'gender' => 'female', 'phone' => '0111000002', 'birth_date' => '1990-08-20', 'address' => '456 Tahrir Ave, Cairo', 'notes' => null],
            ['name' => 'Omar Ali', 'gender' => 'male', 'phone' => '0111000003', 'birth_date' => '2000-01-15', 'address' => '789 Corniche Rd, Alexandria', 'notes' => null],
            ['name' => 'Fatma Mahmoud', 'gender' => 'female', 'phone' => '0111000004', 'birth_date' => '1982-11-03', 'address' => '101 Garden City, Cairo', 'notes' => null],
            ['name' => 'Youssef Ahmed', 'gender' => 'male', 'phone' => '0111000005', 'birth_date' => '1995-03-22', 'address' => '202 Mohandessin, Giza', 'notes' => null],
            ['name' => 'Nourhan Samir', 'gender' => 'female', 'phone' => '0111000006', 'birth_date' => '1998-07-14', 'address' => '303 Heliopolis, Cairo', 'notes' => null],
            ['name' => 'Karim Tarek', 'gender' => 'male', 'phone' => '0111000007', 'birth_date' => '1988-12-30', 'address' => '404 Nasr City, Cairo', 'notes' => null],
            ['name' => 'Laila Ibrahim', 'gender' => 'female', 'phone' => '0111000008', 'birth_date' => '1992-04-05', 'address' => '505 Zamalek, Cairo', 'notes' => null],
            ['name' => 'Ahmed Fathy', 'gender' => 'male', 'phone' => '0111000009', 'birth_date' => '1980-09-18', 'address' => '606 Maadi, Cairo', 'notes' => null],
            ['name' => 'Sara El-Sayed', 'gender' => 'female', 'phone' => '0111000010', 'birth_date' => '2001-02-28', 'address' => '707 Dokki, Giza', 'notes' => null],
            ['name' => 'Mahmoud Reda', 'gender' => 'male', 'phone' => '0111000011', 'birth_date' => '1975-06-12', 'address' => '808 Shubra, Cairo', 'notes' => null],
            ['name' => 'Hala Mohamed', 'gender' => 'female', 'phone' => '0111000012', 'birth_date' => '1993-10-25', 'address' => '909 6th of October, Giza', 'notes' => null],
            ['name' => 'Tarek Nabil', 'gender' => 'male', 'phone' => '0111000013', 'birth_date' => '1987-01-09', 'address' => '1010 New Cairo', 'notes' => null],
            ['name' => 'Rania Gamal', 'gender' => 'female', 'phone' => '0111000014', 'birth_date' => '1996-05-17', 'address' => '1111 Mansoura', 'notes' => null],
            ['name' => 'Hassan Zaki', 'gender' => 'male', 'phone' => '0111000015', 'birth_date' => '1983-08-01', 'address' => '1212 Assiut', 'notes' => null],
            ['name' => 'Dina Ashraf', 'gender' => 'female', 'phone' => '0111000016', 'birth_date' => '1999-12-11', 'address' => '1313 Luxor', 'notes' => null],
            ['name' => 'Khaled Salah', 'gender' => 'male', 'phone' => '0111000017', 'birth_date' => '1991-03-04', 'address' => '1414 Suez', 'notes' => null],
            ['name' => 'Nada Emad', 'gender' => 'female', 'phone' => '0111000018', 'birth_date' => '1986-07-29', 'address' => '1515 Ismailia', 'notes' => null],
            ['name' => 'Amr Hesham', 'gender' => 'male', 'phone' => '0111000019', 'birth_date' => '1989-11-22', 'address' => '1616 Port Said', 'notes' => null],
            ['name' => 'Mariam Adel', 'gender' => 'female', 'phone' => '0111000020', 'birth_date' => '2002-04-14', 'address' => '1717 Hurghada', 'notes' => null],
        ];

        foreach ($patientsData as $index => $data) {
            // إنشاء البريد الإلكتروني فريد
            $email = strtolower(str_replace(' ', '.', $data['name'])) . '@patient.example.com';

            // إنشاء المستخدم
            $user = User::create([
                'name' => $data['name'],
                'email' => $email,
                'password' => Hash::make('123456'), // نفس الباسورد لكل المرضى
                'email_verified_at' => Carbon::now(),
            ]);

            // ربط المستخدم بدور "patient"
            $user->assignRole('patient');

            // إنشاء المريض المرتبط
            Patient::create([
                'user_id' => $user->id,
                'code' => 'P' . str_pad($index + 1, 3, '0', STR_PAD_LEFT), // P001, P002, ..., P020
                'name' => $data['name'],
                'gender' => $data['gender'],
                'phone' => $data['phone'],
                'birth_date' => $data['birth_date'],
                'address' => $data['address'],
                'notes' => $data['notes'],
            ]);
        }
    }
}