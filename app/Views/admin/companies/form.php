<form id="form" accept-charset="utf-8">
   <div class="mb-3">
      <label for="cr" class="form-label">Cr</label>
      <input type="text" name="cr" value="<?= !empty($data_companies['cr']) ? $data_companies['cr'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="name" class="form-label">Name</label>
      <input type="text" name="name" value="<?= !empty($data_companies['name']) ? $data_companies['name'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="industry" class="form-label">Industry</label>
      <input type="text" name="industry" value="<?= !empty($data_companies['industry']) ? $data_companies['industry'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="text" name="email" value="<?= !empty($data_companies['email']) ? $data_companies['email'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="phone_number" class="form-label">Phone Number</label>
      <input type="text" name="phone_number" value="<?= !empty($data_companies['phone_number']) ? $data_companies['phone_number'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="address" class="form-label">Address</label>
      <input type="text" name="address" value="<?= !empty($data_companies['address']) ? $data_companies['address'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="city" class="form-label">City</label>
      <input type="text" name="city" value="<?= !empty($data_companies['city']) ? $data_companies['city'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="state" class="form-label">State</label>
      <input type="text" name="state" value="<?= !empty($data_companies['state']) ? $data_companies['state'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="postal_code" class="form-label">Postal Code</label>
      <input type="text" name="postal_code" value="<?= !empty($data_companies['postal_code']) ? $data_companies['postal_code'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="country" class="form-label">Country</label>
      <input type="text" name="country" value="<?= !empty($data_companies['country']) ? $data_companies['country'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="website" class="form-label">Website</label>
      <input type="text" name="website" value="<?= !empty($data_companies['website']) ? $data_companies['website'] : '' ?>" class="form-control" />
   </div>
   <div class="mb-3">
      <label for="created_at" class="form-label">Created At</label>
      <input type="text" name="created_at" value="<?= !empty($data_companies['created_at']) ? $data_companies['created_at'] : '' ?>" class="form-control" />
   </div>
   <div class="form-group">
      <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <label for="error"></label>
   </div>
</form>