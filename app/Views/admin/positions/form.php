<form id="form" accept-charset="utf-8">
    <div class="mb-3">
        <label for="title" class="form-label">Cargo</label>
        <input type="text" name="title" value="<?= !empty($data_positions['title']) ? $data_positions['title'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Descripción del cargo</label>
        <textarea name="description"
            class="form-control"><?= !empty($data_positions['description']) ? $data_positions['description'] : '' ?></textarea>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <label for="error"></label>
</form>