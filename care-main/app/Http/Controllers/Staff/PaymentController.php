<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,bank',
            'reference' => 'nullable|string|max:255',
            'paid_at' => 'nullable|date',
        ]);

        // compute outstanding
        $paid = $invoice->payments()->sum('amount');
        $outstanding = ($invoice->net_total ?? $invoice->total) - $paid;

        if ($data['amount'] > $outstanding + 0.0001) {
            return redirect()->back()->withErrors(['amount' => 'Amount cannot exceed outstanding balance of ' . number_format($outstanding,2)]);
        }

        $paidAt = isset($data['paid_at']) ? Carbon::parse($data['paid_at']) : Carbon::now();

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $data['amount'],
            'method' => $data['method'],
            'reference' => $data['reference'] ?? null,
            'paid_at' => $paidAt,
        ]);

        // update invoice status based on remaining balance
        $newPaidTotal = $paid + $data['amount'];
        $remaining = ($invoice->net_total ?? $invoice->total) - $newPaidTotal;
        if ($remaining <= 0.0001) {
            $invoice->status = 'paid';
        } else {
            $invoice->status = 'partial';
        }
        $invoice->save();

        return redirect()->back()->with('status','Payment recorded.');
    }
}
