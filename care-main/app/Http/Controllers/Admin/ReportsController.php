<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    // صفحة الواجهة — ترسل القوائم اللازمة (الخدمات والأطباء)
    public function index(Request $request)
    {
        $services = Service::orderBy('name')->get();
        $doctors = Doctor::orderBy('name')->get();

        // إذا أردت تمرير نتائج مبدئية مع الفيو، ممكن إلغاء التعليقات:
        $revenue = $this->revenueByMonth($request);
        $specialty = $this->appointmentsBySpecialization($request);
        $servicesUsage = $this->servicesUsage($request);

        return view('admin.reports.index', compact('services', 'doctors'));
    }

    // إرجاع الإيرادات حسب الشهر (JSON)
    // داخل app/Http/Controllers/Admin/ReportsController.php

    public function revenueByMonth(Request $request)
    {
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfMonth() : Carbon::now()->subMonths(11)->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfMonth() : Carbon::now()->endOfMonth();

        // جلب المبالغ المجموعة حسب شهر
        $rows = Invoice::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('SUM(net_total) as revenue'))
            ->whereBetween('created_at', [$from->toDateString(), $to->toDateString()])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // توليد قائمة الشهور بين from..to
        $period = CarbonPeriod::create($from->copy(), '1 month', $to->copy());
        $result = [];
        foreach ($period as $dt) {
            $key = $dt->format('Y-m');
            $result[] = [
                'month' => $key,
                'revenue' => isset($rows[$key]) ? (float) $rows[$key]->revenue : 0.0,
            ];
        }

        return response()->json($result);
    }

    // إرجاع عدد المواعيد مجمّع حسب تخصص الطبيب (JSON)
    public function appointmentsBySpecialization(Request $request)
    {
        $q = Appointment::select('doctors.specialty as specialization', DB::raw('COUNT(appointments.id) as count'))
            ->join('doctors', 'appointments.doctor_id', 'doctors.id')
            ->groupBy('doctors.specialty')
            ->orderByDesc('count');

        if ($request->filled('from')) {
            $q->whereDate('appointments.starts_at', '>=', Carbon::parse($request->from)->toDateString());
        }
        if ($request->filled('to')) {
            $q->whereDate('appointments.starts_at', '<=', Carbon::parse($request->to)->toDateString());
        }
        if ($request->filled('doctor_id')) {
            $q->where('appointments.doctor_id', $request->doctor_id);
        }

        $rows = $q->get()->map(fn ($r) => ['specialization' => $r->specialization ?? 'غير محدد', 'count' => (int) $r->count]);

        return response()->json($rows);
    }

    // إرجاع استخدام الخدمات (JSON)
    public function servicesUsage(Request $request)
    {
        $from = $request->filled('from') ? Carbon::parse($request->from)->toDateString() : null;
        $to = $request->filled('to') ? Carbon::parse($request->to)->toDateString() : null;

        $q = InvoiceItem::select('invoice_items.service_id', DB::raw('SUM(invoice_items.qty) as total'))
            ->groupBy('invoice_items.service_id');

        // join invoices لو هنفلتر حسب تاريخ أو دكتور
        if ($from || $to || $request->filled('doctor_id')) {
            $q->join('invoices', 'invoice_items.invoice_id', 'invoices.id');

            if ($from && $to) {
                $q->whereBetween('invoices.created_at', [$from, $to]);
            } elseif ($from) {
                $q->whereDate('invoices.created_at', '>=', $from);
            } elseif ($to) {
                $q->whereDate('invoices.created_at', '<=', $to);
            }

            if ($request->filled('doctor_id')) {
                $q->where('invoices.doctor_id', $request->doctor_id);
            }
        }

        if ($request->filled('service_id')) {
            $q->where('invoice_items.service_id', $request->service_id);
        }

        $items = $q->orderByDesc('total')->get();
        $serviceIds = $items->pluck('service_id')->unique()->filter()->values();

        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        $rows = $items->map(function ($r) use ($services) {
            return [
                'service' => $services[$r->service_id]->name ?? 'مجهول',
                'total' => (int) $r->total,
            ];
        });

        return response()->json($rows);
    }

    // تصدير CSV لإيرادات أو خدمات — يستخدم نفس الفلاتر اللي ممكن تمررها بالـquery
    public function exportCsv(Request $request, $report)
    {
        if ($report === 'revenue') {
            $rows = $this->revenueByMonth($request)->getData(); // JSON response -> array
            $filename = 'revenue.csv';
            $header = ['Month', 'Revenue'];
            $data = collect($rows)->map(fn ($r) => [$r->month, $r->revenue]);
        } elseif ($report === 'services') {
            $rows = $this->servicesUsage($request)->getData();
            $filename = 'services.csv';
            $header = ['Service', 'Total'];
            $data = collect($rows)->map(fn ($r) => [$r->service, $r->total]);
        } else {
            abort(404);
        }

        $callback = function () use ($header, $data) {
            $out = fopen('php://output', 'w');
            // إضافة BOM لو عايز دعم Excel للـ UTF-8:
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, $header);
            foreach ($data as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
