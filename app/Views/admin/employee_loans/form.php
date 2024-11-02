<form id="form" accept-charset="utf-8">
    <div class="mb-3">
        <label for="employee_id" class="form-label">Empleado</label>
        <select name="employee_id" class="select2">
            <?php foreach ($data_employees as $employees => $employee): ?>
            <option value="<?= $employee['id'] ?>"
                <?= !empty($data_employee_loans['employee_id']) && $data_employee_loans['employee_id'] == $employee['id'] ? 'selected' : '' ?>>
                <?= $employee['first_name'] . ' ' . $employee['last_name'] ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="amount" class="form-label">Monto</label>
        <input type="text" name="amount" id="amount"
            value="<?= !empty($data_employee_loans['amount']) ? $data_employee_loans['amount'] : '' ?>"
            class="form-control" />
    </div>

    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="total_quotas" class="form-label">Cantidad Cuotas</label>
                <input type="number" name="total_quotas" id="total_quotas"
                    value="<?= !empty($data_employee_loans['total_quotas']) ? $data_employee_loans['total_quotas'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="quotas_of" class="form-label">Pagos De </label>
                <input type="text" name="quotas_of" id="quotas_of"
                    value="<?= !empty($data_employee_loans['quotas_of']) ? $data_employee_loans['quotas_of'] : '' ?>"
                    class="form-control" />
            </div>

        </div>
    </div>





    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="start_date" class="form-label">Fecha Inicio</label>
                <input type="text" name="start_date"
                    value="<?= !empty($data_employee_loans['start_date']) ? $data_employee_loans['start_date'] : '' ?>"
                    class="form-control" id="start_date" autocomplete="off" placeholder="Selecciona Una Fecha" onclick="$('#start_date').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            }).datepicker('show');" />
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="end_date" class="form-label">Fecha Fin</label>
                <input type="text" name="end_date"
                    value="<?= !empty($data_employee_loans['end_date']) ? $data_employee_loans['end_date'] : '' ?>"
                    class="form-control" id="end_date" autocomplete="off" placeholder="Selecciona Una Fecha" onclick="$('#end_date').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            }).datepicker('show');" />
            </div>
        </div>
    </div>


    <div class="mb-3">
        <label for="type" class="form-label">Tipo Préstamo</label>
        <select name="type" id="type" class="select2">
            <?php
            $options = [
                'personal' =>  'Personal',
                'familiar' => 'Familiar',
                'none' => 'Nada'
            ];
            foreach ($options as $value => $label): ?>
            <option value="<?= $value ?>"
                <?= !empty($data_employee_loans['type']) && $data_employee_loans['type'] == $value ? 'selected' : '' ?>>
                <?= $label ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Descripción</label>
        <textarea name="description" id="summernote"
            class="form-control"><?= !empty($data_employee_loans['description']) ? $data_employee_loans['description'] : '' ?></textarea>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Estatus</label>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="status1" name="status" value="pending"
                <?= !empty($data_employee_loans['status']) && $data_employee_loans['status'] == 'pending' ? 'checked' : '' ?> />
            <label class="form-check-label" for="status1">Pending</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="status2" name="status" value="paid"
                <?= !empty($data_employee_loans['status']) && $data_employee_loans['status'] == 'paid' ? 'checked' : '' ?> />
            <label class="form-check-label" for="status2">Pago</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="status3" name="status" value="canceled"
                <?= !empty($data_employee_loans['status']) && $data_employee_loans['status'] == 'canceled' ? 'checked' : '' ?> />
            <label class="form-check-label" for="status3">Cancelado</label>
        </div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <label for="error"></label>
    </div>
</form>

<script>
$(document).ready(function() {

    $('#amount, #total_quotas').on('input', function() {
        var amount = parseFloat($('#amount').val());
        var totalQuotas = parseInt($('#total_quotas').val());
        if (!isNaN(amount) && amount > 0 && !isNaN(totalQuotas) && totalQuotas > 0) {
            var quotaAmount = amount / totalQuotas;
            quotaAmount = quotaAmount.toFixed(2);

            $('#quotas_of').val(quotaAmount);
        } else {
            $('#quotas_of').val('');
        }
    });
});
</script>