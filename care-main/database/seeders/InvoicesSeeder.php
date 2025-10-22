<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Service;

class InvoicesSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors = Doctor::all();
        $services = Service::all();

        foreach ($patients as $idx => $patient) {
            $doctor = $doctors->get($idx % max(1, $doctors->count()));

            $invoice = Invoice::updateOrCreate(
                ['patient_id' => $patient->id],
                [
                    'doctor_id' => $doctor?->id,
                    'total' => 0,
                    'net_total' => 0,
                    'status' => 'unpaid',
                    'due_date' => now()->addDays(7),
                ]
            );

            // add 1-2 items
            $selected = $services->take(2);
            $total = 0;
            foreach ($selected as $s) {
                $qty = 1;
                $line = $s->price * $qty;
                InvoiceItem::updateOrCreate(
                    ['invoice_id' => $invoice->id, 'service_id' => $s->id],
                    ['qty' => $qty, 'unit_price' => $s->price, 'line_total' => $line]
                );
                $total += $line;
            }

            $invoice->update(['total' => $total, 'net_total' => $total]);
        }
    }
}
