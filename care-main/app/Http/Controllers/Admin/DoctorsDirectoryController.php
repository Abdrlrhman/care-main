<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\WorkingHour;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class DoctorsDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $q = Doctor::withCount('medicalRecords')->withCount('invoices');

        if ($request->filled('specialty')) {
            $q->where('specialty', $request->specialty);
        }

        $doctors = $q->orderBy('name')->paginate(20)->withQueryString();

        // gather working hours summary per doctor (simple: count of weekdays configured)
        $wh = WorkingHour::select('doctor_id', DB::raw('COUNT(DISTINCT weekday) as days'))
            ->whereIn('doctor_id', $doctors->pluck('id'))
            ->groupBy('doctor_id')
            ->get()
            ->keyBy('doctor_id');

        // gather unique patients per doctor via appointments
        $patients = Appointment::select('doctor_id', DB::raw('COUNT(DISTINCT patient_id) as patients'))
            ->whereIn('doctor_id', $doctors->pluck('id'))
            ->groupBy('doctor_id')
            ->get()
            ->keyBy('doctor_id');

        $patientsCounts = [];
        foreach ($patients as $k => $row) {
            $patientsCounts[$k] = $row->patients;
        }

        return view('admin.doctors.index', compact('doctors','wh','patientsCounts'));
    }
}
