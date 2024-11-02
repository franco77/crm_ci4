<form id="form" accept-charset="utf-8">
    <div class="mb-3">
        <label for="id_position" class="form-label">Cargo</label>
        <select name="id_position" class="select2">
            <option value="">Selecciona una Opción</option>
            <?php foreach ($data_positions as $positions => $position): ?>
            <option value="<?= $position['id'] ?>"
                <?= !empty($data_employees['id_position']) && $data_employees['id_position'] == $position['id'] ? 'selected' : '' ?>>
                <?= $position['title'] ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="ic" class="form-label">Ic</label>
        <input type="text" name="ic" value="<?= !empty($data_employees['ic']) ? $data_employees['ic'] : '' ?>"
            class="form-control" />
    </div>

    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="first_name" class="form-label">Nombre</label>
                <input type="text" name="first_name"
                    value="<?= !empty($data_employees['first_name']) ? $data_employees['first_name'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="last_name" class="form-label">Apellido</label>
                <input type="text" name="last_name"
                    value="<?= !empty($data_employees['last_name']) ? $data_employees['last_name'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
    </div>



    <div class="mb-3">
        <label for="hire_date" class="form-label">Fecha de Ingreso</label>
        <input type="text" name="hire_date"
            value="<?= !empty($data_employees['hire_date']) ? $data_employees['hire_date'] : '' ?>" class="form-control"
            id="hire_date" autocomplete="off" placeholder="Click Para Seleccionar la fecha" onclick="$('#hire_date').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            }).datepicker('show');" />
    </div>

    <div class="mb-3">
        <div class="form-group">
            <label for="status">Estatus</label>
            <div>
                <label>
                    <input type="radio" name="status" value="Active"
                        <?= !empty($data_employees['status']) && $data_employees['status'] == 'Active' ? 'checked' : '' ?>>
                    Activo
                </label>
            </div>
            <div>
                <label>
                    <input type="radio" name="status" value="Inactive"
                        <?= !empty($data_employees['status']) && $data_employees['status'] == 'Inactive' ? 'checked' : '' ?>>
                    Inactivo
                </label>
            </div>
            <div>
                <label>
                    <input type="radio" name="status" value="Suspended"
                        <?= !empty($data_employees['status']) && $data_employees['status'] == 'Suspended' ? 'checked' : '' ?>>
                    Suspendido
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email"
                    value="<?= !empty($data_employees['email']) ? $data_employees['email'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="phone_number" class="form-label">Numero De Teléfono</label>
                <input type="text" name="phone_number"
                    value="<?= !empty($data_employees['phone_number']) ? $data_employees['phone_number'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
    </div>


    <div class="mb-3">
        <label for="salary" class="form-label">Salario</label>
        <input type="text" name="salary"
            value="<?= !empty($data_employees['salary']) ? $data_employees['salary'] : '' ?>" class="form-control" />
    </div>


    <input type="hidden" name="id" value="<?= !empty($data_employees['id']) ? $data_employees['id'] : '' ?>">

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary me-2">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
    </div>
</form>