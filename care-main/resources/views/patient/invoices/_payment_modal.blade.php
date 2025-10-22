
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden" id="paymentModal">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
    <form id="paymentForm" method="post" action="">
      @csrf
      <div class="px-6 py-4 border-b flex justify-between items-center">
        <h5 class="font-bold text-lg">Record Payment</h5>
        <button type="button" class="text-gray-400 hover:text-gray-700 text-2xl" onclick="document.getElementById('paymentModal').classList.add('hidden')">&times;</button>
      </div>
      <div class="px-6 py-4">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Invoice</label>
          <input type="text" id="paymentInvoiceId" class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-700" readonly>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Remaining</label>
          <input type="text" id="paymentRemaining" class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-700" readonly>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
          <input type="number" step="0.01" name="amount" id="paymentAmount" class="w-full border rounded px-3 py-2 text-gray-700" required>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Method</label>
          <select name="method" class="w-full border rounded px-3 py-2 text-gray-700" required>
            <option value="card">Card</option>
            <option value="cash">Cash</option>
            <option value="bank">Bank</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Reference</label>
          <input type="text" name="reference" class="w-full border rounded px-3 py-2 text-gray-700">
        </div>
      </div>
      <div class="px-6 py-4 flex justify-end gap-2 border-t">
        <button type="button" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300" onclick="document.getElementById('paymentModal').classList.add('hidden')">Close</button>
        <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Save Payment</button>
      </div>
    </form>
  </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('[data-bs-toggle="modal"]').forEach(function(btn){
    btn.addEventListener('click', function(){
      var invoiceId = btn.getAttribute('data-invoice-id');
      var net = parseFloat(btn.getAttribute('data-invoice-net') || 0);
      var paid = parseFloat(btn.getAttribute('data-invoice-paid') || 0);
      var remaining = (net - paid).toFixed(2);

      var form = document.getElementById('paymentForm');
      form.action = '/patient/invoices/' + invoiceId + '/payments';
      document.getElementById('paymentInvoiceId').value = invoiceId;
      document.getElementById('paymentRemaining').value = remaining;
      document.getElementById('paymentAmount').value = remaining;
      document.getElementById('paymentModal').classList.remove('hidden');
    });
  });
});
</script>
