<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $q = Invoice::with(['patient','doctor']);

        if ($request->filled('status')) $q->where('status', $request->get('status'));
        if ($request->filled('patient')) $q->where('patient_id', $request->get('patient'));
        if ($request->filled('doctor')) $q->where('doctor_id', $request->get('doctor'));
        if ($request->filled('from')) $q->whereDate('created_at', '>=', Carbon::parse($request->get('from')));
        if ($request->filled('to')) $q->whereDate('created_at', '<=', Carbon::parse($request->get('to')));

        $invoices = $q->orderBy('created_at','desc')->paginate(25);
        $patients = Patient::select('id','name')->get();
        $doctors = Doctor::select('id','name')->get();

        return view('staff.invoices.index', compact('invoices','patients','doctors'));
    }

    public function create()
    {
        $patients = Patient::select('id','name')->get();
        $doctors = Doctor::select('id','name')->get();
        $services = Service::where('active', true)->get();
        return view('staff.invoices.edit', compact('patients','doctors','services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($data, $request) {
            $subtotal = 0;
            $invoice = Invoice::create([
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'total' => 0,
                'net_total' => 0,
            ]);

            foreach ($request->input('items') as $it) {
                $line = ($it['qty'] * $it['unit_price']);
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $it['service_id'],
                    'qty' => $it['qty'],
                    'unit_price' => $it['unit_price'],
                    'line_total' => $line,
                ]);
                $subtotal += $line;
            }

            $invoice->total = $subtotal;
            $invoice->net_total = $subtotal; // taxes/discounts could be applied later
            $invoice->save();
        });

        return redirect()->route('staff.invoices.index')->with('status','Invoice created.');
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items.service','patient','doctor');
        $patients = Patient::select('id','name')->get();
        $doctors = Doctor::select('id','name')->get();
        $services = Service::where('active', true)->get();
        return view('staff.invoices.edit', compact('invoice','patients','doctors','services'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($request, $invoice, $data) {
            $invoice->update([
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'status' => $data['status'] ?? $invoice->status,
            ]);

            // replace items
            $invoice->items()->delete();
            $subtotal = 0;
            foreach ($request->input('items') as $it) {
                $line = ($it['qty'] * $it['unit_price']);
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $it['service_id'],
                    'qty' => $it['qty'],
                    'unit_price' => $it['unit_price'],
                    'line_total' => $line,
                ]);
                $subtotal += $line;
            }
            $invoice->total = $subtotal;
            $invoice->net_total = $subtotal;
            $invoice->save();
        });

        return redirect()->route('staff.invoices.index')->with('status','Invoice updated.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items.service','patient','doctor','payments');
        return view('staff.invoices.show', compact('invoice'));
    }

    public function markPaid(Invoice $invoice, Request $request)
    {
        $data = $request->validate(['amount' => 'nullable|numeric|min:0','method' => 'nullable|string']);
        $amount = $data['amount'] ?? $invoice->net_total;
        Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'method' => $data['method'] ?? 'cash',
            'paid_at' => Carbon::now(),
        ]);
        // update invoice status
        $invoice->status = 'paid';
        $invoice->save();
        return redirect()->back()->with('status','Marked as paid.');
    }
}
