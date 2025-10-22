@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Doctor Profile & Preferences</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="post" action="{{ route('doctor.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">Profile</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Name</label>
                            <input name="name" class="form-control" value="{{ old('name', $doctor->name ?? Auth::user()->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Specialty</label>
                            <input name="specialty" class="form-control" value="{{ old('specialty', $doctor->specialty) }}">
                        </div>
                        <div class="mb-3">
                            <label>Phone</label>
                            <input name="phone" class="form-control" value="{{ old('phone', $doctor->phone) }}">
                        </div>
                        <div class="mb-3">
                            <label>Bio</label>
                            <textarea name="bio" class="form-control">{{ old('bio', $doctor->bio) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label>Profile Image</label>
                            <input type="file" name="profile_image" class="form-control">
                            @if($doctor->profile_image)
                                <img src="{{ asset('storage/'.$doctor->profile_image) }}" alt="profile" class="img-thumbnail mt-2" style="max-width:120px">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Visit / Print Preferences</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Default diagnosis template (optional)</label>
                            <textarea name="default_diagnosis_template" class="form-control">{{ old('default_diagnosis_template', $doctor->default_diagnosis_template) }}</textarea>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="include_attachments_in_print" name="include_attachments_in_print" {{ $doctor->include_attachments_in_print ? 'checked' : '' }}>
                            <label class="form-check-label" for="include_attachments_in_print">Include attachments in printouts</label>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Notifications</div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="notify_email_new_appointment" name="notify_email_new_appointment" {{ $doctor->notify_email_new_appointment ? 'checked' : '' }}>
                            <label class="form-check-label" for="notify_email_new_appointment">Email for new appointments</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="notify_sms_new_appointment" name="notify_sms_new_appointment" {{ $doctor->notify_sms_new_appointment ? 'checked' : '' }}>
                            <label class="form-check-label" for="notify_sms_new_appointment">SMS for new appointments</label>
                        </div>
                        <hr>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="notify_email_cancel" name="notify_email_cancel" {{ $doctor->notify_email_cancel ? 'checked' : '' }}>
                            <label class="form-check-label" for="notify_email_cancel">Email on appointment cancel</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="notify_sms_cancel" name="notify_sms_cancel" {{ $doctor->notify_sms_cancel ? 'checked' : '' }}>
                            <label class="form-check-label" for="notify_sms_cancel">SMS on appointment cancel</label>
                        </div>
                        <hr>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="notify_email_overdue_invoice" name="notify_email_overdue_invoice" {{ $doctor->notify_email_overdue_invoice ? 'checked' : '' }}>
                            <label class="form-check-label" for="notify_email_overdue_invoice">Email for overdue invoices</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="notify_sms_overdue_invoice" name="notify_sms_overdue_invoice" {{ $doctor->notify_sms_overdue_invoice ? 'checked' : '' }}>
                            <label class="form-check-label" for="notify_sms_overdue_invoice">SMS for overdue invoices</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mb-5">
                    <button class="btn btn-primary">Save</button>
                    <a href="{{ route('password.change') ?? '#' }}" class="btn btn-link">Change Password</a>
                </div>
            </div>

            <div class="col-md-4">
                <!-- quick profile card -->
                <div class="card mb-3">
                    <div class="card-body text-center">
                        @if($doctor->profile_image)
                            <img src="{{ asset('storage/'.$doctor->profile_image) }}" class="rounded-circle mb-2" style="width:120px;height:120px;object-fit:cover">
                        @else
                            <div class="bg-secondary rounded-circle mb-2" style="width:120px;height:120px;display:inline-block"></div>
                        @endif
                        <h5>{{ $doctor->name }}</h5>
                        <div class="text-muted">{{ $doctor->specialty }}</div>
                        <div class="mt-2">{{ $doctor->phone }}</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
