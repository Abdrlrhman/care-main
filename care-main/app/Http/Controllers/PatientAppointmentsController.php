<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;

class PatientAppointmentsController extends Controller
{
    public function index(Request $request)
    {
        $patient = Auth::user()->patient;

        $q = Appointment::with('doctor')->where('patient_id', $patient->id);

        if ($request->filled('status')) {
            if ($request->status == 'upcoming') {
                $q->where('starts_at', '>=', now())->where('status', '!=', 'cancelled');
            } elseif ($request->status == 'completed') {
                $q->where('status', 'completed');
            } elseif ($request->status == 'cancelled') {
                $q->where('status', 'cancelled');
            }
        }

        $appointments = $q->orderBy('starts_at', 'desc')->paginate(20)->withQueryString();

        return view('patient.appointments.index', compact('appointments'));
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        $patient = Auth::user()->patient;
        if ($appointment->patient_id !== $patient->id) {
            abort(403);
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled.');
    }
}
