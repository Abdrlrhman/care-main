<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prescription;

class PatientPrescriptionsController extends Controller
{
    public function index()
    {
        $patient = Auth::user()->patient;

        $prescriptions = Prescription::with('medicalRecord.doctor')
            ->whereHas('medicalRecord', function($q) use ($patient){
                $q->where('patient_id', $patient->id);
            })->orderByDesc('created_at')->get();

        // group by visit_date and doctor
        $grouped = $prescriptions->groupBy(function($p){
            $date = optional($p->medicalRecord->visit_date)->toDateString();
            $doctor = optional($p->medicalRecord->doctor)->name ?? 'Unknown';
            return $date.'|'.$doctor;
        });

        return view('patient.prescriptions.index', compact('grouped'));
    }
}
