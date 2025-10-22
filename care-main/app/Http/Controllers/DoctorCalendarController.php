<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorCalendarController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $doctor = $user->doctor;
        if (! $doctor) {
            abort(404, 'Doctor profile not found');
        }

        return view('doctor.calendar', compact('doctor'));
    }

    public function apiAppointments(Request $request)
    {
        $doctor = Auth::user()->doctor;
        $from = $request->query('start');
        $to = $request->query('end');

        $q = Appointment::with('patient')->where('doctor_id', $doctor->id);
        if ($from && $to) {
            $q->where(function($builder) use ($from, $to) {
                $builder->whereBetween('starts_at', [$from, $to])
                        ->orWhereBetween('ends_at', [$from, $to]);
            });
        }

        $appointments = $q->get();

        $events = $appointments->map(function($a){
            $color = match($a->status){
                'confirmed' => '#198754',
                'in_progress' => '#fd7e14',
                'cancelled' => '#6c757d',
                default => '#0d6efd',
            };

            return [
                'id' => $a->id,
                'title' => ($a->patient->name ?? 'مريض') . ' — ' . ($a->patient->code ?? ''),
                'start' => $a->starts_at->toIso8601String(),
                'end' => $a->ends_at->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'patient_id' => $a->patient_id,
                    'status' => $a->status,
                    'notes' => $a->notes,
                ],
            ];
        });

        return response()->json($events);
    }

    public function apiPatients(Request $request)
    {
        $q = $request->query('query');
        $patients = Patient::when($q, function($builder) use ($q){
            $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
        })->limit(20)->get(['id','name','code','phone']);

        return response()->json($patients);
    }

    public function apiStore(Request $request)
    {
        $doctor = Auth::user()->doctor;

        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // overlap check
        $exists = Appointment::where('doctor_id', $doctor->id)
            ->where(function($query) use ($data){
                $query->whereBetween('starts_at', [$data['starts_at'], $data['ends_at']])
                      ->orWhereBetween('ends_at', [$data['starts_at'], $data['ends_at']]);
            })->exists();

        if ($exists) {
            return response()->json(['message' => 'التوقيت يتداخل مع موعد آخر'], 422);
        }

        $appointment = Appointment::create(array_merge($data, ['doctor_id' => $doctor->id]));

        return response()->json(['id' => $appointment->id], 201);
    }

    public function apiUpdate(Request $request, Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;
        if ($appointment->doctor_id !== $doctor->id) {
            return response()->json(['message'=>'Unauthorized'], 403);
        }

        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // overlap check excluding current
        $exists = Appointment::where('doctor_id', $doctor->id)
            ->where('id','!=',$appointment->id)
            ->where(function($query) use ($data){
                $query->whereBetween('starts_at', [$data['starts_at'], $data['ends_at']])
                      ->orWhereBetween('ends_at', [$data['starts_at'], $data['ends_at']]);
            })->exists();

        if ($exists) {
            return response()->json(['message' => 'التوقيت يتداخل مع موعد آخر'], 422);
        }

        $appointment->update($data);
        return response()->json(['ok'=>true]);
    }

    public function apiDestroy(Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;
        if ($appointment->doctor_id !== $doctor->id) {
            return response()->json(['message'=>'Unauthorized'], 403);
        }

        $appointment->delete();
        return response()->json(['ok'=>true]);
    }

    public function apiWorkingHours()
    {
        $doctor = Auth::user()->doctor;
        $hours = $doctor->workingHours()->orderBy('weekday')->orderBy('start_time')->get(['weekday','start_time','end_time']);
        return response()->json($hours);
    }
}
