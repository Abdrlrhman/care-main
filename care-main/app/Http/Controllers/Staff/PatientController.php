<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use Carbon\Carbon;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $q = Patient::query();

        if ($request->filled('q')) {
            $term = $request->get('q');
            $q->where(function($sub) use ($term){
                $sub->where('name','like','%'.$term.'%')
                    ->orWhere('code','like','%'.$term.'%')
                    ->orWhere('phone','like','%'.$term.'%');
            });
        }

        $patients = $q->orderBy('name')->paginate(25);

        return view('staff.patients.index', compact('patients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:patients,code',
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'phone' => ['nullable','regex:/^[0-9\-\+\s()]{7,20}$/'],
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Patient::create($data);
        return redirect()->route('staff.patients.index')->with('status','Patient added.');
    }

    public function edit(Patient $patient)
    {
        return view('staff.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:patients,code,'.$patient->id,
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'phone' => ['nullable','regex:/^[0-9\-\+\s()]{7,20}$/'],
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $patient->update($data);
        return redirect()->route('staff.patients.index')->with('status','Patient updated.');
    }

    public function show(Patient $patient)
    {
        return view('staff.patients.show', compact('patient'));
    }
}
