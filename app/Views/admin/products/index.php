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
                                        <a href="<?= base_url('wishlist/add/' . $product['id']) ?>" class="wishlist"><i
                                                class="ri-heart-line"></i></a>
                                        <a href="#" class="cart add-to-cart" data-product-id="<?= $product['id'] ?>"><i
                                                class="ri-shopping-cart-line"></i></a>
                                        <a href="<?= base_url('product/' . $product['id']) ?>" class="view"><i
                                                class="ri-eye-line"></i></a>
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
        <div class="row">
            <?php foreach ($products as $product): ?>
                <!-- Aquí tu código para cada producto -->
            <?php endforeach; ?>
        </div>

        <!-- Paginación -->
        <?= $pager->links('default', 'pagination_bootstrap') ?>

        <!-- Pagination -->
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
                        // Aquí se utiliza Toastr para mostrar la notificación
                        toastr.success('Producto añadido al carrito correctamente!');
                        // Aquí puedes actualizar el icono o contador del carrito si es necesario
                    } else {
                        toastr.error('No se pudo añadir el producto al carrito. Intenta nuevamente.');
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
    });
</script>

<?= $this->endSection() ?>