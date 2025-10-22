<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Invoice;

class PaymentsSeeder extends Seeder
{
    public function run(): void
    {
        $invoice = Invoice::first();
        if (! $invoice) {
            return;
        }

        Payment::updateOrCreate(
            ['invoice_id' => $invoice->id, 'amount' => 50],
            ['method' => 'cash', 'paid_at' => now(), 'reference' => 'INIT-PAY']
        );
    }
}
