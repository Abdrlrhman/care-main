<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;

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

    public function create()
    {
        $users = User::doesntHave('doctor')->get();
        return view('admin.doctors.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id|unique:doctors,user_id',
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:6',
            'specialty' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        // إذا لم يتم اختيار مستخدم، ننشئ مستخدم جديد
        if (empty($data['user_id'])) {
            $user = User::create([
                'name' => $data['name'] ?? 'طبيب جديد',
                'email' => $data['email'] ?? null,
                'password' => isset($data['password']) ? bcrypt($data['password']) : bcrypt('doctor123'),
            ]);
            $user->assignRole('doctor');
            $data['user_id'] = $user->id;
        } else {
            $user = User::find($data['user_id']);
            if ($user && !$user->hasRole('doctor')) {
                $user->assignRole('doctor');
            }
        }

        Doctor::create([
            'user_id' => $data['user_id'],
            'specialty' => $data['specialty'] ?? null,
            'phone' => $data['phone'] ?? null,
            'name' => $user->name,
        ]);
        return redirect()->route('admin.doctors.index')->with('success', 'تم إضافة الطبيب بنجاح');
    }

    public function edit(Doctor $doctor)
    {
        return view('admin.doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'specialty' => 'nullable|string',
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
