<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;

class MedicalRecordsController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'visit_date' => 'required|date',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $record = MedicalRecord::create($data);

        if ($request->expectsJson() || $request->isJson() || $request->header('Content-Type') === 'application/json') {
            return response()->json(['id' => $record->id, 'success' => true]);
        }

        return redirect()->route('admin.patients.show', $data['patient_id'])->with('success','تم إنشاء السجل');
    }

    public function uploadAttachment(Request $request, MedicalRecord $medicalRecord)
    {
        $request->validate(['files.*' => 'file|max:10240']);

        $files = $request->file('files');
        $stored = $medicalRecord->attachments ?? [];
        foreach($files as $f){
            $path = $f->store('medical_records/'.$medicalRecord->id,'public');
            $stored[] = $path;
        }
        $medicalRecord->update(['attachments' => $stored]);

        return redirect()->back()->with('success','تم رفع المرفقات');
    }

    // Doctor-scoped index with filters
    public function index(Request $request)
    {
        $doctor = Auth::user()->doctor;
        $q = MedicalRecord::with('patient')->where('doctor_id', $doctor->id);

        if ($request->filled('from')) {
            $q->where('visit_date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $q->where('visit_date', '<=', $request->input('to'));
        }
        if ($request->filled('search')) {
            $s = $request->input('search');
            $q->whereHas('patient', function($b) use ($s){
                $b->where('name','like','%'.$s.'%')->orWhere('code','like','%'.$s.'%');
            })->orWhere('diagnosis','like','%'.$s.'%');
        }

        $records = $q->orderByDesc('visit_date')->paginate(20)->withQueryString();
        return view('doctor.medical_records.index', compact('records'));
    }

    public function exportCsv(Request $request)
    {
        $doctor = Auth::user()->doctor;
        $q = MedicalRecord::with('patient')->where('doctor_id', $doctor->id);
        if ($request->filled('from')) $q->where('visit_date','>=',$request->input('from'));
        if ($request->filled('to')) $q->where('visit_date','<=',$request->input('to'));
        if ($request->filled('search')){
            $s = $request->input('search');
            $q->whereHas('patient', function($b) use ($s){ $b->where('name','like','%'.$s.'%')->orWhere('code','like','%'.$s.'%'); })->orWhere('diagnosis','like','%'.$s.'%');
        }

        $records = $q->orderByDesc('visit_date')->get();

        $response = new StreamedResponse(function() use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id','visit_date','patient_code','patient_name','diagnosis','attachments_count']);
            foreach($records as $r){
                fputcsv($handle, [
                    $r->id,
                    $r->visit_date,
                    $r->patient->code ?? '',
                    $r->patient->name ?? '',
                    str_replace(["\n","\r"],' ', $r->diagnosis),
                    count($r->attachments ?? []),
                ]);
            }
            fclose($handle);
        });

        $filename = 'medical_records_'.date('Ymd_His').'.csv';
        $response->headers->set('Content-Type','text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition','attachment; filename='.$filename);
        return $response;
    }
}
