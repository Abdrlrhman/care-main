@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>{{ isset($invoice) ? 'Edit Invoice' : 'Create Invoice' }}</h2>
        <a href="{{ route('staff.invoices.index') }}" class="btn btn-light">Back</a>
    </div>

    <form method="POST" action="{{ isset($invoice) ? route('staff.invoices.update', $invoice->id) : route('staff.invoices.store') }}">
        @csrf
        @if(isset($invoice)) @method('PUT') @endif

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Patient</label>
                <select name="patient_id" class="form-select">
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" {{ isset($invoice) && $invoice->patient_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Doctor</label>
                <select name="doctor_id" class="form-select">
                    <option value="">—</option>
                    @foreach($doctors as $d)
                        <option value="{{ $d->id }}" {{ isset($invoice) && $invoice->doctor_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" class="form-control" value="{{ old('due_date', isset($invoice) && $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}">
            </div>

            <div class="col-12">
                <label class="form-label">Items</label>
                <div class="table-responsive">
                    <table class="table table-sm" id="items-table">
                        <thead>
                            <tr><th style="width:40%">Service</th><th style="width:15%">Qty</th><th style="width:20%">Unit Price</th><th style="width:20%">Line Total</th><th style="width:5%"></th></tr>
                        </thead>
                        <tbody>
                            @if(old('items'))
                                @foreach(old('items') as $i)
                                    <tr>
                                        <td>
                                            <select name="items[][service_id]" class="form-select service-select">
                                                <option value="">—</option>
                                                @foreach($services as $s)
                                                    <option value="{{ $s->id }}" {{ $s->id == ($i['service_id'] ?? '') ? 'selected' : '' }} data-price="{{ $s->price }}">{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="items[][qty]" class="form-control qty" value="{{ $i['qty'] ?? 1 }}" min="1"></td>
                                        <td><input type="number" step="0.01" name="items[][unit_price]" class="form-control unit-price" value="{{ $i['unit_price'] ?? 0 }}"></td>
                                        <td><input type="text" readonly class="form-control line-total" value="0.00"></td>
                                        <td><button type="button" class="btn btn-sm btn-danger remove-item">×</button></td>
                                    </tr>
                                @endforeach
                            @elseif(isset($invoice))
                                @foreach($invoice->items as $it)
                                    <tr>
                                        <td>
                                            <select name="items[][service_id]" class="form-select service-select">
                                                <option value="">—</option>
                                                @foreach($services as $s)
                                                    <option value="{{ $s->id }}" {{ $s->id == $it->service_id ? 'selected' : '' }} data-price="{{ $s->price }}">{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="items[][qty]" class="form-control qty" value="{{ $it->qty }}" min="1"></td>
                                        <td><input type="number" step="0.01" name="items[][unit_price]" class="form-control unit-price" value="{{ $it->unit_price }}"></td>
                                        <td><input type="text" readonly class="form-control line-total" value="{{ number_format($it->line_total,2) }}"></td>
                                        <td><button type="button" class="btn btn-sm btn-danger remove-item">×</button></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>
                                        <select name="items[][service_id]" class="form-select service-select">
                                            <option value="">—</option>
                                            @foreach($services as $s)
                                                <option value="{{ $s->id }}" data-price="{{ $s->price }}">{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="items[][qty]" class="form-control qty" value="1" min="1"></td>
                                    <td><input type="number" step="0.01" name="items[][unit_price]" class="form-control unit-price" value="0.00"></td>
                                    <td><input type="text" readonly class="form-control line-total" value="0.00"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-item">×</button></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div>
                        <button type="button" id="add-item" class="btn btn-outline-secondary btn-sm">Add Item</button>
                    </div>
                    <div class="text-end">
                        <div>Subtotal: <strong id="subtotal">0.00</strong></div>
                        <div class="mt-1">Discount: <input type="number" step="0.01" name="discount" class="form-control d-inline-block" style="width:120px" value="{{ old('discount', isset($invoice) ? $invoice->discount : 0) }}"></div>
                        <div class="mt-1">Total: <strong id="net_total">0.00</strong></div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control">{{ old('notes', $invoice->notes ?? '') }}</textarea>
            </div>

            <div class="col-12 text-end">
                <button class="btn btn-primary">Save Invoice</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    (function(){
        function recalcRow($tr){
            var qty = parseFloat($tr.querySelector('.qty').value) || 0;
            var up = parseFloat($tr.querySelector('.unit-price').value) || 0;
            var total = qty * up;
            $tr.querySelector('.line-total').value = total.toFixed(2);
        }

        function recalcAll(){
            var rows = document.querySelectorAll('#items-table tbody tr');
            var subtotal = 0;
            rows.forEach(function(r){
                recalcRow(r);
                subtotal += parseFloat(r.querySelector('.line-total').value) || 0;
            });
            document.getElementById('subtotal').innerText = subtotal.toFixed(2);
            var discount = parseFloat(document.querySelector('input[name="discount"]').value) || 0;
            document.getElementById('net_total').innerText = (subtotal - discount).toFixed(2);
        }

        document.addEventListener('change', function(e){
            if(e.target.matches('.qty') || e.target.matches('.unit-price')) recalcAll();
            if(e.target.matches('.service-select')){
                var price = parseFloat(e.target.selectedOptions[0].dataset.price || 0);
                var $row = e.target.closest('tr');
                $row.querySelector('.unit-price').value = price.toFixed(2);
                recalcAll();
            }
        });

        document.getElementById('add-item').addEventListener('click', function(){
            var tbody = document.querySelector('#items-table tbody');
            var tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <select name="items[][service_id]" class="form-select service-select">
                        <option value="">—</option>
                        @foreach($services as $s)
                            <option value="{{ $s->id }}" data-price="{{ $s->price }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="items[][qty]" class="form-control qty" value="1" min="1"></td>
                <td><input type="number" step="0.01" name="items[][unit_price]" class="form-control unit-price" value="0.00"></td>
                <td><input type="text" readonly class="form-control line-total" value="0.00"></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-item">×</button></td>
            `;
            tbody.appendChild(tr);
            recalcAll();
        });

        document.querySelector('#items-table').addEventListener('click', function(e){
            if(e.target.matches('.remove-item')){
                var tr = e.target.closest('tr');
                tr.parentNode.removeChild(tr);
                recalcAll();
            }
        });

        // initial calc
        recalcAll();
    })();
</script>
@endpush

@endsection
