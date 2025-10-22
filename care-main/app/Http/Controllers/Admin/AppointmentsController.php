<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class AppointmentsController extends Controller
{
    public function index()
    {
        $doctors = Doctor::orderBy('name')->get();
        $patients = Patient::orderBy('name')->get();
        return view('admin.appointments.management', compact('doctors','patients'));
    }

    /**
     * API: return appointments filtered for calendar or list view
     */
    public function apiList(Request $request)
    {
        $q = Appointment::with(['patient','doctor']);

        if ($request->filled('doctor_id')) $q->where('doctor_id', $request->doctor_id);
        if ($request->filled('patient_id')) $q->where('patient_id', $request->patient_id);
        if ($request->filled('status')) $q->where('status', $request->status);
        if ($request->filled('from')) $q->whereDate('starts_at', '>=', $request->from);
        if ($request->filled('to')) $q->whereDate('starts_at', '<=', $request->to);

        // فقط المواعيد الفارغة
        $items = $q->where('status', 'free')->orderBy('starts_at')->get();

        // دائماً أرجع JSON
        return response()->json($items);
    }

    public function cancel(Appointment $appointment)
    {
        $appointment->status = 'cancelled';
        $appointment->save();
        return response()->json(['ok'=>true]);
    }

    public function reassign(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
        ]);

        // check overlap
        $exists = Appointment::where('doctor_id', $data['doctor_id'])
            ->where(function($q) use ($appointment){
                $q->whereBetween('starts_at', [$appointment->starts_at, $appointment->ends_at])
                  ->orWhereBetween('ends_at', [$appointment->starts_at, $appointment->ends_at]);
            })->exists();

        if ($exists) return response()->json(['error' => 'Doctor has conflicting appointment'], 422);

        $appointment->doctor_id = $data['doctor_id'];
        $appointment->save();
        return response()->json(['ok'=>true]);
    }
}
