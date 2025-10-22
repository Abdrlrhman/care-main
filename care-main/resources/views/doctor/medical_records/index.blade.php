@extends('layouts.app')

@section('content')
<main class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5">السجلات الطبية</h2>
        <div>
            <a href="?" class="btn btn-outline-secondary">إعادة</a>
            <a href="{{ url()->current() . '?' . http_build_query(request()->all()) }}&export=1" id="exportCsv" class="btn btn-sm btn-outline-primary">Export CSV</a>
        </div>
    </div>

    <form class="row g-2 mb-3" method="GET">
        <div class="col-12 col-md-3"><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
        <div class="col-12 col-md-3"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
        <div class="col-12 col-md-4"><input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="بحث باسم المريض/الكود أو التشخيص"></div>
        <div class="col-12 col-md-2 text-end"><button class="btn btn-primary">تطبيق</button></div>
    </form>

    <div class="table-responsive d-none d-md-block">
        <table class="table align-middle">
            <thead>
                <tr><th>التاريخ</th><th>المريض</th><th>التشخيص</th><th>مرفقات</th><th>إجراءات</th></tr>
            </thead>
            <tbody>
                @foreach($records as $r)
                    <tr>
                        <td>{{ $r->visit_date->format('Y-m-d H:i') }}</td>
                        <td>{{ $r->patient->name }} <div class="small text-muted">{{ $r->patient->code }}</div></td>
                        <td>{{ Str::limit($r->diagnosis,120) }}</td>
                        <td>{{ count($r->attachments ?? []) }}</td>
                        <td>
                            <a href="{{ route('admin.medical_records.edit', $r->id) }}" class="btn btn-sm btn-outline-primary">عرض/تعديل</a>
                            <a href="{{ route('admin.medical_records.edit', $r->id) }}?print=1" class="btn btn-sm btn-outline-secondary">طباعة</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-md-none">
        @foreach($records as $r)
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fw-bold">{{ $r->visit_date->format('Y-m-d H:i') }}</div>
                            <div>{{ $r->patient->name }} • {{ $r->patient->code }}</div>
                            <div class="small text-muted">{{ Str::limit($r->diagnosis,120) }}</div>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('admin.medical_records.edit', $r->id) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3">{{ $records->links() }}</div>
</main>
@endsection