<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\Payment;

class PatientInvoicesController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $patient = $user->patient;
        if (!$patient) {
            abort(403);
        }

        $q = Invoice::with('doctor.user')->withSum('payments', 'amount')->where('patient_id', $patient->id);

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'unpaid') {
                $q->whereNotIn('status', ['paid']);
            } elseif ($status === 'paid') {
                $q->where('status', 'paid');
            } elseif ($status === 'overdue') {
                $q->whereDate('due_date', '<', now()->toDateString())->whereNotIn('status', ['paid']);
            }
        }

        $invoices = $q->orderByDesc('created_at')->paginate(12)->withQueryString();

        return view('patient.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $user = Auth::user();
        $patient = $user->patient;
        if (!$patient || $invoice->patient_id !== $patient->id) {
            abort(403);
        }

        $invoice->load('items.service', 'doctor.user', 'payments');
        $paid = $invoice->payments->sum('amount');
        $remaining = max(0, $invoice->net_total - $paid);

        return view('patient.invoices.show', compact('invoice', 'paid', 'remaining'));
    }

    public function recordPayment(Request $request, Invoice $invoice)
    {
        $user = Auth::user();
        $patient = $user->patient;
        if (!$patient || $invoice->patient_id !== $patient->id) {
            abort(403);
        }

        $paidSoFar = $invoice->payments()->sum('amount');
        $remaining = round($invoice->net_total - $paidSoFar, 2);

        $data = $request->validate([
            'amount' => ['required','numeric','gt:0'],
            'method' => 'required|string|in:card,cash,bank',
            'reference' => 'nullable|string',
            'paid_at' => 'nullable|date',
        ]);

        if ($data['amount'] > $remaining) {
            return back()->withInput()->withErrors(['amount' => "Amount must be less than or equal to remaining balance ({$remaining})."]);
        }

        DB::beginTransaction();
        try {
            $payment = $invoice->payments()->create([
                'amount' => $data['amount'],
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
                'paid_at' => $data['paid_at'] ?? now(),
            ]);

            $paid = $paidSoFar + $payment->amount;
            if (bccomp($paid, $invoice->net_total, 2) >= 0) {
                $invoice->update(['status' => 'paid']);
            } else {
                $invoice->update(['status' => 'partial']);
            }

            DB::commit();
            return redirect()->route('patient.invoices.show', $invoice)->with('success', 'Payment recorded.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
