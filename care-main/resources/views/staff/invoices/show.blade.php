@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Invoice #{{ $invoice->id }}</h2>
        <div>
            <a href="{{ route('staff.invoices.edit', $invoice->id) }}" class="btn btn-secondary">Edit</a>
            @if($invoice->status !== 'paid')
                <form method="POST" action="{{ route('staff.invoices.markPaid', $invoice->id) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-success">Mark Paid</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Patient</h5>
                    <div>{{ optional($invoice->patient)->name }} • {{ optional($invoice->patient)->phone }}</div>
                    <hr>
                    <h5>Doctor</h5>
                    <div>{{ optional($invoice->doctor)->name ?? '—' }}</div>
                    <hr>
                    <h5>Items</h5>
                    <table class="table table-sm">
                        <thead><tr><th>Service</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
                        <tbody>
                            @foreach($invoice->items as $it)
                                <tr>
                                    <td>{{ optional($it->service)->name ?? '—' }}</td>
                                    <td>{{ $it->qty }}</td>
                                    <td>{{ number_format($it->unit_price,2) }}</td>
                                    <td>{{ number_format($it->line_total,2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-end">Subtotal: <strong>{{ number_format($invoice->total,2) }}</strong></div>
                    <div class="text-end">Discount: <strong>{{ number_format($invoice->discount ?? 0,2) }}</strong></div>
                    <div class="text-end">Net: <strong>{{ number_format($invoice->net_total,2) }}</strong></div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5>Notes</h5>
                    <div class="text-muted">{{ $invoice->notes }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Status</h5>
                    <div>{{ ucfirst($invoice->status) }}</div>
                    <hr>
                    <h5>Outstanding</h5>
                    @php
                        $paidSum = $invoice->payments->sum('amount');
                        $outstanding = ($invoice->net_total ?? $invoice->total) - $paidSum;
                    @endphp
                    <div><strong>{{ number_format($outstanding,2) }}</strong></div>
                    <div class="mt-2">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">Record Payment</button>
                    </div>
                    <hr>
                    <h5>Payments</h5>
                    @forelse($invoice->payments as $p)
                        <div class="mb-1">{{ number_format($p->amount,2) }} • {{ optional($p->paid_at)->format('Y-m-d H:i') }} <br><small class="text-muted">{{ $p->method }} {{ $p->reference ? '• '.$p->reference : '' }}</small></div>
                    @empty
                        <div class="text-muted">No payments</div>
                    @endforelse
                </div>
            </div>

            @include('staff.invoices._payment_modal')
        </div>
    </div>
</div>

@endsection
