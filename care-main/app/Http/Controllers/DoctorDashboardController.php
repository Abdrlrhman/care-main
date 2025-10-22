<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;

class DoctorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $doctor = $user->doctor;
        if (! $doctor) {
            abort(403, 'No doctor profile found.');
        }

        $today = now()->toDateString();

        $appointments = Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->whereDate('starts_at', $today)
            ->orderBy('starts_at')
            ->get();

        $invoices = Invoice::where('doctor_id', $doctor->id)
            ->where('status', '!=', 'paid')
            ->orderBy('due_date')
            ->get();

        // new patients in last 7 days
        $newPatients = Patient::where('created_at', '>=', now()->subDays(7))->get();

        // unread notifications for current user
        // $notifications = $user->unreadNotifications()->limit(10)->get();
        $notifications = null;
        return view('doctor_dashboard', compact('doctor','appointments','invoices','newPatients','notifications'));
    }
}
