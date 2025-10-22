<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient','doctor.user'])->orderBy('starts_at')->paginate(15);
       
        return view('admin.appointments.index', compact('appointments'));
    }
  public function show(Appointment $appointment)
    {
        // عرض تفاصيل الموعد (يمكنك تخصيص العرض لاحقاً)
        return view('admin.appointments.show', compact('appointment'));
    }
    public function create()
    {
        $patients = Patient::all();
        $doctors = Doctor::all();
        return view('admin.appointments.create', compact('patients','doctors'));
    }

    protected function overlaps($doctorId, $start, $end, $excludeId = null)
    {
        $q = Appointment::where('doctor_id', $doctorId)
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('starts_at', [$start, $end])
                      ->orWhereBetween('ends_at', [$start, $end]);
            });
        if ($excludeId) $q->where('id', '!=', $excludeId);
        return $q->exists();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'starts_at' => 'required|date|after:now',
            'ends_at' => 'required|date|after:starts_at',
            'notes' => 'nullable|string',
        ]);

        if ($this->overlaps($data['doctor_id'], $data['starts_at'], $data['ends_at'])) {
            return back()->with('error', 'الموعد يتداخل مع موعد آخر للطبيب');
        }

        Appointment::create($data);
        return redirect()->route('admin.appointments.index')->with('success', 'تم إنشاء الموعد');
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::all();
        $doctors = Doctor::all();
        return view('admin.appointments.edit', compact('appointment','patients','doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'starts_at' => 'required|date|after:now',
            'ends_at' => 'required|date|after:starts_at',
            'notes' => 'nullable|string',
        ]);

        if ($this->overlaps($appointment->doctor_id, $data['starts_at'], $data['ends_at'], $appointment->id)) {
            return back()->with('error', 'الموعد يتداخل مع موعد آخر للطبيب');
        }

        $appointment->update($data);
        return redirect()->route('admin.appointments.index')->with('success', 'تم تعديل الموعد');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('admin.appointments.index')->with('success', 'تم حذف الموعد');
    }

    /**
     * Start visit from appointment: create a medical record and redirect to its edit page.
     */
    public function start(Appointment $appointment)
    {
        if (! $appointment->patient) {
            return redirect()->back()->with('error', 'المريض غير موجود');
        }

        $record = \App\Models\MedicalRecord::create([
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'visit_date' => now(),
            'diagnosis' => null,
            'notes' => 'Opened from appointment #' . $appointment->id,
        ]);

        $appointment->update(['status' => 'in_progress']);

        return redirect()->route('admin.medical_records.edit', $record->id);
    }
}
