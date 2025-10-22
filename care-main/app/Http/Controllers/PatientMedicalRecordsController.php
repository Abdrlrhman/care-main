<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MedicalRecord;

class PatientMedicalRecordsController extends Controller
{
    public function index()
    {
        $patient = Auth::user()->patient;
        $records = MedicalRecord::with('doctor')->where('patient_id', $patient->id)->orderByDesc('visit_date')->paginate(20);
        return view('patient.medical_records.index', compact('records'));
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $patient = Auth::user()->patient;
        if ($medicalRecord->patient_id !== $patient->id) abort(403);
        $medicalRecord->load('doctor','prescriptions');
        return view('patient.medical_records.show', compact('medicalRecord'));
    }
}
