@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Invoice #{{ $invoice->id }}</h3>
        <div>
            <form method="POST" action="{{ route('admin.invoices.cancel', $invoice->id) }}" style="display:inline-block;">
                @csrf
                <button class="btn btn-sm btn-danger">Cancel Invoice</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <h5>Items</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr><th>Service</th><th>Qty</th><th>Unit</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $it)
                        <tr>
                            <td>{{ $it->service_name ?? ($it->service->name ?? '-') }}</td>
                            <td>{{ $it->quantity }}</td>
                            <td>{{ number_format($it->unit_price,2) }}</td>
                            <td>{{ number_format($it->line_total,2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h5 class="mt-4">Payments</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Amount</th><th>Method</th><th>Paid At</th><th>Reference</th></tr></thead>
                    <tbody>
                        @foreach($invoice->payments as $p)
                        <tr>
                            <td>{{ number_format($p->amount,2) }}</td>
                            <td>{{ $p->method }}</td>
                            <td>{{ $p->paid_at }}</td>
                            <td>{{ $p->reference }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Summary</h5>
                    <div>Total: {{ number_format($invoice->total,2) }}</div>
                    <div>Net: {{ number_format($invoice->net_total,2) }}</div>
                    <div>Status: {{ $invoice->status }}</div>
                    <div>Due: {{ $invoice->due_date ? $invoice->due_date->toDateString() : '-' }}</div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6>Add Payment</h6>
                    <form method="POST" action="{{ route('admin.invoices.add_payment', $invoice->id) }}">
                        @csrf
                        <div class="mb-2"><input name="amount" type="number" step="0.01" class="form-control" placeholder="Amount" required></div>
                        <div class="mb-2"><input name="method" class="form-control" placeholder="Method (cash/card)" required></div>
                        <div class="mb-2"><input name="reference" class="form-control" placeholder="Reference"></div>
                        <button class="btn btn-sm btn-success">Record Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile collapsible cards --}}
    <div class="d-md-none mt-3">
        <div class="card mb-2">
            <div class="card-body">
                <h6>Items</h6>
                @foreach($invoice->items as $it)
                <div class="d-flex justify-content-between border-top py-1"><div>{{ $it->service_name ?? ($it->service->name ?? '-') }} x{{ $it->quantity }}</div><div>{{ number_format($it->line_total,2) }}</div></div>
                @endforeach
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-body">
                <h6>Payments</h6>
                @foreach($invoice->payments as $p)
                <div class="d-flex justify-content-between border-top py-1"><div>{{ number_format($p->amount,2) }} — {{ $p->method }}</div><div>{{ $p->paid_at }}</div></div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection