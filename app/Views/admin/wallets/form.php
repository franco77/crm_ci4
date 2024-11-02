<form id="form" accept-charset="utf-8">
   <div class="mb-3">
      <label for="user_id" class="form-label">User Id</label>
      <input type="text" name="user_id" value="<?= !empty($data_wallets['user_id']) ? $data_wallets['user_id'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="amount" class="form-label">Amount</label>
      <input type="text" name="amount" value="<?= !empty($data_wallets['amount']) ? $data_wallets['amount'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="remaining_amount" class="form-label">Remaining Amount</label>
      <input type="text" name="remaining_amount" value="<?= !empty($data_wallets['remaining_amount']) ? $data_wallets['remaining_amount'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="deposit_date" class="form-label">Deposit Date</label>
      <input type="text" name="deposit_date" value="<?= !empty($data_wallets['deposit_date']) ? $data_wallets['deposit_date'] : '' ?>" class="form-control" id="deposit_date" />
   </div>
   <div class="mb-3">
      <label for="payment_method" class="form-label">Payment Method</label>
      <input type="text" name="payment_method" value="<?= !empty($data_wallets['payment_method']) ? $data_wallets['payment_method'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="reference" class="form-label">Reference</label>
      <input type="text" name="reference" value="<?= !empty($data_wallets['reference']) ? $data_wallets['reference'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="support" class="form-label">Support</label>
      <input type="text" name="support" value="<?= !empty($data_wallets['support']) ? $data_wallets['support'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="notes" class="form-label">Notes</label>
      <textarea name="notes" class="form-control" ><?= !empty($data_wallets['notes']) ? $data_wallets['notes'] : '' ?></textarea>
   </div>
   <div class="mb-3">
      <label for="status" class="form-label">Status</label>
      <div class="form-check">
         <input class="form-check-input" type="radio" id="status1" name="status" value="active" <?= !empty($data_wallets['status']) && $data_wallets['status'] == 'active' ? 'checked' : '' ?> />
         <label class="form-check-label" for="status1">Active</label>
      </div>
      <div class="form-check">
         <input class="form-check-input" type="radio" id="status2" name="status" value="inactive" <?= !empty($data_wallets['status']) && $data_wallets['status'] == 'inactive' ? 'checked' : '' ?> />
         <label class="form-check-label" for="status2">Inactive</label>
      </div>
      <div class="form-check">
         <input class="form-check-input" type="radio" id="status3" name="status" value="debited" <?= !empty($data_wallets['status']) && $data_wallets['status'] == 'debited' ? 'checked' : '' ?> />
         <label class="form-check-label" for="status3">Debited</label>
      </div>
      <div class="form-check">
         <input class="form-check-input" type="radio" id="status4" name="status" value="favor" <?= !empty($data_wallets['status']) && $data_wallets['status'] == 'favor' ? 'checked' : '' ?> />
         <label class="form-check-label" for="status4">Favor</label>
      </div>
   </div>
   <div class="mb-3">
      <label for="created_at" class="form-label">Created At</label>
      <input type="text" name="created_at" value="<?= !empty($data_wallets['created_at']) ? $data_wallets['created_at'] : '' ?>" class="form-control datetime" id="created_at" data-toggle="datetimepicker" data-target="#created_at" />
   </div>
   <div class="mb-3">
      <label for="updated_at" class="form-label">Updated At</label>
      <input type="text" name="updated_at" value="<?= !empty($data_wallets['updated_at']) ? $data_wallets['updated_at'] : '' ?>" class="form-control datetime" id="updated_at" data-toggle="datetimepicker" data-target="#updated_at" />
   </div>
   <div class="form-group">
      <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <label for="error"></label>
   </div>
</form>