@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Edit Appointment #{{ $appointment->id }}</h2>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('staff.appointments.update', $appointment->id) }}">
        @csrf
        @method('PUT')
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Patient</label>
                <select name="patient_id" class="form-select" required>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" {{ $p->id == $appointment->patient_id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Doctor</label>
                <select name="doctor_id" class="form-select" required>
                    @foreach($doctors as $d)
                        <option value="{{ $d->id }}" {{ $d->id == $appointment->doctor_id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Starts At</label>
                <input type="datetime-local" name="starts_at" class="form-control" value="{{ $appointment->starts_at->format('Y-m-d\TH:i') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Ends At</label>
                <input type="datetime-local" name="ends_at" class="form-control" value="{{ $appointment->ends_at->format('Y-m-d\TH:i') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="pending" {{ $appointment->status=='pending'?'selected':'' }}>Pending</option>
                    <option value="confirmed" {{ $appointment->status=='confirmed'?'selected':'' }}>Confirmed</option>
                    <option value="checked_in" {{ $appointment->status=='checked_in'?'selected':'' }}>Checked-in</option>
                    <option value="cancelled" {{ $appointment->status=='cancelled'?'selected':'' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="4">{{ $appointment->notes }}</textarea>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('staff.appointments.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </form>
</div>
@endsection
