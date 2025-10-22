<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="paymentModalForm" method="post">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Record Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label>Invoice</label>
            <input type="text" id="paymentInvoiceId" class="form-control" readonly>
          </div>
          <div class="mb-2">
            <label>Remaining Balance</label>
            <input type="text" id="paymentRemaining" class="form-control" readonly>
          </div>
          <div class="mb-2">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" id="paymentAmount" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Method</label>
            <select name="method" class="form-select">
              <option value="cash">Cash</option>
              <option value="card">Card</option>
              <option value="bank">Bank</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-2">
            <label>Paid at</label>
            <input type="datetime-local" name="paid_at" class="form-control">
          </div>
          <div class="mb-2">
            <label>Reference</label>
            <input name="reference" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Payment</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var modalEl = document.getElementById('paymentModal');
    modalEl.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var invoiceId = button.getAttribute('data-invoice-id');
    var remaining = button.getAttribute('data-invoice-net') - (button.getAttribute('data-invoice-paid') || 0);
    var form = document.getElementById('paymentModalForm');
    form.action = '/doctor/invoices/' + invoiceId + '/payments';
    document.getElementById('paymentInvoiceId').value = invoiceId;
    document.getElementById('paymentRemaining').value = parseFloat(remaining).toFixed(2);
    document.getElementById('paymentAmount').value = parseFloat(remaining).toFixed(2);
    });

  document.getElementById('paymentModalForm').addEventListener('submit', function(e){
    var remaining = parseFloat(document.getElementById('paymentRemaining').value || 0);
    var amount = parseFloat(document.getElementById('paymentAmount').value || 0);
    if (amount <= 0 || amount > remaining) {
      e.preventDefault();
      alert('Amount must be greater than 0 and less than or equal to remaining balance ('+remaining.toFixed(2)+')');
      return false;
    }
  });
});
</script>
