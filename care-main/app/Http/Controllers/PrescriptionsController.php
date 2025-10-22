<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrescriptionsController extends Controller
{
    public function index(Request $request)
    {
        $doctor = Auth::user()->doctor;
        $q = Prescription::with(['medicalRecord.patient','medicalRecord'])
            ->whereHas('medicalRecord', function($b) use ($doctor){
                $b->where('doctor_id', $doctor->id);
            });

        if ($request->filled('patient')) {
            $p = $request->input('patient');
            $q->whereHas('medicalRecord.patient', function($b) use ($p){
                $b->where('name','like','%'.$p.'%')->orWhere('code','like','%'.$p.'%');
            });
        }

        if ($request->filled('from')) {
            $q->whereHas('medicalRecord', function($b) use ($request){ $b->where('visit_date','>=',$request->input('from')); });
        }
        if ($request->filled('to')) {
            $q->whereHas('medicalRecord', function($b) use ($request){ $b->where('visit_date','<=',$request->input('to')); });
        }

        if ($request->filled('drug')) {
            $q->where('drug_name','like','%'.$request->input('drug').'%');
        }

        $prescriptions = $q->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('doctor.prescriptions.index', compact('prescriptions'));
    }
}
