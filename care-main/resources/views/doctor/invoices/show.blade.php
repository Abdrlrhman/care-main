@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Invoice #{{ $invoice->id }}</h2>
    <div class="row">
        <div class="col-md-8">
            <h5>Patient</h5>
            <div>{{ $invoice->patient->full_name ?? $invoice->patient->first_name.' '.$invoice->patient->last_name }}</div>
            <h5 class="mt-3">Items</h5>
            <table class="table">
                <thead><tr><th>Service</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
                <tbody>
                @foreach($invoice->items as $it)
                    <tr>
                        <td>{{ $it->service->name }}</td>
                        <td>{{ $it->qty }}</td>
                        <td>{{ number_format($it->unit_price,2) }}</td>
                        <td>{{ number_format($it->line_total,2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <h5>Notes</h5>
            <div>{{ $invoice->notes }}</div>
        </div>
    <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">Subtotal <div>{{ number_format($invoice->total,2) }}</div></div>
                    <div class="d-flex justify-content-between mt-2">Net <div>{{ number_format($invoice->net_total,2) }}</div></div>
                    <div class="d-flex justify-content-between mt-2">Status <div>{{ $invoice->status }}</div></div>
            <div class="d-flex justify-content-between mt-2">Due <div>{{ optional($invoice->due_date)->toDateString() }}</div></div>
            @php $paid = $invoice->payments()->sum('amount'); $remaining = max(0, $invoice->net_total - $paid); @endphp
            <div class="d-flex justify-content-between mt-2">Paid <div>{{ number_format($paid,2) }}</div></div>
            <div class="d-flex justify-content-between mt-2">Remaining <div id="remainingBalance">{{ number_format($remaining,2) }}</div></div>
                    <hr>
                    <h6>Payments</h6>
                    <ul>
                        @foreach($invoice->payments as $p)
                            <li>{{ number_format($p->amount,2) }} - {{ $p->method }} - {{ optional($p->paid_at)->toDateTimeString() }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    @if(session('success'))
        var toastEl = document.createElement('div');
        toastEl.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
        toastEl.style.right = '1rem'; toastEl.style.bottom = '1rem';
        toastEl.setAttribute('role','alert'); toastEl.setAttribute('aria-live','assertive'); toastEl.setAttribute('aria-atomic','true');
        toastEl.innerHTML = `<div class="d-flex"><div class="toast-body">{{ session('success') }}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
        document.body.appendChild(toastEl);
        var toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    @endif
});
</script>
@endsection
