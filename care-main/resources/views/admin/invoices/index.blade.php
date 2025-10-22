@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h3 class="mb-2 mb-md-0"><i class="bi bi-receipt me-2"></i> الفواتير</h3>
        <!-- إذا كان لديك راوت لإنشاء فاتورة، أضفه هنا. وإلا، احذف الزر -->
        {{-- <a href="{{ route('admin.invoices.create') }}" class="btn btn-sm btn-primary d-flex align-items-center">
            <i class="bi bi-plus-circle me-1"></i> إنشاء فاتورة
        </a> --}}
    </div>

    <!-- Filters Form -->
    <form class="row g-2 mb-4 p-3 bg-white rounded shadow-sm" style="border: 1px solid #e9ecef;">
        <div class="col-12 col-md-6 col-lg-2">
            <label class="form-label small fw-bold d-none d-md-block">الحالة</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">أي حالة</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>جزئي</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغى</option>
            </select>
        </div>
        <div class="col-12 col-md-6 col-lg-2">
            <label class="form-label small fw-bold d-none d-md-block">كود الطبيب</label>
            <input name="doctor_id" value="{{ request('doctor_id') }}" class="form-control form-control-sm" placeholder="كود الطبيب">
        </div>
        <div class="col-12 col-md-6 col-lg-2">
            <label class="form-label small fw-bold d-none d-md-block">كود المريض</label>
            <input name="patient_id" value="{{ request('patient_id') }}" class="form-control form-control-sm" placeholder="كود المريض">
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <label class="form-label small fw-bold d-none d-md-block">من تاريخ</label>
            <input name="from" type="date" value="{{ request('from') }}" class="form-control form-control-sm">
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <label class="form-label small fw-bold d-none d-md-block">إلى تاريخ</label>
            <input name="to" type="date" value="{{ request('to') }}" class="form-control form-control-sm">
        </div>
        <div class="col-12 text-end mt-2">
            <button type="submit" class="btn btn-sm btn-outline-primary px-4">تصفية</button>
            @if(request()->anyFilled(['status', 'doctor_id', 'patient_id', 'from', 'to']))
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-sm btn-link text-decoration-none">إعادة تعيين</a>
            @endif
        </div>
    </form>

    <!-- Desktop Table -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>المريض</th>
                    <th>الطبيب</th>
                    <th>الإجمالي</th>
                    <th>الصافي</th>
                    <th>الحالة</th>
                    <th>تاريخ الاستحقاق</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td class="fw-bold">#{{ $inv->id }}</td>
                    <td>{{ $inv->patient->name ?? '<span class="text-muted">غير محدد</span>' }}</td>
                    <td>{{ $inv->doctor->name ?? '<span class="text-muted">غير محدد</span>' }}</td>
                    <td>{{ number_format($inv->total, 2) }} ر.س</td>
                    <td class="fw-bold text-success">{{ number_format($inv->net_total, 2) }} ر.س</td>
                    <td>
                        @php
                            $badgeClass = [
                                'draft' => 'bg-secondary',
                                'partial' => 'bg-warning',
                                'paid' => 'bg-success',
                                'cancelled' => 'bg-danger'
                            ][$inv->status] ?? 'bg-light text-dark';
                        @endphp
                        <span class="badge {{ $badgeClass }} text-white text-uppercase">{{ $inv->status }}</span>
                    </td>
                    <td>{{ $inv->due_date ? $inv->due_date->format('Y-m-d') : '<span class="text-muted">—</span>' }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-secondary px-3">
                            <i class="bi bi-eye"></i> عرض
                        </a>

                        @if($inv->status !== 'cancelled' && $inv->status !== 'paid')
                            <form method="POST" action="{{ route('admin.invoices.add_payment', $inv->id) }}" class="d-inline-block ms-1">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-info px-3" onclick="return confirm('هل تريد إضافة دفعة لهذه الفاتورة؟')">
                                    <i class="bi bi-cash"></i> دفعة
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.invoices.cancel', $inv->id) }}" class="d-inline-block ms-1">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger px-3" onclick="return confirm('هل أنت متأكد من إلغاء هذه الفاتورة؟')">
                                    <i class="bi bi-x-circle"></i> إلغاء
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">لا توجد فواتير مطابقة للتصفية الحالية.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="d-md-none">
        @forelse($invoices as $inv)
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1 fw-bold">فاتورة #{{ $inv->id }}</h6>
                        <div class="small text-muted">
                            <i class="bi bi-person me-1"></i> {{ $inv->patient->name ?? '—' }}<br>
                            <i class="bi bi-stethoscope me-1"></i> {{ $inv->doctor->name ?? '—' }}
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-success">{{ number_format($inv->net_total, 2) }} ر.س</div>
                        <span class="badge {{ [
                            'draft' => 'bg-secondary',
                            'partial' => 'bg-warning',
                            'paid' => 'bg-success',
                            'cancelled' => 'bg-danger'
                        ][$inv->status] ?? 'bg-light text-dark' }} text-white text-uppercase small">
                            {{ $inv->status }}
                        </span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
                    <span><i class="bi bi-calendar me-1"></i> {{ $inv->due_date ? $inv->due_date->format('Y-m-d') : '—' }}</span>
                    <span><i class="bi bi-cash me-1"></i> {{ number_format($inv->total, 2) }} ر.س</span>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-2 flex-wrap">
                    <a href="{{ route('admin.invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-secondary flex-grow-1">
                        <i class="bi bi-eye"></i> عرض
                    </a>

                    @if($inv->status !== 'cancelled' && $inv->status !== 'paid')
                        <form method="POST" action="{{ route('admin.invoices.add_payment', $inv->id) }}" class="d-inline-block w-100 mt-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-info w-100" onclick="return confirm('هل تريد إضافة دفعة لهذه الفاتورة؟')">
                                <i class="bi bi-cash"></i> إضافة دفعة
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.invoices.cancel', $inv->id) }}" class="d-inline-block w-100 mt-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('هل أنت متأكد من إلغاء هذه الفاتورة؟')">
                                <i class="bi bi-x-circle"></i> إلغاء الفاتورة
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-receipt mb-2" style="font-size: 2rem;"></i>
            <div>لا توجد فواتير</div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($invoices->hasPages())
        <div class="mt-4">
            {{ $invoices->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection