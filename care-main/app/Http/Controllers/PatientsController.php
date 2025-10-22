<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\Doctor;
use Illuminate\Http\Request;

class PatientsController extends Controller
{
    public function index(Request $request)
    {
        $q = Patient::query();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $q->where(function($b) use ($s){
                $b->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('gender')) {
            $q->where('gender', $request->input('gender'));
        }

        if ($request->filled('age_min') || $request->filled('age_max')) {
            $today = now();
            if ($request->filled('age_min')) {
                $maxBirth = $today->copy()->subYears($request->input('age_min'));
                $q->where('birth_date', '<=', $maxBirth);
            }
            if ($request->filled('age_max')) {
                $minBirth = $today->copy()->subYears($request->input('age_max'));
                $q->where('birth_date', '>=', $minBirth);
            }
        }

        $patients = $q->withCount(['medicalRecords as last_visit_date' => function($qr){
            $qr->select(\DB::raw('MAX(visit_date)'));
        }])->orderBy('name')->paginate(20)->withQueryString();

        $doctors = Doctor::orderBy('name')->get(['id','name']);

        return view('admin.patients.index', compact('patients','doctors'));
    }
    public function create()
    {
        return view('admin.patients.create');
    }


    public function store(Request $request)
    {
        // Validate patient data
        $data = $request->validate([
            'code' => 'required|string|unique:patients,code',
            'name' => 'required|string',
            'gender' => 'nullable|in:male,female,other',
            'phone' => ['nullable', 'regex:/^\\+?[0-9\- ]{6,20}$/'],
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|required_if:email,',
        ]);

        // Create Patient
        $patient = Patient::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'gender' => $data['gender'] ?? null,
            'phone' => $data['phone'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'address' => $data['address'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // Create User if email provided
        if (!empty($data['email']) && !empty($data['password'])) {
            $user = User::create([
                'name' => $patient->name,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'patient', // أو أي عمود تستخدمه للأدوار
            ]);

            // ربط المستخدم بالمريض (إذا كان لديك علاقة)
            $patient->update(['user_id' => $user->id]);
            // أو إذا كانت العلاقة من جهة المستخدم:
            // $user->update(['patient_id' => $patient->id]);
        }

        return response()->json(['id' => $patient->id], 201);
    }


    public function show($id)
    {
        $patient = Patient::with(['medicalRecords'=>function($q){ $q->orderByDesc('visit_date'); }, 'appointments.doctor.user', 'invoices.payments'])->findOrFail($id);
        return view('admin.patients.show', compact('patient'));
    }
}
