<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invoice;

class InvoicePolicy
{
    public function view(User $user, Invoice $invoice)
    {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('doctor') && $user->doctor && $invoice->doctor_id == $user->doctor->id) return true;
        return false;
    }

    public function update(User $user, Invoice $invoice)
    {
        return $this->view($user, $invoice);
    }
}
