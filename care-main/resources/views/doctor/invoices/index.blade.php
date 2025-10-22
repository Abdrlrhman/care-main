@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Invoices</h2>

    <form class="row g-2 mb-3" method="get">
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                <option value="sent" {{ request('status')=='sent'?'selected':'' }}>Sent</option>
                <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Paid</option>
                <option value="overdue" {{ request('status')=='overdue'?'selected':'' }}>Overdue</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="patient_id" class="form-select">
                <option value="">All patients</option>
                @foreach($patients as $p)
                    <option value="{{ $p->id }}" {{ request('patient_id')==$p->id? 'selected':'' }}>{{ $p->full_name ?? $p->first_name.' '.$p->last_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="from" class="form-control" value="{{ request('from') }}"></div>
        <div class="col-md-2"><input type="date" name="to" class="form-control" value="{{ request('to') }}"></div>
        <div class="col-md-3 text-end">
            <a href="{{ route('doctor.invoices.create') }}" class="btn btn-primary">New Invoice</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Total</th>
                    <th>Net</th>
                    <th>Status</th>
                    <th>Due</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                <tr>
                    <td>{{ $inv->id }}</td>
                    <td>{{ $inv->patient->full_name ?? $inv->patient->first_name.' '.$inv->patient->last_name }}</td>
                    <td>{{ number_format($inv->total,2) }}</td>
                    <td>{{ number_format($inv->net_total,2) }}</td>
                    <td>{{ ucfirst($inv->status) }}</td>
                    <td>{{ optional($inv->due_date)->toDateString() }}</td>
                    <td>
                        <a href="{{ route('doctor.invoices.show', $inv) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('doctor.invoices.edit', $inv) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#paymentModal" data-invoice-id="{{ $inv->id }}" data-invoice-net="{{ $inv->net_total }}" data-invoice-paid="{{ $inv->payments_sum_amount ?? 0 }}">Record Payment</button>
                        <a href="#" onclick="window.print()" class="btn btn-sm btn-outline-dark">Print</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $invoices->links() }}

    @include('doctor.invoices._payment_modal')
</div>

@endsection
