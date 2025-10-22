@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-2">
        <h2 class="text-2xl font-bold text-blue-700">Daily Report</h2>
        <form method="GET" class="flex gap-2" action="{{ route('staff.reports.daily') }}">
            <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="border rounded px-3 py-2 text-sm text-gray-700 focus:ring-blue-500">
            <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">Go</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h5 class="text-lg font-semibold text-gray-700 mb-2">Appointments</h5>
            <div class="mb-2">Total today: <span class="font-bold text-blue-700">{{ $total }}</span></div>
            <div class="mb-2">Completed: <span class="font-bold text-green-700">{{ $completed }}</span></div>
            <div>Canceled: <span class="font-bold text-red-700">{{ $canceled }}</span></div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h5 class="text-lg font-semibold text-gray-700 mb-2">Payments</h5>
            <div class="mb-2">Total collected: <span class="font-bold text-blue-700">{{ number_format($totalPayments,2) }}</span></div>
            <div class="mt-2">
                @foreach($byMethod as $m => $amt)
                    <div>{{ ucfirst($m) }}: <span class="font-bold text-blue-700">{{ number_format($amt,2) }}</span></div>
                @endforeach
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex items-end justify-end">
            <a href="{{ route('staff.reports.export', ['date' => $date->format('Y-m-d')]) }}" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">Export CSV</a>
        </div>
    </div>

    <div class="mt-6">
        <h5 class="text-lg font-semibold text-gray-700 mb-4">Payments (detailed)</h5>
        <div class="bg-white rounded-lg shadow">
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>ID</th><th>Invoice</th><th>Amount</th><th>Method</th><th>Reference</th><th>Paid at</th></tr></thead>
                        <tbody>
                            @php
                                $start = $date->copy()->startOfDay();
                                $end = $date->copy()->endOfDay();
                                $payments = \App\Models\Payment::whereBetween('paid_at', [$start, $end])->orderBy('paid_at','desc')->get();
                            @endphp
                            @forelse($payments as $p)
                                <tr>
                                    <td>{{ $p->id }}</td>
                                    <td>{{ $p->invoice_id }}</td>
                                    <td>{{ number_format($p->amount,2) }}</td>
                                    <td>{{ ucfirst($p->method) }}</td>
                                    <td>{{ $p->reference }}</td>
                                    <td>{{ optional($p->paid_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6">No payments for this date.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
