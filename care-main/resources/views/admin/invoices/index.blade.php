@extends('layouts.app')

@section('content')
<div class="container mx-auto px-2 py-4">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-2">
        <h3 class="text-lg font-bold flex items-center gap-2 mb-2 md:mb-0">
            <i class="bi bi-receipt"></i> الفواتير
        </h3>
        {{-- <a href="{{ route('admin.invoices.create') }}" class="btn btn-sm btn-primary d-flex align-items-center">
            <i class="bi bi-plus-circle me-1"></i> إنشاء فاتورة
        </a> --}}
    </div>

    <!-- Filters Form -->
    <form class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 mb-4 p-3 bg-white rounded shadow border border-gray-200">
        <div>
            <label class="block text-xs font-bold mb-1">الحالة</label>
            <select name="status" class="block w-full rounded border-gray-300 text-sm py-2 px-2">
                <option value="">أي حالة</option>
                <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>غير مدفوع</option>
                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>جزئي</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغى</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold mb-1">كود الطبيب</label>
            <input name="doctor_id" value="{{ request('doctor_id') }}" class="block w-full rounded border-gray-300 text-sm py-2 px-2" placeholder="كود الطبيب">
        </div>
        <div>
            <label class="block text-xs font-bold mb-1">كود المريض</label>
            <input name="patient_id" value="{{ request('patient_id') }}" class="block w-full rounded border-gray-300 text-sm py-2 px-2" placeholder="كود المريض">
        </div>
        <div>
            <label class="block text-xs font-bold mb-1">من تاريخ</label>
            <input name="from" type="date" value="{{ request('from') }}" class="block w-full rounded border-gray-300 text-sm py-2 px-2">
        </div>
        <div>
            <label class="block text-xs font-bold mb-1">إلى تاريخ</label>
            <input name="to" type="date" value="{{ request('to') }}" class="block w-full rounded border-gray-300 text-sm py-2 px-2">
        </div>
        <div class="col-span-1 md:col-span-2 lg:col-span-5 flex justify-end items-center gap-2 mt-2">
            <button type="submit" class="bg-blue-600 text-white text-sm px-4 py-2 rounded hover:bg-blue-700 transition">تصفية</button>
            @if(request()->anyFilled(['status', 'doctor_id', 'patient_id', 'from', 'to']))
                <a href="{{ route('admin.invoices.index') }}" class="text-blue-600 text-sm underline">إعادة تعيين</a>
            @endif
        </div>
    </form>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-right text-xs font-bold">#</th>
                    <th class="px-3 py-2 text-right text-xs font-bold">المريض</th>
                    <th class="px-3 py-2 text-right text-xs font-bold">الطبيب</th>
                    <th class="px-3 py-2 text-right text-xs font-bold">الإجمالي</th>
                    <th class="px-3 py-2 text-right text-xs font-bold">الصافي</th>
                    <th class="px-3 py-2 text-right text-xs font-bold">الحالة</th>
                    <th class="px-3 py-2 text-right text-xs font-bold">تاريخ الاستحقاق</th>
                    <th class="px-3 py-2 text-center text-xs font-bold">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr class="border-b">
                    <td class="px-3 py-2 font-bold text-right">#{{ $inv->id }}</td>
                    <td class="px-3 py-2 text-right">{!! $inv->patient->name ?? '<span class="text-gray-400">غير محدد</span>' !!}</td>
                    <td class="px-3 py-2 text-right">{!! $inv->doctor->name ?? '<span class="text-gray-400">غير محدد</span>' !!}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($inv->total, 2) }} ر.س</td>
                    <td class="px-3 py-2 font-bold text-green-600 text-right">{{ number_format($inv->net_total, 2) }} ر.س</td>
                    <td class="px-3 py-2 text-right">
                        @php
                            $badgeClass = [
                                'unpaid' => 'bg-gray-900',
                                'partial' => 'bg-yellow-400',
                                'paid' => 'bg-green-600',
                                'cancelled' => 'bg-red-600'
                            ][$inv->status] ?? 'bg-gray-200 text-gray-800';
                        @endphp
                        <span class="inline-block px-2 py-1 rounded text-xs font-bold text-white {{ $badgeClass }}">{{ $inv->status }}</span>
                    </td>
                    <td class="px-3 py-2 text-right">{!! $inv->due_date ? $inv->due_date->format('Y-m-d') : '<span class="text-gray-400">—</span>' !!}</td>
                    <td class="px-3 py-2 text-center">
                        <a href="{{ route('admin.invoices.show', $inv->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1 rounded transition">
                            <i class="bi bi-eye"></i> عرض
                        </a>
                        @if($inv->status !== 'cancelled' && $inv->status !== 'paid')
                            <form method="POST" action="{{ route('admin.invoices.add_payment', $inv->id) }}" class="inline-block ml-1">
                                @csrf
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded transition" onclick="return confirm('هل تريد إضافة دفعة لهذه الفاتورة؟')">
                                    <i class="bi bi-cash"></i> دفعة
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.invoices.cancel', $inv->id) }}" class="inline-block ml-1">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded transition" onclick="return confirm('هل أنت متأكد من إلغاء هذه الفاتورة؟')">
                                    <i class="bi bi-x-circle"></i> إلغاء
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-gray-400">لا توجد فواتير مطابقة للتصفية الحالية.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden">
        @forelse($invoices as $inv)
        <div class="bg-white rounded shadow mb-3 border border-gray-200">
            <div class="p-3">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h6 class="mb-1 font-bold text-base">فاتورة #{{ $inv->id }}</h6>
                        <div class="text-xs text-gray-500">
                            <i class="bi bi-person mr-1"></i> {{ $inv->patient->name ?? '—' }}<br>
                            <i class="bi bi-stethoscope mr-1"></i> {{ $inv->doctor->name ?? '—' }}
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="font-bold text-green-600">{{ number_format($inv->net_total, 2) }} ر.س</div>
                        <span class="inline-block px-2 py-1 rounded text-xs font-bold text-white
                            {{
                                [
                                    'unpaid' => 'bg-gray-900',
                                    'partial' => 'bg-yellow-400',
                                    'paid' => 'bg-green-600',
                                    'cancelled' => 'bg-red-600'
                                ][$inv->status] ?? 'bg-gray-200 text-gray-800'
                            }}">
                            {{ $inv->status }}
                        </span>
                    </div>
                </div>
                <div class="flex justify-between items-center text-xs text-gray-500 mb-2">
                    <span><i class="bi bi-calendar mr-1"></i> {{ $inv->due_date ? $inv->due_date->format('Y-m-d') : '—' }}</span>
                    <span><i class="bi bi-cash mr-1"></i> {{ number_format($inv->total, 2) }} ر.س</span>
                </div>
                <div class="flex flex-col gap-2 mt-2">
                    <a href="{{ route('admin.invoices.show', $inv->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-2 rounded transition w-full text-center">
                        <i class="bi bi-eye"></i> عرض
                    </a>
                    @if($inv->status !== 'cancelled' && $inv->status !== 'paid')
                        <form method="POST" action="{{ route('admin.invoices.add_payment', $inv->id) }}" class="w-full">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-2 rounded transition w-full" onclick="return confirm('هل تريد إضافة دفعة لهذه الفاتورة؟')">
                                <i class="bi bi-cash"></i> إضافة دفعة
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.invoices.cancel', $inv->id) }}" class="w-full">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-2 rounded transition w-full" onclick="return confirm('هل أنت متأكد من إلغاء هذه الفاتورة؟')">
                                <i class="bi bi-x-circle"></i> إلغاء الفاتورة
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-gray-400">
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