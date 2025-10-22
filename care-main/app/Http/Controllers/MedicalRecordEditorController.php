<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalRecordEditorController extends Controller
{
    public function create(Request $request)
    {
        $patientId = $request->query('patient_id');
        $doctorId = auth()->user()->doctor->id ?? null;
        $record = new MedicalRecord(['patient_id'=>$patientId,'doctor_id'=>$doctorId,'visit_date'=>now()]);
        // prefill prescriptions if provided via ?prefill=1,2,3 (ids)
        $prefill = [];
        if ($request->filled('prefill')){
            $ids = array_filter(explode(',', $request->input('prefill')));
            $prefill = Prescription::whereIn('id',$ids)->get()->map(function($p){
                return [
                    'drug_name'=>$p->drug_name,
                    'dosage'=>$p->dosage,
                    'frequency'=>$p->frequency,
                    'duration'=>$p->duration,
                    'notes'=>$p->notes,
                ];
            })->toArray();
        }
        return view('admin.medical_records.editor', compact('record','prefill'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        return view('admin.medical_records.editor', ['record' => $medicalRecord]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'visit_date' => 'required|date',
            'diagnosis' => 'required|string',
            'notes' => 'nullable|string',
            'prescriptions' => 'array',
        ]);

        DB::transaction(function() use ($data, $request){
            $record = MedicalRecord::create($data);
            $pres = $request->input('prescriptions', []);
            foreach($pres as $p){
                if(!empty($p['drug_name'])){
                    Prescription::create(array_merge($p, ['medical_record_id' => $record->id]));
                }
            }
        });

        return redirect()->route('admin.patients.show', $data['patient_id'])->with('success','تم حفظ السجل');
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $data = $request->validate([
            'visit_date' => 'required|date',
            'diagnosis' => 'required|string',
            'notes' => 'nullable|string',
            'prescriptions' => 'array',
        ]);

        DB::transaction(function() use ($medicalRecord, $data, $request){
            // lock structural fields after finalize
            $medicalRecord->update($data);
            // replace prescriptions: simple approach: delete and recreate
            $medicalRecord->prescriptions()->delete();
            foreach($request->input('prescriptions', []) as $p){
                if(!empty($p['drug_name'])){
                    Prescription::create(array_merge($p, ['medical_record_id'=>$medicalRecord->id]));
                }
            }
        });

        return redirect()->route('admin.patients.show', $medicalRecord->patient_id)->with('success','تم تحديث السجل');
    }
}
