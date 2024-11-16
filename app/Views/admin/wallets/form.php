<form id="form" enctype="multipart/form-data" accept-charset="utf-8">
    <div class="mb-3">
        <label for="user_id" class="form-label">Cliente</label>
        <select name="user_id" class="select2" id="clientSelect">
            <?php foreach ($data_customer as $customer): ?>
                <option value="<?= esc($customer->id) ?>"
                    <?= !empty($data_wallets['user_id']) && $data_wallets['user_id'] == $customer->id ? 'selected' : '' ?>>
                    <?= esc($customer->first_name) . ' ' . esc($customer->last_name) ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>


    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="amount" class="form-label">Importe</label>
                <input type="text" name="amount"
                    value="<?= !empty($data_wallets['amount']) ? $data_wallets['amount'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="remaining_amount" class="form-label">Importe restante</label>
                <input type="text" name="remaining_amount"
                    value="<?= !empty($data_wallets['remaining_amount']) ? $data_wallets['remaining_amount'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="deposit_date" class="form-label">Fecha de depósito</label>
        <input type="text" name="deposit_date"
            value="<?= !empty($data_wallets['deposit_date']) ? $data_wallets['deposit_date'] : '' ?>"
            class="form-control" id="deposit_date" autocomplete="off" placeholder="Click Para Seleccionar la fecha"
            onclick="$('#deposit_date').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            }).datepicker('show');" />
    </div>

    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="payment_method" class="form-label">Método de pago</label>
                <input type="text" name="payment_method"
                    value="<?= !empty($data_wallets['payment_method']) ? $data_wallets['payment_method'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="reference" class="form-label">Referencia</label>
                <input type="text" name="reference"
                    value="<?= !empty($data_wallets['reference']) ? $data_wallets['reference'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
    </div>


    <div class="mb-3">
        <label for="status" class="form-label">Estado</label>
        <br>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="status1" name="status" value="active"
                <?= !empty($data_wallets['status']) && $data_wallets['status'] == 'active' ? 'checked' : '' ?> />
            <label class="form-check-label" for="status1">Activo</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="status2" name="status" value="inactive"
                <?= !empty($data_wallets['status']) && $data_wallets['status'] == 'inactive' ? 'checked' : '' ?> />
            <label class="form-check-label" for="status2">Inactivo</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="status3" name="status" value="debited"
                <?= !empty($data_wallets['status']) && $data_wallets['status'] == 'debited' ? 'checked' : '' ?> />
            <label class="form-check-label" for="status3">Debitado</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="status4" name="status" value="favor"
                <?= !empty($data_wallets['status']) && $data_wallets['status'] == 'favor' ? 'checked' : '' ?> />
            <label class="form-check-label" for="status4">A Favor</label>
        </div>
    </div>

    <div class="mb-3">
        <label for="fileInput" class="form-label">Subir archivo</label>
        <input class="form-control" name="support" type="file" id="fileInput" />
    </div>

    <div class="mb-3">
        <label for="notes" class="form-label">Notas</label>
        <textarea name="notes"
            class="form-control"><?= !empty($data_wallets['notes']) ? $data_wallets['notes'] : '' ?></textarea>
    </div>


    <div class="form-group">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <label for="error"></label>
    </div>
</form>