<form id="form" accept-charset="utf-8">
    <div class="mb-3">
        <label for="loan_id" class="form-label"><?= lang('app.loan_payments.loan') ?></label>
        <select name="loan_id" class="select2">
            <option value=""><?= lang('app.loan_payments.select_to_loan') ?></option>
            <?php foreach ($data_employee_loans as $employee_loan): ?>
            <option value="<?= $employee_loan['loan_id'] ?>"
                <?= !empty($data_loan_payments['loan_id']) && $data_loan_payments['loan_id'] == $employee_loan['loan_id'] ? 'selected' : '' ?>>
                <?= $employee_loan['first_name'] . ' ' . $employee_loan['last_name'] . ' - ' . number_format($employee_loan['loan_amount'], 2) ?>
            </option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="col-md-8">
        <div id="employee_data"></div>
    </div>
    <br>



    <div class="mb-3">
        <label for="payment_date" class="form-label"><?= lang('app.loan_payments.payment_date') ?></label>
        <input type="text" name="payment_date"
            value="<?= !empty($data_loan_payments['payment_date']) ? $data_loan_payments['payment_date'] : '' ?>"
            class="form-control" id="payment_date" autocomplete="off" placeholder="<?= lang('app.placeholder_date') ?>"
            onclick="$('#payment_date').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            }).datepicker('show');" />
    </div>
    <div class="mb-3">
        <label for="amount" class="form-label"><?= lang('app.loan_payments.amount') ?></label>
        <input type="text" name="amount"
            value="<?= !empty($data_loan_payments['amount']) ? $data_loan_payments['amount'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="description" class="form-label"><?= lang('app.loan_payments.description') ?></label>
        <textarea name="description" rows="7"
            class="form-control"><?= !empty($data_loan_payments['description']) ? $data_loan_payments['description'] : '' ?></textarea>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label"><?= lang('app.loan_payments.status') ?></label>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="status1" name="status" value="pending"
                <?= !empty($data_loan_payments['status']) && $data_loan_payments['status'] == 'pending' ? 'checked' : '' ?> />
            <label class="form-check-label" for="status1"><?= lang('app.loan_payments.status1') ?></label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="status2" name="status" value="paid"
                <?= !empty($data_loan_payments['status']) && $data_loan_payments['status'] == 'paid' ? 'checked' : '' ?> />
            <label class="form-check-label" for="status2"><?= lang('app.loan_payments.status2') ?></label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="status3" name="status" value="canceled"
                <?= !empty($data_loan_payments['status']) && $data_loan_payments['status'] == 'canceled' ? 'checked' : '' ?> />
            <label class="form-check-label" for="status3"><?= lang('app.loan_payments.status3') ?></label>
        </div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary"><?= lang('app.btn_save') ?></button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('app.btn_close') ?></button>
        <label for="error"></label>
    </div>
</form>

<script>
$(document).ready(function() {
    $('select[name="loan_id"]').on('change', function() {
        var loan_id = $(this).val();
        $.ajax({
            type: 'POST',
            url: '<?= base_url('loanpayments/getLoanPayData') ?>',
            data: {
                loan_id: loan_id
            },
            dataType: 'json',
            success: function(response) {
                $('#employee_data').html(''); // Limpiar contenido anterior

                if (response.status === 'success') {
                    var data = response.data;
                    // Construir la tabla
                    var table =
                        '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
                    table += '<tr><th><?= lang("app.employees.full_name") ?></th><td>' +
                        data.first_name + ' ' + data.last_name + '</td></tr>';
                    table += '<tr><th><?= lang("app.employees.ic") ?></th><td>' + data.ic +
                        '</td></tr>';
                    table +=
                        '<tr><th><?= lang("app.employee_loans.total_quotas") ?></th><td>' +
                        data.total_quotas + '</td></tr>';
                    table +=
                        '<tr><th><?= lang("app.employee_loans.quotas_of") ?></th><td>' +
                        data.quotas_of + '</td></tr>';
                    // Agrega más filas según sea necesario
                    table += '</table>';


                    // Añadir la tabla al contenedor
                    $('#employee_data').html(table);
                } else {
                    $('#employee_data').html('<p>' + response.message + '</p>');
                }
            },
            error: function() {
                $('#employee_data').html(
                    '<p>Hubo un error al obtener los datos. Por favor, inténtelo de nuevo.</p>'
                );
            }
        });
    });
});
</script>