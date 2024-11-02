<style>
.ck-editor__editable_inline {
    min-height: 200px;
}
</style>
<form id="form" accept-charset="utf-8" enctype="multipart/form-data">

    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="productCode" class="form-label">Sku</label>
                <input type="text" name="productCode"
                    value="<?= !empty($data_products['productCode']) ? $data_products['productCode'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" name="productName"
                    value="<?= !empty($data_products['productName']) ? $data_products['productName'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
    </div>





    <div class="mb-3">
        <label for="productVendor" class="form-label">Proveedor</label>
        <select name="productVendor" class="select2">
            <option value="">Seleccione una opción</option>
            <?php foreach ($data_vendors as $vendors): ?>
            <option value="<?= $vendors['name'] ?>"
                <?= !empty($data_products['productVendor']) && $data_products['productVendor'] == $vendors['name'] ? 'selected' : '' ?>>
                <?= $vendors['name'] ?></option>
            <?php endforeach ?>
        </select>
    </div>



    <div class="mb-3">
        <label for="productLine" class="form-label">Category</label>
        <select name="productLine" class="select2">
            <option value="">Seleccione una opción</option>
            <?php foreach ($data_category as $category): ?>
            <option value="<?= $category['name'] ?>"
                <?= !empty($data_products['productLine']) && $data_products['productLine'] == $category['name'] ? 'selected' : '' ?>>
                <?= $category['name'] ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="quantityInStock" class="form-label">Quantity InStock</label>
                <input type="text" name="quantityInStock"
                    value="<?= !empty($data_products['quantityInStock']) ? $data_products['quantityInStock'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="buyPrice" class="form-label">Buy Price</label>
                <input type="text" name="buyPrice"
                    value="<?= !empty($data_products['buyPrice']) ? $data_products['buyPrice'] : '' ?>"
                    class="form-control" />
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="productDescription" class="form-label">Product Description</label>
        <textarea name="productDescription" id="ckeditor-editor"
            class="form-control"><?= !empty($data_products['productDescription']) ? $data_products['productDescription'] : '' ?></textarea>
    </div>

    <div class="mb-3">
        <label for="productImage" class="form-label">Imagen del Producto</label>
        <input type="file" name="productImage" id="productImage" class="form-control" accept="image/*" />
        <?php if (!empty($data_products['productImage'])): ?>
        <div class="mt-2">
            <img src="<?= base_url('uploads/products/' . $data_products['productImage']) ?>"
                alt="Imagen actual del producto" class="img-thumbnail" style="max-width: 200px;">
            <p class="mt-1">Imagen actual: <?= $data_products['productImage'] ?></p>
        </div>
        <?php endif; ?>
    </div>

    <input type="hidden" name="id" value="<?= !empty($data_products['id']) ? $data_products['id'] : '' ?>">

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <label for="error"></label>
    </div>
</form>

<script>
document.getElementById('productImage').addEventListener('change', function(event) {
    var file = event.target.files[0];
    var reader = new FileReader();
    reader.onload = function(e) {
        var preview = document.createElement('img');
        preview.src = e.target.result;
        preview.className = 'img-thumbnail mt-2';
        preview.style.maxWidth = '200px';

        var previewContainer = document.getElementById('productImage').parentNode;
        var existingPreview = previewContainer.querySelector('img');
        if (existingPreview) {
            previewContainer.removeChild(existingPreview);
        }
        previewContainer.appendChild(preview);
    }
    reader.readAsDataURL(file);
});
</script>