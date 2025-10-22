@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Invoices</h2>
        <a href="{{ route('staff.invoices.create') }}" class="btn btn-primary">Create Invoice</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-2"><select name="status" class="form-select"><option value="">All</option><option value="pending">Pending</option><option value="paid">Paid</option></select></div>
        <div class="col-md-3"><select name="patient" class="form-select"><option value="">Patient</option>@foreach($patients as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><select name="doctor" class="form-select"><option value="">Doctor</option>@foreach($doctors as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><input type="date" name="from" class="form-control" placeholder="From"></div>
        <div class="col-md-2"><input type="date" name="to" class="form-control" placeholder="To"></div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table mb-0">
                    <thead>
                        <tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Total</th><th>Status</th><th>Due</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $inv)
                            <tr>
                                <td>{{ $inv->id }}</td>
                                <td>{{ optional($inv->patient)->name }}</td>
                                <td>{{ optional($inv->doctor)->name }}</td>
                                <td>{{ number_format($inv->net_total,2) }}</td>
                                <td>{{ ucfirst($inv->status) }}</td>
                                <td>{{ optional($inv->due_date)->format('Y-m-d') ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('staff.invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('staff.invoices.edit', $inv->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7">No invoices found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-block d-md-none p-3">
                @forelse($invoices as $inv)
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex justify-content-between"><div><strong>#{{ $inv->id }}</strong> {{ optional($inv->patient)->name }}</div><div>{{ number_format($inv->net_total,2) }}</div></div>
                            <div class="text-muted">{{ ucfirst($inv->status) }} • Due: {{ optional($inv->due_date)->format('Y-m-d') ?? '—' }}</div>
                            <div class="mt-2"><a href="{{ route('staff.invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-primary">View</a></div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted">No invoices found.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $invoices->withQueryString()->links() }}</div>
</div>
@endsection
