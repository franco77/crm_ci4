<form id="form" accept-charset="utf-8">
    <div class="mb-3">

        <input type="hidden" name="employee_id" readonly
            value="<?= !empty($data_payrolls['employee_id']) ? $data_payrolls['employee_id'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="payroll_date" class="form-label"><?= lang('app.payrolls.payroll_date') ?></label>
        <input type="text" name="payroll_date"
            value="<?= !empty($data_payrolls['payroll_date']) ? $data_payrolls['payroll_date'] : '' ?>"
            class="form-control" id="payroll_date" />
    </div>
    <div class="mb-3">
        <label for="gross_salary" class="form-label"><?= lang('app.payrolls.gross_salary') ?></label>
        <input type="text" name="gross_salary"
            value="<?= !empty($data_payrolls['gross_salary']) ? $data_payrolls['gross_salary'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="loan_deductions" class="form-label"><?= lang('app.payrolls.loan_deductions') ?></label>
        <input type="text" name="loan_deductions"
            value="<?= !empty($data_payrolls['loan_deductions']) ? $data_payrolls['loan_deductions'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="deductfix" class="form-label"><?= lang('app.payrolls.deductfix') ?></label>
        <input type="text" name="deductfix"
            value="<?= !empty($data_payrolls['deductfix']) ? $data_payrolls['deductfix'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="bonus" class="form-label"><?= lang('app.payrolls.bonus') ?></label>
        <input type="text" name="bonus" value="<?= !empty($data_payrolls['bonus']) ? $data_payrolls['bonus'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="net_salary" class="form-label"><?= lang('app.payrolls.net_salary') ?></label>
        <input type="text" name="net_salary"
            value="<?= !empty($data_payrolls['net_salary']) ? $data_payrolls['net_salary'] : '' ?>"
            class="form-control" />
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary"><?= lang('app.btn_save') ?></button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('app.btn_close') ?></button>
        <label for="error"></label>
    </div>
</form>