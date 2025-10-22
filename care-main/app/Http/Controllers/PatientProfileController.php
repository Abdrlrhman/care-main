<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Patient;

class PatientProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $patient = $user->patient;
        if (!$patient) abort(404);
        return view('patient.profile.edit', compact('patient'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $patient = $user->patient;
        if (!$patient) abort(404);

        $data = $request->validate([
            'phone' => ['nullable','regex:/^\+?[0-9\-\s]{7,20}$/'],
            'address' => ['nullable','string','max:500'],
            'notify_email' => 'nullable|boolean',
            'notify_sms' => 'nullable|boolean',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        // update patient fields
        $patient->phone = $data['phone'] ?? $patient->phone;
        $patient->address = $data['address'] ?? $patient->address;
        $patient->save();

        // notification prefs stored on user meta or patient; we'll store on patient
        $patient->notify_email = $request->boolean('notify_email');
        $patient->notify_sms = $request->boolean('notify_sms');
        $patient->save();

        // change password if requested
        if (!empty($data['new_password'])) {
            if (empty($data['current_password']) || !Hash::check($data['current_password'], $user->password)) {
                return back()->withInput()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->password = Hash::make($data['new_password']);
            $user->save();
        }

        return redirect()->route('patient.profile.edit')->with('success', 'Profile updated.');
    }
}
