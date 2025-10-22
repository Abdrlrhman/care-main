<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // KPIs
        $totalDoctors = Doctor::count();
        $totalPatients = Patient::count();
        $todaysAppointments = Appointment::whereDate('starts_at', now()->toDateString())->count();

        // monthly revenue (paid payments this month)
        $monthlyRevenue = Payment::whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');

        // Appointments per day (last 30 days)
        $start = now()->subDays(29)->startOfDay();
        $dates = [];
        $counts = [];
        for ($i = 0; $i < 30; $i++) {
            $d = $start->copy()->addDays($i);
            $dates[] = $d->toDateString();
            $counts[] = Appointment::whereDate('starts_at', $d->toDateString())->count();
        }

        // Revenue breakdown (paid vs unpaid)
        $paid = Invoice::where('status', 'paid')->sum('net_total');
        $unpaid = Invoice::whereNotIn('status', ['paid'])->sum('net_total');

        // latest 5 invoices
        $latestInvoices = Invoice::with('patient','doctor.user')->orderByDesc('created_at')->limit(5)->get();

        // latest 5 patients
        $latestPatients = Patient::orderByDesc('created_at')->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalDoctors','totalPatients','todaysAppointments','monthlyRevenue',
            'dates','counts','paid','unpaid','latestInvoices','latestPatients'
        ));
    }
}
