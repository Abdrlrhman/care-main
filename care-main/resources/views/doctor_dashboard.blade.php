@extends('layouts.app')

@section('content')
<main class="container py-3" aria-labelledby="doctor-dashboard-title">
    <h1 id="doctor-dashboard-title" class="h4 mb-3">لوحة الطبيب - {{ $doctor->name }}</h1>

    <div class="row g-3">
        <!-- Top row: profile & notifications -->
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between bg-white p-3 rounded shadow-sm">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('storage/profile-placeholder.png') }}" alt="profile" class="rounded-circle me-3" width="56" height="56">
                    <div>
                        <div class="fw-bold">{{ $doctor->name }}</div>
                        <div class="text-muted small">{{ $doctor->specialty }}</div>
                    </div>
                </div>
                <div>
                    <button class="btn btn-link position-relative" aria-label="Notifications" id="notif-btn">
                        إشعارات
                        <span class="badge bg-danger ms-1">{{$notifications?$notifications->count() : 0 }}</span>
        <!-- New layout with Tailwind CSS -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row items-center justify-between bg-white p-5 rounded-lg shadow">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('storage/profile-placeholder.png') }}" alt="profile" class="rounded-full w-14 h-14">
                    <div>
                        <div class="font-bold text-lg text-blue-700">{{ $doctor->name }}</div>
                        <div class="text-gray-500 text-sm">{{ $doctor->specialty }}</div>
                    </div>
                </div>
                <div>
                    <button class="relative px-4 py-2 rounded bg-blue-100 text-blue-700 font-semibold hover:bg-blue-200 transition" aria-label="Notifications" id="notif-btn">
                        إشعارات
                        <span class="absolute top-0 right-0 -mt-2 -mr-2 px-2 py-1 rounded-full bg-red-600 text-white text-xs">{{$notifications?$notifications->count() : 0 }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow text-center p-5">
                <div class="text-sm text-gray-500 mb-2">مواعيد اليوم</div>
                <div class="text-2xl font-bold text-blue-700">{{ $appointments->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow text-center p-5">
                <div class="text-sm text-gray-500 mb-2">فواتير غير مدفوعة</div>
                <div class="text-2xl font-bold text-blue-700">{{ $invoices->count() }} / {{ number_format($invoices->sum('net_total'),2) }} SDG</div>
            </div>
            <div class="bg-white rounded-lg shadow text-center p-5">
                <div class="text-sm text-gray-500 mb-2">مرضى جدد (7 أيام)</div>
                <div class="text-2xl font-bold text-blue-700">{{ $newPatients->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow text-center p-5">
                <div class="text-sm text-gray-500 mb-2">إشعارات غير مقرؤة</div>
                <div class="text-2xl font-bold text-blue-700">{{ $notifications?$notifications->count() : 0 }}</div>
            </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- KPI cards -->
        <div class="col-6 col-md-3" role="region" aria-label="Today's appointments">
            <div class="card p-3 h-100">
                <div class="small text-muted">مواعيد اليوم</div>
                <div class="h5">{{ $appointments->count() }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3" role="region" aria-label="Pending invoices">
            <div class="card p-3 h-100">
                <div class="small text-muted">فواتير غير مدفوعة</div>
                <div class="h5">{{ $invoices->count() }} / {{ number_format($invoices->sum('net_total'),2) }} SDG</div>
            </div>
        </div>
        <div class="col-6 col-md-3" role="region" aria-label="New patients">
            <div class="card p-3 h-100">
                <div class="small text-muted">مرضى جدد (7 أيام)</div>
                <div class="h5">{{ $newPatients->count() }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3" role="region" aria-label="Unread notifications">
            <div class="card p-3 h-100">
                <div class="small text-muted">إشعارات غير مقروءة</div>
                <div class="h5">{{ $notifications?$notifications->count() : 0 }}</div>
            </div>
        </div>

        <!-- Panels -->
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">مواعيد اليوم</div>
                <div class="card-body">
                    @if($appointments->isEmpty())
                        <div class="text-center text-muted">لا توجد مواعيد اليوم.</div>
                    @else
                    <div class="table-responsive d-none d-md-block">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>الوقت</th>
                                    <th>المريض</th>
                                    <th>الحالة</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $appt)
                                    <tr>
                                        <td>{{ $appt->starts_at->format('H:i') }} - {{ $appt->ends_at->format('H:i') }}</td>
                                        <td><a href="{{ route('admin.patients.show', $appt->patient->id ?? $appt->patient->getKey()) }}">{{ $appt->patient->name }}</a></td>
                                        <td>{{ $appt->status }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary start-visit" data-id="{{ $appt->id }}">ابدأ الزيارة</button>
                                            <button class="btn btn-sm btn-secondary reschedule" data-id="{{ $appt->id }}">إعادة جدولة</button>
                                            <button class="btn btn-sm btn-danger cancel-appt" data-id="{{ $appt->id }}">إلغاء</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- mobile cards -->
                    <div class="d-md-none">
                        @foreach($appointments as $appt)
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="fw-bold">{{ $appt->patient->name }}</div>
                                            <div class="small text-muted">{{ $appt->starts_at->format('H:i') }} • {{ $appt->status }}</div>
                                        </div>
                                        <div class="text-end">
                                            <button class="btn btn-sm btn-primary start-visit" data-id="{{ $appt->id }}">ابدأ</button>
                                            <button class="btn btn-sm btn-secondary reschedule" data-id="{{ $appt->id }}">جدولة</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">المرضى الجدد</div>
                <div class="card-body">
                    @if($newPatients->isEmpty())
                        <div class="text-center text-muted">لا يوجد مرضى جدد خلال 7 أيام.</div>
                    @else
                        <ul class="list-group">
                            @foreach($newPatients as $p)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('admin.patients.show', $p->id) }}" class="fw-bold">{{ $p->name }}</a>
                                        <div class="small text-muted">{{ $p->code }} • آخر زيارة --</div>
                                    </div>
                                    <a href="{{ route('admin.patients.show', $p->id) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">فواتير معلقة</div>
                <div class="card-body">
                    @if($invoices->isEmpty())
                        <div class="text-center text-muted">لا توجد فواتير معلقة.</div>
                    @else
                        <div class="table-responsive d-none d-md-block">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>المريض</th>
                                        <th>المجموع</th>
                                        <th>تاريخ الاستحقاق</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $inv)
                                        <tr>
                                            <td>{{ $inv->id }}</td>
                                            <td>{{ $inv->patient->name }}</td>
                                            <td>{{ number_format($inv->net_total,2) }}</td>
                                            <td>{{ optional($inv->due_date)->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-md-none">
                            @foreach($invoices as $inv)
                                <div class="card mb-2">
                                    <div class="card-body d-flex justify-content-between">
                                        <div>
                                            <div class="fw-bold">فاتورة #{{ $inv->id }}</div>
                                            <div class="small text-muted">{{ $inv->patient->name }}</div>
                                        </div>
                                        <div class="text-end">{{ number_format($inv->net_total,2) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</main>

@push('scripts')
<script>
document.addEventListener('click', function(e){
    const startBtn = e.target.closest('.start-visit');
    const reschBtn = e.target.closest('.reschedule');
    const cancelBtn = e.target.closest('.cancel-appt');

    if(startBtn){
        const id = startBtn.dataset.id;
        // redirect to medical record create for appointment
        window.location.href = `/admin/appointments/${id}/start`;
    }
    if(reschBtn){
        const id = reschBtn.dataset.id;
        // open edit
        window.location.href = `/admin/appointments/${id}/edit`;
    }
    if(cancelBtn){
        const id = cancelBtn.dataset.id;
        if(confirm('هل أنت متأكد من إلغاء الموعد؟')){
            fetch(`/admin/appointments/${id}`,{method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
            .then(()=> location.reload());
        }
    }
});
</script>
@endpush

@endsection
