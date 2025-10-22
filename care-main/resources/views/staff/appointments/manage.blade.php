@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Appointments</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Create Appointment</button>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3"><input type="text" name="patient" class="form-control" placeholder="Patient" value="{{ request('patient') }}"></div>
        <div class="col-md-3"><input type="text" name="doctor" class="form-control" placeholder="Doctor" value="{{ request('doctor') }}"></div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>Confirmed</option>
                <option value="checked_in" {{ request('status')=='checked_in'?'selected':'' }}>Checked-in</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="date" class="form-control" value="{{ request('date') }}"></div>
        <div class="col-md-2"><button class="btn btn-secondary">Filter</button></div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Starts At</th>
                            <th>Ends At</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appt)
                            <tr>
                                <td>{{ optional($appt->patient)->name }}</td>
                                <td>{{ optional($appt->doctor)->name }}</td>
                                <td>{{ $appt->starts_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $appt->ends_at->format('Y-m-d H:i') }}</td>
                                <td>{{ ucfirst($appt->status) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($appt->notes, 60) }}</td>
                                <td>
                                    <a href="{{ route('staff.appointments.edit', $appt->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('staff.appointments.destroy', $appt->id) }}" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Cancel</button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7">No appointments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-block d-md-none p-3">
                @forelse($appointments as $appt)
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div><strong>{{ optional($appt->patient)->name }}</strong></div>
                                <div class="text-muted">{{ $appt->starts_at->format('Y-m-d H:i') }}</div>
                            </div>
                            <div>Doctor: {{ optional($appt->doctor)->name }}</div>
                            <div>Status: {{ ucfirst($appt->status) }}</div>
                            <div class="mt-2">
                                <a href="{{ route('staff.appointments.edit', $appt->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('staff.appointments.destroy', $appt->id) }}" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Cancel</button></form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted">No appointments found.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $appointments->withQueryString()->links() }}</div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('staff.appointments.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Patient</label>
                                <select name="patient_id" class="form-select" required>
                                    <option value="">Select patient</option>
                                    @foreach($patients as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Doctor</label>
                                <select name="doctor_id" class="form-select" required>
                                    <option value="">Select doctor</option>
                                    @foreach($doctors as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Starts At</label>
                                <input type="datetime-local" name="starts_at" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ends At</label>
                                <input type="datetime-local" name="ends_at" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="checked_in">Checked-in</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
