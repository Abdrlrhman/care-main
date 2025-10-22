<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class PatientsDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $q = Patient::query();

        if ($request->filled('gender')) {
            $q->where('gender', $request->gender);
        }

        if ($request->filled('min_age') || $request->filled('max_age')) {
            $today = now();
            if ($request->filled('min_age')) {
                $q->whereDate('birth_date', '<=', $today->subYears(intval($request->min_age)));
                $today = now();
            }
            if ($request->filled('max_age')) {
                $q->whereDate('birth_date', '>=', $today->subYears(intval($request->max_age)));
                $today = now();
            }
        }

        if ($request->filled('registered_from')) {
            $q->whereDate('created_at', '>=', $request->registered_from);
        }
        if ($request->filled('registered_to')) {
            $q->whereDate('created_at', '<=', $request->registered_to);
        }

        $patients = $q->orderBy('name')->paginate(20)->withQueryString();

        // aggregate last visit and total visits per patient (appointments)
        $visits = Appointment::select('patient_id', DB::raw('COUNT(*) as total'), DB::raw('MAX(starts_at) as last_visit'))
            ->whereIn('patient_id', $patients->pluck('id'))
            ->groupBy('patient_id')
            ->get()
            ->keyBy('patient_id');

        return view('admin.patients.index', compact('patients','visits'));
    }

    public function deactivate(Request $request, Patient $patient)
    {
        // simple toggle active flag if exists, otherwise soft delete
        if (schema_has_column('patients', 'active')) {
            $patient->active = false;
            $patient->save();
        } else {
            // use soft delete if model uses SoftDeletes
            if (method_exists($patient, 'delete')) {
                $patient->delete();
            }
        }

        return redirect()->route('admin.patients.directory')->with('success', 'Patient deactivated');
    }
}
