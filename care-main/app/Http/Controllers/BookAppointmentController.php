<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;
use App\Models\WorkingHour;
use App\Models\Appointment;
use Carbon\Carbon;

class BookAppointmentController extends Controller
{
    public function index()
    {
        $specialties = Doctor::query()->select('specialty')->distinct()->pluck('specialty');
        return view('appointments.book', compact('specialties'));
    }

    public function apiDoctors(Request $request)
    {
        $specialty = $request->get('specialty');
        $q = Doctor::query();
        if ($specialty) $q->where('specialty', $specialty);
        $doctors = $q->select('id','name','specialty','bio')->get();
        return response()->json($doctors);
    }

    public function apiSlots(Request $request)
    {
        $doctorId = $request->get('doctor_id');
        $date = $request->get('date'); // yyyy-mm-dd
        if (!$doctorId || !$date) return response()->json(['error'=>'doctor_id and date required'], 422);

        $doctor = Doctor::findOrFail($doctorId);

        $weekday = Carbon::parse($date)->dayOfWeek; // 0 Sun .. 6 Sat

        $hours = WorkingHour::where('doctor_id', $doctorId)->where('weekday', $weekday)->get();

        $slots = [];
        $slotLength = 30; // minutes

        foreach ($hours as $wh) {
            $start = Carbon::parse($date . ' ' . $wh->start_time);
            $end = Carbon::parse($date . ' ' . $wh->end_time);
            for ($cursor = $start->copy(); $cursor->addMinutes(0)->lt($end); $cursor->addMinutes($slotLength)) {
                $slotEnd = $cursor->copy()->addMinutes($slotLength);
                if ($slotEnd->gt($end)) break;
                $slots[] = ['start' => $cursor->toDateTimeString(), 'end' => $slotEnd->toDateTimeString()];
            }
        }

        // filter out slots that conflict with existing appointments of the doctor
        $existing = Appointment::where('doctor_id', $doctorId)->whereDate('starts_at', $date)->where('status', '!=', 'cancelled')->get();

        $available = collect($slots)->reject(function($s) use ($existing) {
            $sStart = Carbon::parse($s['start']);
            $sEnd = Carbon::parse($s['end']);
            foreach ($existing as $ap) {
                $aStart = $ap->starts_at;
                $aEnd = $ap->ends_at;
                if ($sStart->lt($aEnd) && $sEnd->gt($aStart)) return true; // overlap
            }
            return false;
        })->values();

        return response()->json($available);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $patient = $user->patient;

        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'notes' => 'nullable|string',
        ]);

        // check working hours
        $weekday = Carbon::parse($data['starts_at'])->dayOfWeek;
        $time = Carbon::parse($data['starts_at'])->format('H:i:s');
        $found = WorkingHour::where('doctor_id', $data['doctor_id'])->where('weekday', $weekday)
            ->where('start_time', '<=', $time)->where('end_time', '>=', Carbon::parse($data['ends_at'])->format('H:i:s'))->exists();
        if (!$found) {
            return back()->withInput()->withErrors(['starts_at' => 'Selected time is outside doctor working hours.']);
        }

        // avoid overlapping with patient's own appointments
        $overlap = Appointment::where('patient_id', $patient->id)->where(function($q) use ($data){
            $q->whereBetween('starts_at', [$data['starts_at'], $data['ends_at']])->orWhereBetween('ends_at', [$data['starts_at'], $data['ends_at']])->orWhereRaw('? < starts_at AND ? > ends_at', [$data['starts_at'], $data['ends_at']]);
        })->where('status','!=','cancelled')->exists();

        if ($overlap) {
            return back()->withInput()->withErrors(['starts_at' => 'You already have an appointment that overlaps this time.']);
        }

        $appt = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $data['doctor_id'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('patient.dashboard')->with('success', 'Appointment requested.');
    }
}
