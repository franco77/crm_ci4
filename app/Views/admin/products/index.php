<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>
<style>
.product-item {
    transition: opacity 0.5s ease, transform 0.5s ease;
    opacity: 1;
    transform: translateY(0);
    position: relative;
}

.product-item.hidden {
    opacity: 0;
    transform: translateY(20px);
    pointer-events: none;
}

#productContainer {
    transition: transform 0.5s ease;
    position: relative;
}
</style>


<div class="card-title"><?= $title ?></div>




<div class="container-fluid">
    <div class="my-4">
        <div class="row">
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-12">
                <div class="card custom-card products-navigation-card">
                    <div class="card-body p-0">

                        <div class="card-body p-0">
                            <div class="p-4 border-bottom">
                                <p class="fw-semibold mb-0 text-muted">BUSCAR</p>
                                <div class="px-2 py-3 pb-0">
                                    <div class="d-flex" role="search">
                                        <input class="form-control me-2" type="search" placeholder="Search"
                                            aria-label="Search" id="searchInput">
                                        <button class="btn btn-light" type="submit">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="p-4 border-bottom">
                            <p class="fw-semibold mb-0 text-muted">CATEGORÍAS</p>
                            <div class="px-2 py-3 pb-0" id="categoryFilters">
                                <?php foreach ($categories as $category): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input category-filter" type="checkbox"
                                        value="<?= $category['productLine'] ?>"
                                        id="category-<?= $category['productLine'] ?>">
                                    <label class="form-check-label" for="category-<?= $category['productLine'] ?>">
                                        <?= $category['productLine'] ?>
                                    </label>
                                    <span class="badge bg-light text-muted float-end"><?= $category['count'] ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="p-4 border-bottom">
                            <p class="fw-semibold mb-0 text-muted">RANGO DE PRECIOS</p>
                            <div class="px-2 py-3 pb-0" id="priceFilters">
                                <?php foreach ($priceRanges as $range): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input price-filter" type="checkbox"
                                        value="<?= $range['id'] ?>" id="price-<?= $range['id'] ?>"
                                        data-min="<?= $range['min'] ?>" data-max="<?= $range['max'] ?>">
                                    <label class="form-check-label" for="price-<?= $range['id'] ?>">
                                        $<?= $range['min'] ?> - $<?= $range['max'] ?>
                                    </label>
                                    <span class="badge bg-light text-muted float-end"><?= $range['count'] ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="p-4 border-bottom">
                            <p class="fw-semibold mb-0 text-muted">IR AL CARRITO</p>
                            <div class="px-2 py-3 pb-0" id="priceFilters">
                                <a href="<?= base_url('admin/cart/') ?>" class="btn btn-primary btn-wave w-100 mb-2">VER
                                    CARRITO</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-xxl-9 col-xl-8 col-lg-8 col-md-12">
                <div id="searchResults"></div>

                <div class="row" id="productContainer">
                    <?php foreach ($products as $product): ?>
                    <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-12 product-item"
                        data-category="<?= $product['productLine'] ?>" data-price="<?= $product['buyPrice'] ?>">
                        <div class="card custom-card product-card">
                            <div class="card-body">
                                <a href="<?= base_url('product/' . $product['id']) ?>" class="product-image">
                                    <img src="<?= base_url('uploads/products/' . $product['productImage']) ?>"
                                        class="card-img mb-3" alt="<?= $product['productName'] ?>"
                                        style="width: 245px; height: 200px; object-fit: cover;">
                                </a>
                                <div class="product-icons">
                                    <a href="javascript:void(0)" class="wishlist"
                                        data-product-id="<?= $product['id'] ?>">
                                        <i class="ri-heart-line" id="wishlist-icon-<?= $product['id'] ?>"></i>
                                    </a>
                                    <a href="#" class="cart add-to-cart" data-product-id="<?= $product['id'] ?>"><i
                                            class="ri-shopping-cart-line"></i></a>
                                    <a href="#" class="view" data-product-id="<?= $product['id'] ?>">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </div>
                                <p
                                    class="product-name fw-semibold mb-0 d-flex align-items-center justify-content-between">
                                    <?= $product['productName'] ?>
                                    <span class="float-end text-warning fs-12">4.2<i
                                            class="ri-star-s-fill align-middle ms-1 d-inline-block"></i></span>
                                </p>
                                <p class="product-description fs-11 text-muted mb-2">
                                    <?= substr($product['productDescription'], 0, 50) ?>...</p>
                                <p class="mb-1 fw-semibold fs-16 d-flex align-items-center justify-content-between">
                                    <span>$<?= number_format($product['buyPrice'], 2) ?></span>
                                </p>
                                <p class="fs-11 text-success fw-semibold mb-0 d-flex align-items-center">
                                    <i class="ti ti-discount-2 fs-16 me-1"></i>EN INVENTARIO:
                                    <?= $product['quantityInStock'] ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

        <!-- Pagination -->
        <!-- Mostrar productos -->


        <!-- Paginación -->
        <?= $pager->links('default', 'pagination_bootstrap') ?>

        <!-- Pagination -->
    </div>
</div>


<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Detalles del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- El contenido se cargará dinámicamente -->
            </div>
        </div>
    </div>
</div>




<?= $this->endSection() ?>
<!-- /.content -->

<!-- / Incluir la libreria toastr  -->
<!-- page script -->
<?= $this->section("js") ?>
<script>
$(document).ready(function() {
    function filterProducts() {
        var selectedCategories = [];
        var selectedPriceRanges = [];

        // Obtener categorías seleccionadas
        $('.category-filter:checked').each(function() {
            selectedCategories.push($(this).val());
        });

        // Obtener rangos de precio seleccionados
        $('.price-filter:checked').each(function() {
            selectedPriceRanges.push({
                min: parseFloat($(this).data('min')),
                max: parseFloat($(this).data('max'))
            });
        });

        var $productContainer = $('#productContainer');
        var $products = $('.product-item');
        var visibleProducts = [];
        var hiddenProducts = [];

        // Filtrar productos
        $products.each(function() {
            var $product = $(this);
            var productCategory = $product.data('category');
            var productPrice = parseFloat($product.data('price'));
            var categoryMatch = selectedCategories.length === 0 || selectedCategories.includes(
                productCategory);
            var priceMatch = selectedPriceRanges.length === 0 || selectedPriceRanges.some(range =>
                productPrice >= range.min && productPrice <= range.max
            );

            if (categoryMatch && priceMatch) {
                $product.removeClass('hidden');
                visibleProducts.push($product); // Almacenar productos visibles
            } else {
                $product.addClass('hidden');
                hiddenProducts.push($product); // Almacenar productos ocultos
            }
        });

        // Reorganizar los productos en el contenedor: visibles primero, ocultos después
        $productContainer.empty(); // Limpiar contenedor
        visibleProducts.forEach(function($product) {
            $productContainer.append($product); // Añadir productos visibles
        });
        hiddenProducts.forEach(function($product) {
            $productContainer.append($product); // Añadir productos ocultos (aunque no se mostrarán)
        });

        // Desplazar el contenedor de productos hacia la parte superior si hay productos visibles
        if (visibleProducts.length > 0) {
            $('html, body').animate({
                scrollTop: $productContainer.offset().top -
                    100 // Ajusta la posición según sea necesario
            }, 500);
        }
    }


    // Aplicar filtros cuando se cambia una selección
    $('.category-filter, .price-filter').on('change', filterProducts);

    // Inicialmente mostrar todos los productos
    filterProducts();

    var searchTimeout;
    var baseUrl = '<?= base_url() ?>'; // Asegúrate de que esta variable esté definida correctamente

    function performSearch() {
        var searchTerm = $('#searchInput').val();

        if (searchTerm.length < 3) {
            $('#searchResults').empty();
            return;
        }

        $.ajax({
            url: baseUrl + 'admin/products/search',
            method: 'GET',
            data: {
                term: searchTerm
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta recibida:', response);
                displayResults(response);
            },
            error: function(xhr, status, error) {
                console.error('Error en la búsqueda:', status, error);
                $('#searchResults').html(
                    '<p>Error al realizar la búsqueda. Por favor, intente de nuevo.</p>');
            }
        });
    }

    function displayResults(products) {
        console.log('Mostrando resultados para:', products);
        var resultsHtml = '';

        if (!Array.isArray(products) || products.length === 0) {
            resultsHtml = '<p>No se encontraron productos.</p>';
        } else {
            resultsHtml = `
                <div class="text-dark fs-26 fw-semibold mb-4"><span class="about-heading">Resultado</span></div>
                <div class="row" id="productContainer">`;

            products.forEach(function(product) {
                resultsHtml += `
                <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-12 product-item">
                    <div class="card custom-card product-card">
                        <div class="card-body">
                            <a href="${baseUrl}product/${product.id}" class="product-image">
                                <img src="${baseUrl}uploads/products/${product.productImage}" class="card-img mb-3" alt="${product.productName}" style="width: 245px; height: 200px; object-fit: cover;">
                            </a>
                            <div class="product-icons">
                                <a href="${baseUrl}wishlist/add/${product.id}" class="wishlist"><i class="ri-heart-line"></i></a>
                                <a href="#" class="cart add-to-cart" data-product-id="${product.id}"><i class="ri-shopping-cart-line"></i></a>
                                <a href="${baseUrl}product/${product.id}" class="view"><i class="ri-eye-line"></i></a>
                            </div>
                            <p class="product-name fw-semibold mb-0 d-flex align-items-center justify-content-between">
                                ${product.productName}
                                <span class="float-end text-warning fs-12">4.2<i class="ri-star-s-fill align-middle ms-1 d-inline-block"></i></span>
                            </p>
                            <p class="product-description fs-11 text-muted mb-2">${product.productDescription ? product.productDescription.substring(0, 50) + '...' : ''}</p>
                            <p class="mb-1 fw-semibold fs-16 d-flex align-items-center justify-content-between">
                                <span>$${parseFloat(product.buyPrice).toFixed(2)}</span>
                            </p>
                            <p class="fs-11 text-success fw-semibold mb-0 d-flex align-items-center">
                                <i class="ti ti-discount-2 fs-16 me-1"></i>EN INVENTARIO: ${product.quantityInStock}
                            </p>
                        </div>
                    </div>
                </div>`;
            });
            resultsHtml += '</div>';
        }

        console.log('HTML generado:', resultsHtml);
        $('#searchResults').html(resultsHtml);

        // Reinicializar los eventos para los nuevos elementos
        initializeProductEvents();
    }

    function initializeProductEvents() {
        $('.add-to-cart').off('click').on('click', function(e) {
            e.preventDefault();
            var productId = $(this).data('product-id');
            addToCart(productId);
        });
    }

    function addToCart(productId) {
        $.ajax({
            url: baseUrl + 'admin/cart/add',
            method: 'POST',
            data: {
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Producto añadido al carrito correctamente!');
                } else {
                    toastr.error('No se pudo añadir el producto al carrito. Intenta nuevamente.');
                }
            },
            error: function() {
                toastr.error('Ocurrió un error. Por favor, intenta nuevamente.');
            }
        });
    }


    function addToWishlist(productId) {
        $.ajax({
            url: baseUrl + 'admin/wishlist/add',
            method: 'POST',
            data: {
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Producto añadido a favoritos correctamente!');
                } else {
                    toastr.error('No se pudo añadir el producto a favoritos. Intenta nuevamente.');
                }
            },
            error: function() {
                toastr.error('Ocurrió un error. Por favor, intenta nuevamente.');
            }
        });
    }

    // Inicializar eventos para productos existentes
    initializeProductEvents();

    // Eventos de búsqueda
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });

    $('#searchButton').on('click', performSearch);



    // Wishlist

    const modal = {
        element: $('#productModal'),
        body: $('#productModal .modal-body'),
        image: $('#modalProductImage'),
        name: $('#modalProductName'),
        category: $('#modalProductCategory'),
        price: $('#modalProductPrice'),
        stock: $('#modalProductStock'),
        code: $('#modalProductCode'),
        description: $('#modalProductDescription'),
        addToCart: $('#modalAddToCart'),
        addToWishlist: $('#modalAddToWishlist')
    };

    // Función mejorada para cargar detalles del producto
    function loadProductDetails(productId) {
        console.log('Cargando producto ID:', productId); // Debug

        showModalLoading();

        $.ajax({
            url: `${baseUrl}admin/products/getDetails/${productId}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response); // Debug

                if (response && response.success && response.product) {
                    updateModalContent(response.product);
                } else {
                    const errorMsg = response.message ||
                        'No se pudo cargar la información del producto';
                    showModalError(errorMsg);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error Ajax:', error); // Debug
                showModalError(`Error al cargar los detalles del producto: ${error}`);
            }
        });
    }

    // Función mejorada para mostrar loading
    function showModalLoading() {
        modal.body.html(`
            <div class="d-flex justify-content-center align-items-center min-vh-30">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mb-0">Cargando detalles del producto...</p>
                </div>
            </div>
        `);
    }

    // Función mejorada para mostrar errores
    function showModalError(message) {
        modal.body.html(`
            <div class="alert alert-danger m-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ri-error-warning-line fs-24 me-3"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Error</h6>
                        <p class="mb-0">${message}</p>
                    </div>
                </div>
            </div>
        `);
    }

    // Función mejorada para actualizar el contenido
    function updateModalContent(product) {
        console.log('Actualizando modal con datos:', product); // Debug

        const modalContent = `
            <div class="row">
                <div class="col-md-6">
                    <img src="${baseUrl}uploads/products/${product.productImage}" 
                         alt="${product.productName}" 
                         class="img-fluid rounded mb-3" 
                         style="width: 100%; height: 300px; object-fit: cover;">
                </div>
                <div class="col-md-6">
                    <h4 class="mb-3">${product.productName}</h4>
                    <p class="mb-2">
                        <span class="text-muted">Categoría: </span>
                        <span>${product.productLine}</span>
                    </p>
                    <p class="mb-2">
                        <span class="text-muted">Precio: </span>
                        <span class="fs-4 fw-bold text-primary">$${parseFloat(product.buyPrice).toFixed(2)}</span>
                    </p>
                    <p class="mb-2">
                        <span class="text-muted">Stock: </span>
                        <span class="text-success">${product.quantityInStock} unidades</span>
                    </p>
                    <p class="mb-3">
                        <span class="text-muted">Código: </span>
                        <span>${product.productCode}</span>
                    </p>
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Descripción:</h6>
                        <p class="text-justify">${product.productDescription}</p>
                    </div>
                  <button class="btn btn-primary btn-wave" onclick="addToCart(${product.id})">
                    <i class="ri-shopping-cart-line me-2"></i>Agregar al Carrito
                </button>
                <button class="btn btn-outline-primary btn-wave" onclick="addToWishlist(${product.id})">
                    <i class="ri-heart-line me-2"></i>Agregar a Favoritos
                </button>
                    </div>
                </div>
            </div>
        `;

        modal.body.html(modalContent);
    }

    // Evento mejorado para el botón de vista
    $(document).on('click', '.view', function(e) {
        e.preventDefault();
        const productCard = $(this).closest('.product-card');
        const productId = productCard.find('.add-to-cart').data('product-id');

        console.log('Click en ver producto:', productId); // Debug

        if (!productId) {
            console.error('No se encontró el ID del producto');
            return;
        }

        const productModal = new bootstrap.Modal(document.getElementById('productModal'));
        productModal.show();
        loadProductDetails(productId);
    });

    function checkWishlistStatus(productId) {
        $.ajax({
            url: `${baseUrl}admin/wishlist/check/${productId}`,
            method: 'GET',
            success: function(response) {
                if (response.success && response.inWishlist) {
                    $(`#wishlist-icon-${productId}`)
                        .removeClass('ri-heart-line')
                        .addClass('ri-heart-fill text-danger');
                }
            }
        });
    }

    // Verificar estado inicial de wishlist para todos los productos
    $('.wishlist').each(function() {
        const productId = $(this).data('product-id');
        checkWishlistStatus(productId);
    });

    // Manejar clic en botón de wishlist
    $('.wishlist').on('click', function(e) {
        e.preventDefault();
        const $this = $(this);
        const productId = $this.data('product-id');
        const $icon = $this.find('i');
        const isInWishlist = $icon.hasClass('ri-heart-fill');

        // Animación mientras se procesa
        $icon.addClass('animate__animated animate__pulse');

        if (!isInWishlist) {
            // Agregar a wishlist
            $.ajax({
                url: `${baseUrl}admin/wishlist/add`,
                method: 'POST',
                data: {
                    product_id: productId
                },
                success: function(response) {
                    if (response.success) {
                        $icon
                            .removeClass('ri-heart-line animate__pulse')
                            .addClass('ri-heart-fill text-danger animate__bounceIn');

                        toastr.success('Producto añadido a favoritos');
                    } else {
                        toastr.error(response.message || 'No se pudo añadir a favoritos');
                        $icon.removeClass('animate__pulse');
                    }
                },
                error: function() {
                    toastr.error('Error al añadir a favoritos');
                    $icon.removeClass('animate__pulse');
                }
            });
        } else {
            // Remover de wishlist
            $.ajax({
                url: `${baseUrl}admin/wishlist/remove`,
                method: 'POST',
                data: {
                    product_id: productId
                },
                success: function(response) {
                    if (response.success) {
                        $icon
                            .removeClass('ri-heart-fill text-danger animate__pulse')
                            .addClass('ri-heart-line animate__bounceIn');

                        toastr.success('Producto eliminado de favoritos');
                    } else {
                        toastr.error(response.message ||
                            'No se pudo eliminar de favoritos');
                        $icon.removeClass('animate__pulse');
                    }
                },
                error: function() {
                    toastr.error('Error al eliminar de favoritos');
                    $icon.removeClass('animate__pulse');
                }
            });
        }
    });


});
</script>

<?= $this->endSection() ?>