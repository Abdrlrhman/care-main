@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $invoice->exists ? 'Edit Invoice' : 'New Invoice' }}</h2>

    <form method="post" action="{{ $invoice->exists ? route('doctor.invoices.update', $invoice) : route('doctor.invoices.store') }}">
        @csrf
        @if($invoice->exists)
            @method('PUT')
        @endif

        <div class="row mb-3">
            <div class="col-md-5">
                <label>Patient</label>
                <select name="patient_id" class="form-select">
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" {{ (old('patient_id', $invoice->patient_id)==$p->id)?'selected':'' }}>{{ $p->full_name ?? $p->first_name.' '.$p->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Doctor</label>
                <input class="form-control" readonly value="{{ Auth::user()->doctor->user->name ?? Auth::user()->name }}">
            </div>
            <div class="col-md-3">
                <label>Due date</label>
                <input type="date" name="due_date" class="form-control" value="{{ old('due_date', optional($invoice->due_date)->toDateString()) }}">
            </div>
        </div>

    <div class="card mb-3">
            <div class="card-header">Items</div>
            <div class="card-body">
        <table class="table d-none d-md-table" id="items-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Line Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(old('items'))
                            @foreach(old('items') as $i)
                                <tr>
                                    <td>
                                        <select name="items[][service_id]" class="form-select service-select">
                                            @foreach($services as $s)
                                                <option value="{{ $s->id }}" {{ $s->id == $i['service_id'] ? 'selected' : '' }} data-price="{{ $s->price }}">{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="items[][qty]" class="form-control qty" value="{{ $i['qty'] }}"></td>
                                    <td><input type="number" step="0.01" name="items[][unit_price]" class="form-control unit_price" value="{{ $i['unit_price'] }}"></td>
                                    <td class="line_total">{{ number_format($i['qty']*$i['unit_price'],2) }}</td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></td>
                                </tr>
                            @endforeach
                        @else
                            @foreach($items as $it)
                                <tr>
                                    <td>
                                        <select name="items[][service_id]" class="form-select service-select">
                                            @foreach($services as $s)
                                                <option value="{{ $s->id }}" {{ $s->id == $it->service_id ? 'selected' : '' }} data-price="{{ $s->price }}">{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="items[][qty]" class="form-control qty" value="{{ $it->qty }}"></td>
                                    <td><input type="number" step="0.01" name="items[][unit_price]" class="form-control unit_price" value="{{ $it->unit_price }}"></td>
                                    <td class="line_total">{{ number_format($it->line_total,2) }}</td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <!-- mobile stacked cards -->
                <div id="items-cards" class="d-block d-md-none">
                </div>
                <div class="mb-2">
                    <button type="button" id="add-row" class="btn btn-sm btn-outline-primary">Add Item</button>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4 offset-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between"><div>Subtotal</div><div id="subtotal">0.00</div></div>
                        <div class="d-flex justify-content-between mt-2"><div>Discount</div><div><input type="number" step="0.01" name="discount" id="discount" class="form-control" value="{{ old('discount', 0) }}"></div></div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold"><div>Net</div><div id="net_total">0.00</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label>Notes</label>
            <textarea name="notes" class="form-control">{{ old('notes', $invoice->notes ?? '') }}</textarea>
        </div>

        <div class="d-flex gap-2">
            <button name="action" value="save" class="btn btn-secondary">Save Draft</button>
            <button name="action" value="send" class="btn btn-primary">Send</button>
            <button type="button" id="mark-paid" class="btn btn-success">Mark Paid</button>
            <a href="{{ route('doctor.invoices.index') }}" class="btn btn-link">Cancel</a>
        </div>
    </form>

    @include('doctor.invoices._payment_modal')
</div>

@section('scripts')
    <script>
document.addEventListener('DOMContentLoaded', function(){
    function recalc(){
        let subtotal = 0;
        document.querySelectorAll('#items-table tbody tr').forEach(function(row){
            let qty = parseFloat(row.querySelector('.qty').value||0);
            let price = parseFloat(row.querySelector('.unit_price').value||0);
            let line = qty * price;
            row.querySelector('.line_total').innerText = line.toFixed(2);
            subtotal += line;
        });
        document.getElementById('subtotal').innerText = subtotal.toFixed(2);
        let discount = parseFloat(document.getElementById('discount').value||0);
        let net = Math.max(0, subtotal - discount);
        document.getElementById('net_total').innerText = net.toFixed(2);
    refreshCards();
    }

    function refreshCards(){
        let container = document.getElementById('items-cards');
        container.innerHTML = '';
        document.querySelectorAll('#items-table tbody tr').forEach(function(row){
            let service = row.querySelector('.service-select').selectedOptions[0].textContent;
            let qty = row.querySelector('.qty').value;
            let price = parseFloat(row.querySelector('.unit_price').value||0).toFixed(2);
            let line = row.querySelector('.line_total').innerText;
            let card = document.createElement('div');
            card.className = 'card mb-2';
            card.innerHTML = `<div class="card-body"><div class="d-flex justify-content-between"><div><strong>${service}</strong><div>Qty: ${qty}</div></div><div>${line}</div></div></div>`;
            container.appendChild(card);
        });
    }

    document.getElementById('add-row').addEventListener('click', function(){
        let services = @json($services->map(function($s){ return ['id'=>$s->id,'name'=>$s->name,'price'=>$s->price]; }));
        let tr = document.createElement('tr');
        tr.innerHTML = `
            <td><select name="items[][service_id]" class="form-select service-select">${services.map(s=>`<option value="${s.id}" data-price="${s.price}">${s.name}</option>`).join('')}</select></td>
            <td><input type="number" name="items[][qty]" class="form-control qty" value="1"></td>
            <td><input type="number" step="0.01" name="items[][unit_price]" class="form-control unit_price" value="0"></td>
            <td class="line_total">0.00</td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></td>
        `;
        document.querySelector('#items-table tbody').appendChild(tr);
        recalc();
    });

    document.body.addEventListener('change', function(e){
        if (e.target.matches('.service-select')){
            let opt = e.target.selectedOptions[0];
            let price = opt ? opt.dataset.price : 0;
            let row = e.target.closest('tr');
            row.querySelector('.unit_price').value = price;
            recalc();
        }
        if (e.target.matches('.qty') || e.target.matches('.unit_price') || e.target.id=='discount'){
            recalc();
        }
    });

    document.body.addEventListener('click', function(e){
        if (e.target.matches('.remove-row')){
            e.target.closest('tr').remove();
            recalc();
        }
        if (e.target.id=='mark-paid'){
            @if($invoice->exists)
                let btn = e.target;
                let modalBtn = document.createElement('button');
                modalBtn.setAttribute('data-bs-toggle','modal');
                modalBtn.setAttribute('data-bs-target','#paymentModal');
                modalBtn.setAttribute('data-invoice-id','{{ $invoice->id }}');
                modalBtn.setAttribute('data-invoice-net','{{ $invoice->net_total }}');
                modalBtn.setAttribute('data-invoice-paid','{{ $invoice->payments()->sum("amount") }}');
                // dispatch show
                modalBtn.click();
            @else
                alert('Save the invoice first before recording a payment.');
            @endif
        }
    });

    recalc();
});
</script>
@endsection

@endsection
