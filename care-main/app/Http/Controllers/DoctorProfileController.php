<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Doctor;

class DoctorProfileController extends Controller
{
    public function edit()
    {
        $doctor = Auth::user()->doctor;
        return view('doctor.profile.edit', compact('doctor'));
    }

    public function update(Request $request)
    {
        $doctor = Auth::user()->doctor;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
            'default_diagnosis_template' => 'nullable|string',
            'include_attachments_in_print' => 'boolean',
            'notify_email_new_appointment' => 'boolean',
            'notify_sms_new_appointment' => 'boolean',
            'notify_email_cancel' => 'boolean',
            'notify_sms_cancel' => 'boolean',
            'notify_email_overdue_invoice' => 'boolean',
            'notify_sms_overdue_invoice' => 'boolean',
        ]);

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $path = $file->storePubliclyAs('doctors', $doctor->id . '_' . time() . '.' . $file->getClientOriginalExtension(), 'public');
            $data['profile_image'] = $path;
        }

        // normalize booleans
        $bools = [
            'include_attachments_in_print',
            'notify_email_new_appointment',
            'notify_sms_new_appointment',
            'notify_email_cancel',
            'notify_sms_cancel',
            'notify_email_overdue_invoice',
            'notify_sms_overdue_invoice',
        ];
        foreach ($bools as $b) {
            $data[$b] = $request->has($b) ? (bool)$request->input($b) : false;
        }

        $doctor->update(array_merge($data, [
            'name' => $data['name'],
            'specialty' => $data['specialty'] ?? $doctor->specialty,
            'phone' => $data['phone'] ?? $doctor->phone,
            'bio' => $data['bio'] ?? $doctor->bio,
        ]));

        return redirect()->route('doctor.profile.edit')->with('success', 'Profile updated.');
    }
}
