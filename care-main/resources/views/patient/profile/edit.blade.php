@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold text-blue-700 mb-6">Profile & Settings</h2>

    <form method="post" action="{{ route('patient.profile.update') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow p-6">
            <h5 class="text-lg font-semibold text-gray-700 mb-4">Personal Info</h5>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-700" value="{{ $patient->name }}" readonly>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <input class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-700" value="{{ $patient->gender }}" readonly>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
                <input class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-700" value="{{ optional($patient->birth_date)->toDateString() }}" readonly>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input name="phone" value="{{ old('phone', $patient->phone) }}" class="w-full border rounded px-3 py-2 text-gray-700 @error('phone') border-red-500 @enderror">
                @error('phone')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" class="w-full border rounded px-3 py-2 text-gray-700 @error('address') border-red-500 @enderror" rows="3">{{ old('address', $patient->address) }}</textarea>
                @error('address')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h5 class="text-lg font-semibold text-gray-700 mb-4">Account</h5>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Change Password</label>
                <input type="password" name="current_password" class="w-full border rounded px-3 py-2 text-gray-700 mb-2" placeholder="Current password">
                <input type="password" name="new_password" class="w-full border rounded px-3 py-2 text-gray-700 mb-2" placeholder="New password">
                <input type="password" name="new_password_confirmation" class="w-full border rounded px-3 py-2 text-gray-700" placeholder="Confirm new password">
            </div>

                        <h6 class="mt-3">Notifications</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="notify_email" id="notify_email" {{ $patient->notify_email ? 'checked' : '' }}>
                            <label class="form-check-label" for="notify_email">Email notifications</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="notify_sms" id="notify_sms" {{ $patient->notify_sms ? 'checked' : '' }}>
                            <label class="form-check-label" for="notify_sms">SMS notifications</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary">Save</button>
                    <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
