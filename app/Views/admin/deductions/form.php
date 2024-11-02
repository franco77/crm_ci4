<form id="form" accept-charset="utf-8">
    <div class="mb-3">
        <label for="description" class="form-label"><?= lang('app.deductions.description') ?></label>
        <input type="text" name="description"
            value="<?= !empty($data_deductions['description']) ? $data_deductions['description'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="amount" class="form-label"><?= lang('app.deductions.amount') ?></label>
        <input type="number" name="amount"
            value="<?= !empty($data_deductions['amount']) ? $data_deductions['amount'] : '' ?>" class="form-control" />
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary"><?= lang('app.btn_save')?></button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('app.btn_close')?></button>
        <label for="error"></label>
    </div>
</form>