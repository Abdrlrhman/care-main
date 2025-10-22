@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-2">
        <h2 class="text-2xl font-bold text-blue-700">Invoice #{{ $invoice->id }}</h2>
        <div class="flex gap-2">
            @if($invoice->status != 'paid')
                <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition" data-bs-toggle="modal" data-bs-target="#paymentModal" data-invoice-id="{{ $invoice->id }}" data-invoice-net="{{ $invoice->net_total }}" data-invoice-paid="{{ $invoice->payments->sum('amount') }}">Pay Now</button>
            @endif
            <a href="{{ route('patient.invoices.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 transition">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <div class="bg-white rounded-lg shadow mb-6 p-6">
                <h5 class="text-lg font-semibold text-gray-700 mb-2">Patient</h5>
                <div class="mb-2 text-gray-700">{{ $invoice->patient->name ?? ($invoice->patient->first_name.' '.$invoice->patient->last_name) }}</div>
                <div class="text-gray-500">Phone: {{ $invoice->patient->phone }}</div>
                <hr class="my-4">
                <h5 class="text-lg font-semibold text-gray-700 mb-2">Doctor</h5>
                <div class="mb-2 text-gray-700">{{ $invoice->doctor->user->name ?? $invoice->doctor->name }}</div>
                <div class="text-gray-500">{{ $invoice->doctor->specialty ?? '' }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h5 class="text-lg font-semibold text-gray-700 mb-2">Items</h5>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded shadow">
                        <thead class="bg-blue-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Service</th>
                                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Qty</th>
                                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Price</th>
                                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $it)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $it->service->name ?? 'Service #' . $it->service_id }}</td>
                                    <td class="px-4 py-2 text-right">{{ $it->qty }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($it->unit_price,2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($it->line_total,2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Summary</h5>
                    <div class="d-flex justify-content-between">Subtotal <div>{{ number_format($invoice->total,2) }}</div></div>
                    <div class="d-flex justify-content-between mt-2">Discount <div>{{ number_format($invoice->discount ?? 0,2) }}</div></div>
                    <div class="d-flex justify-content-between mt-2">Net <div>{{ number_format($invoice->net_total,2) }}</div></div>
                    <div class="d-flex justify-content-between mt-2">Payments <div>{{ number_format($paid,2) }}</div></div>
                    <div class="d-flex justify-content-between mt-2">Remaining <div>{{ number_format($remaining,2) }}</div></div>
                    <div class="mt-3 small text-muted">Status: {{ ucfirst($invoice->status) }}</div>
                    <div class="mt-2 small text-muted">Due: {{ optional($invoice->due_date)->toDateString() }}</div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5>Payments</h5>
                    @if($invoice->payments->isEmpty())
                        <div class="text-muted">No payments recorded.</div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($invoice->payments as $p)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <div>{{ ucfirst($p->method) }} {{ $p->reference ? ' - ' . $p->reference : '' }}</div>
                                        <div class="small text-muted">{{ optional($p->paid_at)->toDateTimeString() }}</div>
                                    </div>
                                    <div class="fw-bold">{{ number_format($p->amount,2) }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('patient.invoices._payment_modal')
</div>
@endsection
