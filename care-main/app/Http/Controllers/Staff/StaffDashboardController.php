<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class StaffDashboardController extends Controller
{
    public function index(Request $request)
    {
        $todayStart = Carbon::now()->startOfDay();
        $todayEnd = Carbon::now()->endOfDay();

        $appointmentsToday = Appointment::with(['patient','doctor'])
            ->whereBetween('starts_at', [$todayStart, $todayEnd])
            ->orderBy('starts_at')
            ->get();

        $kpiAppointments = $appointmentsToday->count();

        $newPatientsToday = \App\Models\Patient::whereBetween('created_at', [$todayStart, $todayEnd])->count();

        $pendingInvoices = Invoice::with(['patient','doctor'])
            
            ->whereIn('status', ['pending','unpaid', 'partial'])
            ->orderBy('due_date')
            ->get();

        // fallback if no paid_at column: compute unpaid by checking payments relation
        if (!\Schema::hasColumn('invoices', 'paid_at')) {
            $pendingInvoices = Invoice::with(['patient','doctor','payments'])
                ->get()
                ->filter(function($inv){
                    return ($inv->payments->sum('amount') < $inv->net_total);
                });
        }

        $kpiPendingInvoices = $pendingInvoices->count();

        $collectedToday = Payment::whereBetween('paid_at', [$todayStart, $todayEnd])->sum('amount');

        $kpiCollected = $collectedToday;

        return view('staff.dashboard.index', compact(
            'appointmentsToday', 'kpiAppointments', 'newPatientsToday', 'pendingInvoices', 'kpiPendingInvoices', 'kpiCollected'
        ));
    }

    public function checkIn(Appointment $appointment)
    {
        $appointment->status = 'checked_in';
        $appointment->save();
        return redirect()->back()->with('status', 'Patient checked in.');
    }

    public function cancel(Appointment $appointment)
    {
        $appointment->status = 'cancelled';
        $appointment->save();
        return redirect()->back()->with('status', 'Appointment cancelled.');
    }
}
