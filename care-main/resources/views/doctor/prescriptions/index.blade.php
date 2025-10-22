@extends('layouts.app')

@section('content')
<main class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5">الوصفات الطبية</h2>
    </div>

    <form class="row g-2 mb-3" method="GET">
        <div class="col-12 col-md-4"><input type="search" name="patient" value="{{ request('patient') }}" class="form-control" placeholder="مريض (اسم أو كود)"></div>
        <div class="col-12 col-md-3"><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
        <div class="col-12 col-md-3"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
        <div class="col-12 col-md-2"><input type="search" name="drug" value="{{ request('drug') }}" class="form-control" placeholder="اسم الدواء"></div>
    </form>

    <div class="table-responsive d-none d-md-block">
        <table class="table align-middle">
            <thead>
                <tr><th>التاريخ</th><th>المريض</th><th>الدواء</th><th>جرعة</th><th>تكرار</th><th>مدة</th><th>ملاحظات</th><th>إجراءات</th></tr>
            </thead>
            <tbody>
                @foreach($prescriptions as $pres)
                    <tr>
                        <td>{{ $pres->medicalRecord->visit_date->format('Y-m-d') }}</td>
                        <td>{{ $pres->medicalRecord->patient->name }}</td>
                        <td>{{ $pres->drug_name }}</td>
                        <td>{{ $pres->dosage }}</td>
                        <td>{{ $pres->frequency }}</td>
                        <td>{{ $pres->duration }}</td>
                        <td>{{ Str::limit($pres->notes,60) }}</td>
                        <td>
                            <a href="{{ route('admin.medical_records.edit', $pres->medical_record_id) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                            <a href="{{ route('admin.medical_records.edit', $pres->medical_record_id) }}?print=1" class="btn btn-sm btn-outline-secondary">طباعة</a>
                            <a href="{{ route('admin.medical_records.create') }}?patient_id={{ $pres->medicalRecord->patient_id }}&prefill={{ $pres->id }}" class="btn btn-sm btn-success">إعادة استخدام</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-md-none">
        @foreach($prescriptions as $pres)
            <div class="card mb-2">
                <div class="card-body">
                    <div class="fw-bold">{{ $pres->drug_name }} • {{ $pres->dosage }}</div>
                    <div class="small text-muted">{{ $pres->medicalRecord->patient->name }} • {{ $pres->medicalRecord->visit_date->format('Y-m-d') }}</div>
                    <div class="mt-2">{{ Str::limit($pres->notes,120) }}</div>
                    <div class="mt-2 text-end">
                        <a href="{{ route('admin.medical_records.create') }}?patient_id={{ $pres->medicalRecord->patient_id }}&prefill={{ $pres->id }}" class="btn btn-sm btn-success">إعادة استخدام</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3">{{ $prescriptions->links() }}</div>
</main>
@endsection