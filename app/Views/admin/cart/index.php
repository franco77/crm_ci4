<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-xxl-9">
            <div class="card custom-card" id="cart-container">
                <div class="card-header">
                    <div class="card-title">
                        Carrito de compras
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($cartItems)): ?>
                    <div class="text-center">
                        <h3>Tu carrito esta vacío</h3>
                        <p>¡Agrega algunos artículos a tu carrito para comenzar!</p>
                        <a href="<?= base_url('products') ?>" class="btn btn-primary">Seguir comprando</a>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-nowrap">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items-body">
                                <?php foreach ($cartItems as $item): ?>
                                <tr id="cart-item-<?= $item['cart_item_id'] ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <span class="avatar avatar-md bg-light">
                                                    <img src="<?= base_url('uploads/products/' . $item['productImage']) ?>"
                                                        alt="<?= $item['productName'] ?>">
                                                </span>
                                            </div>
                                            <div>
                                                <div class="mb-1 fs-14 fw-semibold">
                                                    <a
                                                        href="<?= base_url('product/' . $item['product_id']) ?>"><?= $item['productName'] ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold fs-14">
                                            $<?= number_format($item['buyPrice'], 2) ?>
                                        </div>
                                    </td>
                                    <td class="product-quantity-container">
                                        <input type="hidden" name="product_image[]"
                                            value="<?= $item['productImage'] ?>">
                                        <input type="hidden" name="product_id[]" value="<?= $item['product_id'] ?>">
                                        <div class="input-group border rounded flex-nowrap" style="width: 120px;">
                                            <button
                                                class="btn btn-icon btn-light input-group-text flex-fill product-quantity-minus"
                                                data-cart-item-id="<?= $item['cart_item_id'] ?>"><i
                                                    class="ri-subtract-line"></i></button>
                                            <input type="number"
                                                class="form-control form-control-sm border-0 text-center w-100 product-quantity"
                                                aria-label="quantity" value="<?= $item['quantity'] ?>" min="1"
                                                max="<?= $item['quantityInStock'] ?>"
                                                data-cart-item-id="<?= $item['cart_item_id'] ?>">
                                            <button
                                                class="btn btn-icon btn-light input-group-text flex-fill product-quantity-plus"
                                                data-cart-item-id="<?= $item['cart_item_id'] ?>"><i
                                                    class="ri-add-line"></i></button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fs-14 fw-semibold item-total"
                                            data-cart-item-id="<?= $item['cart_item_id'] ?>">
                                            $<?= number_format($item['buyPrice'] * $item['quantity'], 2) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-icon btn-danger btn-delete"
                                            data-cart-item-id="<?= $item['cart_item_id'] ?>">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-xxl-3">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Order Summary
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-muted">Subtotal</div>
                        <div class="fw-semibold fs-14" id="subtotal">$0.00</div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-muted">Shipping</div>
                        <div class="fw-semibold fs-14" id="shipping">$0.00</div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-muted">Tax</div>
                        <div class="fw-semibold fs-14" id="tax">$0.00</div>
                    </div>
                    <hr>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="fw-semibold">Total</div>
                        <div class="fw-semibold fs-18 text-primary" id="total">$0.00</div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="#" class="btn btn-success btn-wave w-100 mb-2 btn-generate-order">Generar Pedido
                        Automático</a>
                    <a href="<?= base_url('checkout') ?>" class="btn btn-primary btn-wave w-100 mb-2">Pagar</a>
                    <a href="<?= base_url('admin/products/list') ?>" class="btn btn-light btn-wave w-100">Continue
                        Shopping</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section("js") ?>
<script>
$(document).ready(function() {
    // Función para cargar el resumen del pedido al cargar la página
    function loadOrderSummary() {
        $.ajax({
            url: '<?= base_url('admin/cart/summary') ?>', // Endpoint que calcula los totales
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    updateOrderSummary(response.subtotal, response.shipping, response.tax, response
                        .total);
                } else {
                    toastr.error('Failed to load order summary. Please try again.');
                }
            },
            error: function() {
                toastr.error(
                    'An error occurred while loading the order summary. Please try again.');
            }
        });
    }

    // Función para actualizar el total de un artículo específico
    function updateCartItem(cartItemId, quantity) {
        $.ajax({
            url: '<?= base_url('admin/cart/update') ?>',
            method: 'POST',
            data: {
                cart_item_id: cartItemId,
                quantity: quantity
            },
            success: function(response) {
                if (response.success) {
                    updateItemTotal(cartItemId, response.itemTotal);
                    updateOrderSummary(response.subtotal, response.shipping, response.tax, response
                        .total);
                } else {
                    toastr.error('Failed to update cart. Please try again.');
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    }

    function updateItemTotal(cartItemId, total) {
        $('.item-total[data-cart-item-id="' + cartItemId + '"]').text('$' + total);
    }

    function updateOrderSummary(subtotal, shipping, tax, total) {
        $('#subtotal').text('$' + subtotal);
        $('#shipping').text('$' + shipping);
        $('#tax').text('$' + tax);
        $('#total').text('$' + total);
    }

    // Cargar el resumen del pedido cuando se carga la página
    loadOrderSummary();

    // Eventos de incremento/decremento de cantidad
    $('.product-quantity-minus').on('click', function() {
        var input = $(this).siblings('.product-quantity');
        var value = parseInt(input.val());
        if (value > 1) {
            input.val(value - 1);
            updateCartItem($(this).data('cart-item-id'), value - 1);
        }
    });

    $('.product-quantity-plus').on('click', function() {
        var input = $(this).siblings('.product-quantity');
        var value = parseInt(input.val());
        var max = parseInt(input.attr('max'));
        if (value < max) {
            input.val(value + 1);
            updateCartItem($(this).data('cart-item-id'), value + 1);
        }
    });

    $('.product-quantity').on('change', function() {
        var value = parseInt($(this).val());
        var min = parseInt($(this).attr('min'));
        var max = parseInt($(this).attr('max'));
        if (value < min) {
            $(this).val(min);
            value = min;
        } else if (value > max) {
            $(this).val(max);
            value = max;
        }
        updateCartItem($(this).data('cart-item-id'), value);
    });

    // Manejo del botón eliminar para borrar un producto del carrito
    $('.btn-delete').on('click', function() {
        var cartItemId = $(this).data('cart-item-id');

        // Usar SweetAlert para confirmar la eliminación
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/cart/remove') ?>',
                    method: 'POST',
                    data: {
                        cart_item_id: cartItemId
                    },
                    success: function(response) {
                        if (response.success) {
                            // Eliminar la fila del producto eliminado
                            $('#cart-item-' + cartItemId).remove();
                            updateOrderSummary(response.subtotal, response.shipping,
                                response.tax, response.total);

                            // Mostrar notificación con Toastr
                            toastr.success('Producto eliminado del carrito.');

                            // Si el carrito está vacío, mostrar el mensaje de carrito vacío
                            if (response.subtotal == '0.00') {
                                $('#cart-container').html(
                                    ' <div class="card-header"><div class="card-title">Carrito de compras</div></div><div class="card-body"><div class="text-center"><h3>Tu carrito esta vacío</h3><a href="<?= base_url('products') ?>" class="btn btn-primary">Seguir comprando</a></div></div>'
                                );
                            }
                        } else {
                            toastr.error(
                                'Failed to remove item from cart. Please try again.'
                            );
                        }
                    },
                    error: function() {
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            }
        });
    });



    $(document).ready(function() {
        $('.btn-generate-order').on('click', function(e) {
            e.preventDefault();

            // Obtener datos del resumen
            var subtotal = parseFloat($('#subtotal').text().replace('$', ''));
            var tax = parseFloat($('#tax').text().replace('$', ''));
            var total = parseFloat($('#total').text().replace('$', ''));
            var client_id =
                123; // Supongamos que obtienes el ID del cliente de otra forma (sesión o usuario autenticado)

            // Crear el objeto de datos que enviaremos al servidor
            var data = {
                client_id: client_id, // ID del cliente
                invoice_total: total,
                invoice_subtotal: subtotal,
                tax: tax,
                amount_paid: 0, // No se ha pagado nada aún
                amount_due: total, // La cantidad pendiente es el total
                notes: '', // Podrías agregar un campo de notas si es necesario
                product_id: [],
                product_name: [],
                product_image: [],
                quantity: [],
                price: []
            };

            // Recorrer los productos del carrito
            $('#cart-items-body tr').each(function() {
                var product_id = $(this).find('input[name="product_id[]"]')
                    .val(); // Cambia aquí para obtener el product_id real
                var product_name = $(this).find('a').text().trim();
                var product_image = $(this).find('input[name="product_image[]"]').val();
                var quantity = $(this).find('.product-quantity').val();
                var price = $(this).find('.item-total').text().replace('$', '');

                // Rellenar los arrays con los productos del carrito
                data.product_id.push(product_id);
                data.product_name.push(product_name);
                data.product_image.push(product_image);
                data.quantity.push(quantity);
                data.price.push(price);
            });

            // Mostrar SweetAlert para confirmar la creación del pedido
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se generará el pedido automáticamente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, generar pedido',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar los datos al servidor usando AJAX si el usuario confirma
                    $.ajax({
                        url: '<?= base_url("admin/order/create") ?>', // Ajusta la ruta al método de tu controlador
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 201) {
                                Swal.fire(
                                    'Pedido creado',
                                    'El pedido ha sido generado con éxito con el ID: ' +
                                    response.invoice_id,
                                    'success'
                                ).then(() => {
                                    // Redirigir al usuario a la página de confirmación
                                    window.location.href =
                                        '<?= base_url("order/confirmation/") ?>' +
                                        response.invoice_id;
                                });
                            } else {
                                Swal.fire('Error',
                                    'No se pudo generar el pedido.',
                                    'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error',
                                'Ocurrió un error al generar el pedido.',
                                'error');
                        }
                    });
                }
            });
        });
    });

});
</script>
<?= $this->endSection() ?>