<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class ReportsController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $appointments = Appointment::whereBetween('starts_at', [$start, $end]);
        $total = $appointments->count();
        $completed = (clone $appointments)->where('status', 'completed')->count();
        $canceled = (clone $appointments)->where('status', 'canceled')->count();

        $paymentsQ = Payment::whereBetween('paid_at', [$start, $end]);
        $totalPayments = $paymentsQ->sum('amount');
        $byMethod = $paymentsQ->selectRaw('method, SUM(amount) as sum')->groupBy('method')->get()->pluck('sum', 'method')->toArray();

        return view('staff.reports.daily', compact('date','total','completed','canceled','totalPayments','byMethod'));
    }

    public function exportCsv(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $payments = Payment::whereBetween('paid_at', [$start, $end])->with('invoice')->get();

        $filename = 'staff-payments-' . $date->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['id','invoice_id','amount','method','reference','paid_at'];

        $callback = function() use ($payments, $columns) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, $columns);
            foreach ($payments as $p) {
                fputcsv($fh, [$p->id, $p->invoice_id, $p->amount, $p->method, $p->reference, $p->paid_at]);
            }
            fclose($fh);
        };

        return Response::stream($callback, 200, $headers);
    }
}
