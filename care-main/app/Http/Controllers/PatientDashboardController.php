<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Prescription;
use App\Models\MedicalRecord;

class PatientDashboardController extends Controller
{
    public function index()
    {
        $patient = Auth::user()->patient;

        // Next appointment
        $next = $patient->appointments()->where('status','!=','cancelled')->where('starts_at','>=',now())->orderBy('starts_at')->with('doctor')->first();

        // Outstanding balance
        $outstanding = $patient->invoices()->whereNotIn('status', ['paid'])->sum('net_total') - $patient->invoices()->whereNotNull('id')->withSum('payments','amount')->get()->sum('payments_sum_amount');

        // Last prescription
        $lastPrescription = Prescription::whereHas('medicalRecord', function($q) use ($patient){ $q->where('patient_id', $patient->id); })->with('medicalRecord')->orderByDesc('created_at')->first();

        // upcoming appointments list
        $upcoming = $patient->appointments()->where('starts_at','>=', now())->orderBy('starts_at')->with('doctor')->limit(10)->get();

        // recent medical records
        $records = $patient->medicalRecords()->orderByDesc('visit_date')->limit(10)->get();

        return view('patient.dashboard.index', compact('patient','next','outstanding','lastPrescription','upcoming','records'));
    }
}
