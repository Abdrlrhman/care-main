<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class PatientRegisterController extends Controller
{
    public function create()
    {
        return view('auth.register_patient');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:50',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
        ]);

        // create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // assign role 'user' إذا موجود spatie
        if (method_exists($user, 'assignRole')) {
            try {
                $user->assignRole('user');
            } catch (\Throwable $e) {
                // إذا الدور ما موجود تجاهل
            }
        }

        // create patient record
        $patient = Patient::create([
            'user_id' => $user->id,
            'code' => 'PT'.uniqid(),
            'name' => $data['name'],
            'gender' => $data['gender'] ?? null,
            'phone' => $data['phone'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'address' => $data['address'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // login user
        Auth::login($user);

        // redirect: لو في route patient.dashboard روح ليها، غير كدا للـ home
        if (Route::has('patient.dashboard')) {
            return redirect()->route('patient.dashboard')->with('success', 'تم التسجيل بنجاح');
        }

        return redirect('/')->with('success', 'تم التسجيل بنجاح');
    }
}
