@extends('layouts.app')

@section('content')
<div class="container mx-auto px-2 py-4">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-3 gap-2">
        <h3 class="text-lg font-bold">فاتورة #{{ $invoice->id }}</h3>
        <div>
            <form method="POST" action="{{ route('admin.invoices.cancel', $invoice->id) }}" class="inline-block">
                @csrf
                <button class="bg-red-600 text-white text-xs px-3 py-2 rounded hover:bg-red-700 transition">إلغاء الفاتورة</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-8">
            <h5 class="font-bold mb-2">الخدمات</h5>
            <div class="overflow-x-auto rounded shadow mb-4">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100 text-xs">
                        <tr>
                            <th class="px-3 py-2 text-right">الخدمة</th>
                            <th class="px-3 py-2 text-right">الكمية</th>
                            <th class="px-3 py-2 text-right">سعر الوحدة</th>
                            <th class="px-3 py-2 text-right">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $it)
                        <tr class="border-b">
                            <td class="px-3 py-2 text-right">{{ $it->service_name ?? ($it->service->name ?? '-') }}</td>
                            <td class="px-3 py-2 text-right">{{ $it->quantity }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($it->unit_price,2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($it->line_total,2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h5 class="font-bold mb-2">المدفوعات</h5>
            <div class="overflow-x-auto rounded shadow">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100 text-xs">
                        <tr>
                            <th class="px-3 py-2 text-right">المبلغ</th>
                            <th class="px-3 py-2 text-right">طريقة الدفع</th>
                            <th class="px-3 py-2 text-right">تاريخ الدفع</th>
                            <th class="px-3 py-2 text-right">المرجع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $p)
                        <tr class="border-b">
                            <td class="px-3 py-2 text-right">{{ number_format($p->amount,2) }}</td>
                            <td class="px-3 py-2 text-right">{{ $p->method }}</td>
                            <td class="px-3 py-2 text-right">{{ $p->paid_at }}</td>
                            <td class="px-3 py-2 text-right">{{ $p->reference }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="md:col-span-4 flex flex-col gap-4">
            <div class="bg-white rounded shadow p-4 mb-3">
                <h5 class="font-bold mb-2">الملخص</h5>
                <div class="mb-1">الإجمالي: <span class="font-bold">{{ number_format($invoice->total,2) }}</span></div>
                <div class="mb-1">الصافي: <span class="font-bold text-green-600">{{ number_format($invoice->net_total,2) }}</span></div>
                <div class="mb-1">الحالة: <span class="font-bold">{{ $invoice->status }}</span></div>
                <div class="mb-1">تاريخ الاستحقاق: <span class="font-bold">{{ $invoice->due_date ? $invoice->due_date->toDateString() : '-' }}</span></div>
            </div>

            <div class="bg-white rounded shadow p-4">
                <h6 class="font-bold mb-2">إضافة دفعة</h6>
                <form method="POST" action="{{ route('admin.invoices.add_payment', $invoice->id) }}">
                    @csrf
                    <div class="mb-2">
                        <input name="amount" type="number" step="0.01" class="block w-full rounded border-gray-300 text-sm py-2 px-2" placeholder="المبلغ" required>
                    </div>
                    <div class="mb-2">
                        <input name="method" class="block w-full rounded border-gray-300 text-sm py-2 px-2" placeholder="طريقة الدفع (نقداً/بطاقة)" required>
                    </div>
                    <div class="mb-2">
                        <input name="reference" class="block w-full rounded border-gray-300 text-sm py-2 px-2" placeholder="المرجع">
                    </div>
                    <button class="bg-green-600 text-white text-xs px-4 py-2 rounded hover:bg-green-700 transition">تسجيل دفعة</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Mobile collapsible cards --}}
    <div class="md:hidden mt-4 flex flex-col gap-2">
        <div class="bg-white rounded shadow p-3">
            <h6 class="font-bold mb-2">الخدمات</h6>
            @foreach($invoice->items as $it)
            <div class="flex justify-between border-t py-1 text-sm">
                <div>{{ $it->service_name ?? ($it->service->name ?? '-') }} ×{{ $it->quantity }}</div>
                <div>{{ number_format($it->line_total,2) }}</div>
            </div>
            @endforeach
        </div>
        <div class="bg-white rounded shadow p-3">
            <h6 class="font-bold mb-2">المدفوعات</h6>
            @foreach($invoice->payments as $p)
            <div class="flex justify-between border-t py-1 text-sm">
                <div>{{ number_format($p->amount,2) }} — {{ $p->method }}</div>
                <div>{{ $p->paid_at }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection