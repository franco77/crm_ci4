<form id="form" accept-charset="utf-8">
   <div class="mb-3">
      <label for="invoice_id" class="form-label">Invoice Id</label>
      <input type="text" name="invoice_id" value="<?= !empty($data_payments['invoice_id']) ? $data_payments['invoice_id'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="amount_paid" class="form-label">Amount Paid</label>
      <input type="text" name="amount_paid" value="<?= !empty($data_payments['amount_paid']) ? $data_payments['amount_paid'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="payment_date" class="form-label">Payment Date</label>
      <input type="text" name="payment_date" value="<?= !empty($data_payments['payment_date']) ? $data_payments['payment_date'] : '' ?>" class="form-control" id="payment_date" />
   </div>
   <div class="mb-3">
      <label for="payment_reference" class="form-label">Payment Reference</label>
      <input type="text" name="payment_reference" value="<?= !empty($data_payments['payment_reference']) ? $data_payments['payment_reference'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="paid_by" class="form-label">Paid By</label>
      <input type="text" name="paid_by" value="<?= !empty($data_payments['paid_by']) ? $data_payments['paid_by'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="amount_usd" class="form-label">Amount Usd</label>
      <input type="text" name="amount_usd" value="<?= !empty($data_payments['amount_usd']) ? $data_payments['amount_usd'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="created_at" class="form-label">Created At</label>
      <input type="text" name="created_at" value="<?= !empty($data_payments['created_at']) ? $data_payments['created_at'] : '' ?>" class="form-control" />
   </div>
   <div class="form-group">
      <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <label for="error"></label>
   </div>
</form>