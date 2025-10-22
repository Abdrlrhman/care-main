<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('staff.invoices.payments', $invoice->id) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Record Payment for Invoice #{{ $invoice->id }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">Outstanding balance: <strong>{{ number_format((($invoice->net_total ?? $invoice->total) - $invoice->payments->sum('amount')),2) }}</strong></div>
          <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Method</label>
            <select name="method" class="form-select" required>
              <option value="cash">Cash</option>
              <option value="card">Card</option>
              <option value="bank">Bank</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Reference</label>
            <input type="text" name="reference" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Paid at</label>
            <input type="datetime-local" name="paid_at" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button class="btn btn-primary">Save Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>
