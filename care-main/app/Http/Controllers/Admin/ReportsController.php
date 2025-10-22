<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('name')->get();
        $doctors = \App\Models\Doctor::orderBy('name')->get();
        return view('admin.reports.index', compact('services','doctors'));
    }

    public function revenueByMonth(Request $request)
    {
        $from = $request->filled('from') ? $request->from : now()->subMonths(12)->startOfMonth();
        $to = $request->filled('to') ? $request->to : now()->endOfMonth();

        $q = Invoice::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('SUM(net_total) as revenue'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('month')
            ->orderBy('month');

        if ($request->filled('doctor_id')) $q->where('doctor_id', $request->doctor_id);

        return $q->get();
    }

    public function appointmentsBySpecialization(Request $request)
    {
        $q = Appointment::select('doctors.specialty as specialization', DB::raw('COUNT(appointments.id) as count'))
            ->join('doctors','appointments.doctor_id','doctors.id')
            ->groupBy('doctors.specialty')
            ->orderByDesc('count');

        if ($request->filled('from')) $q->whereDate('appointments.starts_at', '>=', $request->from);
        if ($request->filled('to')) $q->whereDate('appointments.starts_at', '<=', $request->to);

        return $q->get();
    }

    public function servicesUsage(Request $request)
    {
        $q = InvoiceItem::select('service_id', DB::raw('SUM(qty) as total'))
            ->groupBy('service_id')
            ->orderByDesc('total');

        if ($request->filled('from') || $request->filled('to')) {
            $from = $request->filled('from') ? $request->from : '1970-01-01';
            $to = $request->filled('to') ? $request->to : now()->toDateString();
            $q->join('invoices','invoice_items.invoice_id','invoices.id')
                ->whereBetween('invoices.created_at', [$from, $to]);
        }

        $items = $q->get();
        $services = Service::whereIn('id', $items->pluck('service_id'))->get()->keyBy('id');
        return $items->map(function($r) use ($services){
            return ['service' => $services[$r->service_id]->name ?? 'Unknown', 'total' => $r->total];
        });
    }

    public function exportCsv(Request $request, $report)
    {
        if ($report === 'revenue') {
            $rows = $this->revenueByMonth($request);
            $filename = 'revenue.csv';
            $header = ['Month','Revenue'];
            $data = $rows->map(function($r){ return [$r->month, $r->revenue]; });
        } elseif ($report === 'services') {
            $rows = $this->servicesUsage($request);
            $filename = 'services.csv';
            $header = ['Service','Total'];
            $data = $rows->map(function($r){ return [$r['service'], $r['total']]; });
        } else {
            return abort(404);
        }

        $callback = function() use ($header, $data) {
            $out = fopen('php://output','w');
            fputcsv($out, $header);
            foreach($data as $row) fputcsv($out, $row);
            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
