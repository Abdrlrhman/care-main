@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-2">
        <h2 class="text-2xl font-bold text-blue-700">My Invoices</h2>
        <form class="flex gap-2" method="get">
            <select name="status" class="border rounded px-3 py-2 text-sm text-gray-700 focus:ring-blue-500" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="unpaid" {{ request('status')=='unpaid'?'selected':'' }}>Unpaid</option>
                <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Paid</option>
                <option value="overdue" {{ request('status')=='overdue'?'selected':'' }}>Overdue</option>
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($invoices as $inv)
            <div class="bg-white rounded-lg shadow p-5 flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <h5 class="text-lg font-bold text-blue-700">Invoice #{{ $inv->id }}</h5>
                        <div class="text-gray-500">Doctor: {{ $inv->doctor->user->name ?? $inv->doctor->name }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-bold text-blue-700">{{ number_format($inv->net_total,2) }}</div>
                        <div class="text-sm text-gray-400">Due: {{ optional($inv->due_date)->toDateString() }}</div>
                    </div>
                </div>

                <div class="mt-3 mb-2">
                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                        @if($inv->status=='paid') bg-green-100 text-green-700
                        @elseif($inv->status=='partial') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-700 @endif">
                        {{ ucfirst($inv->status) }}
                    </span>
                </div>

                <div class="mt-auto flex gap-2">
                    <a href="{{ route('patient.invoices.show', $inv) }}" class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs">View</a>
                    @if($inv->status != 'paid')
                        <button class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-xs" data-bs-toggle="modal" data-bs-target="#paymentModal" data-invoice-id="{{ $inv->id }}" data-invoice-net="{{ $inv->net_total }}" data-invoice-paid="{{ $inv->payments_sum_amount ?? 0 }}">Pay</button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $invoices->links() }}</div>

    @include('patient.invoices._payment_modal')
</div>
@endsection
