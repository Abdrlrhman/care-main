<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class InvoicesController extends Controller
{
    public function index(Request $request)
    {
        $q = Invoice::with(['patient','doctor']);

        if ($request->filled('status')) $q->where('status', $request->status);
        if ($request->filled('doctor_id')) $q->where('doctor_id', $request->doctor_id);
        if ($request->filled('patient_id')) $q->where('patient_id', $request->patient_id);
        if ($request->filled('from')) $q->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to')) $q->whereDate('created_at', '<=', $request->to);

        $invoices = $q->orderByDesc('created_at')->paginate(25)->withQueryString();

        return view('admin.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['items','payments','patient','doctor']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function addPayment(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string',
            'reference' => 'nullable|string',
        ]);

        DB::transaction(function() use ($invoice, $data){
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $data['amount'],
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
                'paid_at' => now(),
            ]);

            $paid = $invoice->payments()->sum('amount') + $data['amount'];
            if ($paid >= $invoice->net_total) {
                $invoice->status = 'paid';
            } else {
                $invoice->status = 'partial';
            }
            $invoice->save();
        });

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success','Payment recorded');
    }

    public function cancel(Invoice $invoice)
    {
        $invoice->status = 'cancelled';
        $invoice->save();
        return redirect()->route('admin.invoices.show', $invoice->id)->with('success','Invoice cancelled');
    }
}
