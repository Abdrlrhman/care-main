<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('user')->get();

        return view('admin.doctors.index', compact('doctors'));
    }

    public function show(Doctor $doctor)
    {
        return view('admin.doctors.show', compact('doctor'));
    }

    // عرض صفحة الإنشاء (الآن مباشرة لإنشاء مستخدم جديد كطبيب)
    public function create()
    {
        return view('admin.doctors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'specialty' => 'nullable|string|max:255',
            'appointment_fee' => 'nullable|numeric|min:0',
            'profit_share' => 'nullable|numeric|min:0|max:100',
            'phone' => 'nullable|string|max:50',
        ]);

        // أنشئ المستخدم
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // عطّيه دور دكتور (إذا بتستعمل spatie/laravel-permission)
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('doctor');
        }

        // أنشئ سجل الطبيب
        Doctor::create([
            'user_id' => $user->id,
            'appointment_fee' => $data['appointment_fee'] ?? 0, 
            'profit_share' => $data['profit_share'] ?? 0,
            'specialty' => $data['specialty'] ?? null,
            'phone' => $data['phone'] ?? null,
            'name' => $user->name,
        ]);

        return redirect()->route('admin.doctors.index')->with('success', 'تم إنشاء المستخدم وتمت إضافته كطبيب بنجاح');
    }

    public function edit(Doctor $doctor)
    {
        return view('admin.doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'specialty' => 'nullable|string',
            'appointment_fee' => 'nullable|numeric|min:0',
            'profit_share' => 'nullable|numeric|min:0|max:100',
            'phone' => 'nullable|string',
        ]);
        $doctor->update($data);

        return redirect()->route('admin.doctors.index')->with('success', 'تم تعديل بيانات الطبيب');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();

        return redirect()->route('admin.doctors.index')->with('success', 'تم حذف الطبيب');
    }
}
