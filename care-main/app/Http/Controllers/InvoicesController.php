<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoicesController extends Controller
{
    public function index(Request $request)
    {
        $doctor = Auth::user()->doctor;

    $q = Invoice::with('patient')->withSum('payments', 'amount')->where('doctor_id', $doctor->id);

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        if ($request->filled('patient_id')) {
            $q->where('patient_id', $request->patient_id);
        }

        if ($request->filled('from')) {
            $q->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $q->whereDate('created_at', '<=', $request->to);
        }

        $invoices = $q->orderByDesc('created_at')->paginate(20)->withQueryString();

        $patients = Patient::orderBy('last_name')->get();

        return view('doctor.invoices.index', compact('invoices', 'patients'));
    }

    public function create()
    {
        $doctor = Auth::user()->doctor;
        $patients = Patient::orderBy('last_name')->get();
        $services = Service::where('active', 1)->orderBy('name')->get();

        return view('doctor.invoices.edit', [
            'invoice' => new Invoice(['doctor_id' => $doctor->id, 'status' => 'draft']),
            'patients' => $patients,
            'services' => $services,
            'items' => collect(),
        ]);
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $patients = Patient::orderBy('last_name')->get();
        $services = Service::where('active', 1)->orderBy('name')->get();

        $items = $invoice->items()->with('service')->get();

        return view('doctor.invoices.edit', compact('invoice', 'patients', 'services', 'items'));
    }

    public function store(Request $request)
    {
        $doctor = Auth::user()->doctor;

        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // action button determines status when provided
        $action = $request->input('action');
        if ($action === 'send') $data['status'] = 'sent';
        else $data['status'] = $data['status'] ?? 'draft';

        if ($data['status'] === 'sent' && empty($data['due_date'])) {
            return back()->withInput()->withErrors(['due_date' => 'Due date is required when sending an invoice.']);
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'patient_id' => $data['patient_id'],
                'doctor_id' => $doctor->id,
                'status' => $data['status'],
                'due_date' => $data['due_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'discount' => $request->input('discount', 0),
            ] + ['total' => 0, 'net_total' => 0]);

            $subtotal = 0;
            foreach ($data['items'] as $it) {
                $line = $it['qty'] * $it['unit_price'];
                $subtotal += $line;
                $invoice->items()->create([
                    'service_id' => $it['service_id'],
                    'qty' => $it['qty'],
                    'unit_price' => $it['unit_price'],
                    'line_total' => $line,
                ]);
            }

            $discount = $request->input('discount', 0);
            $net = max(0, $subtotal - floatval($discount));

            $invoice->update(['total' => $subtotal, 'net_total' => $net, 'discount' => $discount]);

            DB::commit();
            return redirect()->route('doctor.invoices.index')->with('success', 'Invoice saved.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $action = $request->input('action');
        if ($action === 'send') $data['status'] = 'sent';
        else $data['status'] = $data['status'] ?? 'draft';

        if ($data['status'] === 'sent' && empty($data['due_date'])) {
            return back()->withInput()->withErrors(['due_date' => 'Due date is required when sending an invoice.']);
        }

        DB::beginTransaction();
        try {
            $invoice->update([
                'patient_id' => $data['patient_id'],
                'due_date' => $data['due_date'] ?? null,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'discount' => $request->input('discount', 0),
            ]);

            // replace items
            $invoice->items()->delete();

            $subtotal = 0;
            foreach ($data['items'] as $it) {
                $line = $it['qty'] * $it['unit_price'];
                $subtotal += $line;
                $invoice->items()->create([
                    'service_id' => $it['service_id'],
                    'qty' => $it['qty'],
                    'unit_price' => $it['unit_price'],
                    'line_total' => $line,
                ]);
            }

            $discount = $request->input('discount', 0);
            $net = max(0, $subtotal - floatval($discount));

            $invoice->update(['total' => $subtotal, 'net_total' => $net, 'discount' => $discount]);

            DB::commit();
            return redirect()->route('doctor.invoices.index')->with('success', 'Invoice updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function recordPayment(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        // compute remaining
        $paidSoFar = $invoice->payments()->sum('amount');
        $remaining = round($invoice->net_total - $paidSoFar, 2);

        $data = $request->validate([
            'amount' => ['required','numeric','gt:0'],
            'method' => 'nullable|string',
            'paid_at' => 'nullable|date',
            'reference' => 'nullable|string',
        ]);

        if ($data['amount'] > $remaining) {
            return back()->withInput()->withErrors(['amount' => "Amount must be less than or equal to remaining balance ({$remaining})."]);
        }

        $payment = $invoice->payments()->create([
            'amount' => $data['amount'],
            'method' => $data['method'] ?? null,
            'paid_at' => $data['paid_at'] ?? now(),
            'reference' => $data['reference'] ?? null,
        ]);

        $paid = $paidSoFar + $payment->amount;
        if (bccomp($paid, $invoice->net_total, 2) >= 0) {
            $invoice->update(['status' => 'paid']);
        } else {
            $invoice->update(['status' => 'partial']);
        }

        return redirect()->route('doctor.invoices.show', $invoice)->with('success', 'Payment recorded successfully.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load('items.service', 'patient', 'payments');
        return view('doctor.invoices.show', compact('invoice'));
    }
}
