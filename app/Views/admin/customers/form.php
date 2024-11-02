<form id="form" accept-charset="utf-8">
    <div class="mb-3">
        <label for="ic" class="form-label">Ic</label>
        <input type="text" name="ic" value="<?= !empty($data_customers['ic']) ? $data_customers['ic'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="first_name" class="form-label">First Name</label>
        <input type="text" name="first_name"
            value="<?= !empty($data_customers['first_name']) ? $data_customers['first_name'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" name="last_name"
            value="<?= !empty($data_customers['last_name']) ? $data_customers['last_name'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="text" name="email" value="<?= !empty($data_customers['email']) ? $data_customers['email'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="phone_number" class="form-label">Phone Number</label>
        <input type="text" name="phone_number"
            value="<?= !empty($data_customers['phone_number']) ? $data_customers['phone_number'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <input type="text" name="address"
            value="<?= !empty($data_customers['address']) ? $data_customers['address'] : '' ?>" class="form-control" />
    </div>
    <div class="mb-3">
        <label for="city" class="form-label">City</label>
        <input type="text" name="city" value="<?= !empty($data_customers['city']) ? $data_customers['city'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="state" class="form-label">State</label>
        <input type="text" name="state" value="<?= !empty($data_customers['state']) ? $data_customers['state'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="postal_code" class="form-label">Postal Code</label>
        <input type="text" name="postal_code"
            value="<?= !empty($data_customers['postal_code']) ? $data_customers['postal_code'] : '' ?>"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="country" class="form-label">Country</label>
        <input type="text" name="country"
            value="<?= !empty($data_customers['country']) ? $data_customers['country'] : '' ?>" class="form-control" />
    </div>


    <input type="hidden" name="id" value="<?= !empty($data_customers['id']) ? $data_customers['id'] : '' ?>">


    <div class="mb-3">
        <label for="created_at" class="form-label">Created At</label>
        <input type="text" name="created_at"
            value="<?= !empty($data_customers['created_at']) ? $data_customers['created_at'] : '' ?>"
            class="form-control" />
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <label for="error"></label>
    </div>
</form>