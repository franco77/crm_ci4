<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>
<style>
/* Animaciones para los items de wishlist */
.wishlist-item {
    transition: all 0.3s ease-in-out;
}

.btn-delete {
    transition: all 0.2s ease;
}

.btn-delete:hover {
    transform: scale(1.1);
    color: #dc3545;
}

/* Animación para el badge del contador */
.badge.animate__bounceIn {
    animation-duration: 0.5s;
}

/* Animación de fadeOut para eliminación */
.animate__fadeOut {
    animation-duration: 0.5s;
}

/* Estilo para el botón deshabilitado */
.btn-delete:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Tooltip para el botón de eliminar */
.btn-delete {
    position: relative;
}

.btn-delete::after {
    content: "Eliminar de favoritos";
    position: absolute;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.2s ease;
}

.btn-delete:hover::after {
    opacity: 1;
    visibility: visible;
}
</style>

<!-- Start::row-1 -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div class="fs-15 mb-0">
                    Total <span class="badge bg-success"><?= count($wishlist) ?></span> productos en favoritos
                </div>
                <div class="d-flex" role="search">
                    <input class="form-control form-control-sm me-2" type="search" placeholder="Buscar en favoritos"
                        id="searchWishlist" aria-label="Search">
                    <button class="btn btn-sm btn-light" type="button" id="searchButton">Buscar</button>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($wishlist)): ?>
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body text-center py-5">
                <i class="ri-heart-line fs-3 text-muted mb-3"></i>
                <h5 class="fw-semibold">Tu lista de favoritos está vacía</h5>
                <p class="text-muted">Explora nuestros productos y agrega los que más te gusten a tu lista.</p>
                <a href="<?= base_url('admin/products') ?>" class="btn btn-primary">
                    <i class="ri-shopping-bag-line me-2"></i>Ver Productos
                </a>
            </div>
        </div>
    </div>
    <?php else: ?>
    <?php foreach ($wishlist as $item): ?>
    <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-12 wishlist-item"
        data-name="<?= strtolower($item['productName']) ?>">
        <div class="card custom-card product-card">
            <div class="card-body">
                <a href="<?= base_url('admin/products/details/' . $item['product_id']) ?>" class="product-image">
                    <img src="<?= base_url('uploads/products/' . $item['productImage']) ?>" class="card-img mb-3"
                        alt="<?= $item['productName'] ?>" style="width: 100%; height: 200px; object-fit: cover;">
                </a>
                <div class="product-icons">
                    <a href="javascript:void(0);" class="wishlist btn-delete"
                        data-product-id="<?= $item['product_id'] ?>">
                        <i class="ri-close-line"></i>
                    </a>
                </div>
                <p class="product-name fw-semibold mb-0 d-flex align-items-center justify-content-between">
                    <?= $item['productName'] ?>
                    <span class="float-end text-warning fs-12">4.2<i
                            class="ri-star-s-fill align-middle ms-1"></i></span>
                </p>
                <p class="product-description fs-11 text-muted mb-2">
                    <?= substr($item['productDescription'], 0, 50) ?>...
                </p>
                <p class="mb-1 fw-semibold fs-16 d-flex align-items-center justify-content-between">
                    <span>$<?= number_format($item['buyPrice'], 2) ?></span>
                </p>
                <p class="fs-11 text-success fw-semibold mb-0 d-flex align-items-center">
                    <i class="ti ti-discount-2 fs-16 me-1"></i>
                    En Inventario: <?= $item['quantityInStock'] ?>
                </p>
            </div>
            <div class="card-footer text-center">
                <button class="btn btn-primary-light m-1 btn-move-to-cart" data-product-id="<?= $item['product_id'] ?>">
                    <i class="ri-shopping-cart-2-line me-2 align-middle"></i>
                    Mover al Carrito
                </button>
                <button class="btn btn-success-light m-1 btn-view-product" data-product-id="<?= $item['product_id'] ?>">
                    <i class="ri-eye-line me-2 align-middle"></i>
                    Ver Producto
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Paginación -->
<?php if ($pager): ?>
<div class="d-flex justify-content-end mt-4">
    <?= $pager->links('group1', 'pagination_bootstrap') ?>
</div>
<?php endif; ?>


<?= $this->endSection() ?>

<?= $this->section("js") ?>
<script>
const baseUrl = '<?= base_url() ?>'; // Define baseUrl si no está definido
$(document).ready(function() {
    $('#searchWishlist').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.wishlist-item').each(function() {
            const productName = $(this).data('name');
            $(this).toggle(productName.includes(searchTerm));
        });
    });

    function updateWishlistCount() {
        const count = $('.wishlist-item:visible').length;
        $('.badge.bg-success').text(count);
        if (count === 0) {
            $('.row').html(
                `<div class="col-12"><div class="card custom-card"><div class="card-body text-center py-5"><i class="ri-heart-line fs-3 text-muted mb-3"></i><h5 class="fw-semibold">Tu lista de favoritos está vacía</h5><p class="text-muted">Explora nuestros productos y agrega los que más te gusten a tu lista.</p><a href="${baseUrl}admin/products" class="btn btn-primary"><i class="ri-shopping-bag-line me-2"></i>Ver Productos</a></div></div></div>`
            );
        }
    }

    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const $item = $btn.closest('.wishlist-item');
        const productId = $btn.data('product-id');
        $btn.prop('disabled', true);
        Swal.fire({
            title: '¿Eliminar de favoritos?',
            text: '¿Estás seguro de eliminar este producto de tu lista de favoritos?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}admin/wishlist/remove`,
                    method: 'POST',
                    data: {
                        product_id: productId
                    },
                    dataType: 'json'
                }).done(function(response) {
                    if (response.success) {
                        $item.fadeOut(300, function() {
                            $(this).remove();
                            updateWishlistCount();
                        });
                        toastr.success('Producto eliminado de favoritos');
                    } else {
                        toastr.error(response.message ||
                            'Error al eliminar el producto');
                    }
                }).fail(function() {
                    toastr.error('Error al procesar la solicitud');
                }).always(function() {
                    $btn.prop('disabled', false);
                });
            } else {
                $btn.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.btn-move-to-cart', function() {
        const $button = $(this);
        const productId = $button.data('product-id');
        $button.prop('disabled', true);
        $.ajax({
            url: `${baseUrl}admin/cart/add`,
            method: 'POST',
            data: {
                product_id: productId
            }
        }).done(function(response) {
            if (response.success) {
                toastr.success('Producto agregado al carrito');
                $button.closest('.wishlist-item').fadeOut(300, function() {
                    $(this).remove();
                    updateWishlistCount();
                });
            } else {
                toastr.error('Error al agregar al carrito');
            }
        }).fail(function() {
            toastr.error('Error al procesar la solicitud');
        }).always(function() {
            $button.prop('disabled', false);
        });
    });

    $(document).on('click', '.btn-view-product', function() {
        const productId = $(this).data('product-id');
        window.location.href = `${baseUrl}admin/products/details/${productId}`;
    });
});
</script>
<?= $this->endSection() ?>