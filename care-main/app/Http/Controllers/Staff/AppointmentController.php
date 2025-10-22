<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\WorkingHour;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $q = Appointment::with(['patient','doctor']);

        if ($request->filled('patient')) {
            $q->whereHas('patient', function($sub) use ($request) {
                $sub->where('name', 'like', '%'.$request->get('patient').'%');
            });
        }
        if ($request->filled('doctor')) {
            $q->whereHas('doctor', function($sub) use ($request) {
                $sub->where('name', 'like', '%'.$request->get('doctor').'%');
            });
        }
        if ($request->filled('status')) {
            $q->where('status', $request->get('status'));
        }
        if ($request->filled('date')) {
            $date = Carbon::parse($request->get('date'));
            $q->whereDate('starts_at', $date->toDateString());
        }

        $appointments = $q->orderBy('starts_at','desc')->paginate(20);

        $doctors = Doctor::select('id','name')->get();
        $patients = Patient::select('id','name')->get();

        return view('staff.appointments.manage', compact('appointments','doctors','patients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // working hours check
        $weekday = Carbon::parse($data['starts_at'])->dayOfWeek;
        $startTime = Carbon::parse($data['starts_at'])->format('H:i:s');
        $endTime = Carbon::parse($data['ends_at'])->format('H:i:s');

        $within = WorkingHour::where('doctor_id', $data['doctor_id'])->where('weekday', $weekday)
            ->where('start_time', '<=', $startTime)
            ->where('end_time', '>=', $endTime)
            ->exists();

        if (!$within) {
            return back()->withInput()->withErrors(['starts_at' => 'Selected time is outside doctor working hours.']);
        }

        // overlap check with doctor's appointments
        $overlap = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('status','!=','cancelled')
            ->where(function($q) use ($data){
                $q->whereBetween('starts_at', [$data['starts_at'], $data['ends_at']])
                  ->orWhereBetween('ends_at', [$data['starts_at'], $data['ends_at']])
                  ->orWhereRaw('? < starts_at AND ? > ends_at', [$data['starts_at'], $data['ends_at']]);
            })->exists();

        if ($overlap) {
            return back()->withInput()->withErrors(['starts_at' => 'Doctor has another appointment that overlaps this time.']);
        }

        Appointment::create([
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'status' => $data['status'] ?? 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('staff.appointments.index')->with('status','Appointment created.');
    }

    public function edit(Appointment $appointment)
    {
        $doctors = Doctor::select('id','name')->get();
        $patients = Patient::select('id','name')->get();
        return view('staff.appointments.edit', compact('appointment','doctors','patients'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $weekday = Carbon::parse($data['starts_at'])->dayOfWeek;
        $startTime = Carbon::parse($data['starts_at'])->format('H:i:s');
        $endTime = Carbon::parse($data['ends_at'])->format('H:i:s');

        $within = WorkingHour::where('doctor_id', $data['doctor_id'])->where('weekday', $weekday)
            ->where('start_time', '<=', $startTime)
            ->where('end_time', '>=', $endTime)
            ->exists();
        if (!$within) {
            return back()->withInput()->withErrors(['starts_at' => 'Selected time is outside doctor working hours.']);
        }

        $overlap = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('status','!=','cancelled')
            ->where('id','!=',$appointment->id)
            ->where(function($q) use ($data){
                $q->whereBetween('starts_at', [$data['starts_at'], $data['ends_at']])
                  ->orWhereBetween('ends_at', [$data['starts_at'], $data['ends_at']])
                  ->orWhereRaw('? < starts_at AND ? > ends_at', [$data['starts_at'], $data['ends_at']]);
            })->exists();

        if ($overlap) {
            return back()->withInput()->withErrors(['starts_at' => 'Doctor has another appointment that overlaps this time.']);
        }

        $appointment->update([
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'status' => $data['status'] ?? $appointment->status,
            'notes' => $data['notes'] ?? $appointment->notes,
        ]);

        return redirect()->route('staff.appointments.index')->with('status','Appointment updated.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->status = 'cancelled';
        $appointment->save();
        return redirect()->route('staff.appointments.index')->with('status','Appointment cancelled.');
    }
}
