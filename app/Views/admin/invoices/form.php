<form id="form" accept-charset="utf-8">
    <div class="row">
        <div class="col-md-7">
            <div class="mb-3">
                <label for="client_id" class="form-label">Propietario</label>
                <select name="client_id" class="select2" id="clientSelect">
                    <option value="">Selecciona un Usuario</option>
                    <?php foreach ($data_customer as $customer): ?>
                        <option value="<?= esc($customer->id) ?>"
                            <?= !empty($data_invoices['client_id']) && $data_invoices['client_id'] == $customer->id ? 'selected' : '' ?>>
                            <?= esc($customer->first_name) . ' ' . esc($customer->last_name) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <!-- Div donde se mostrarán los detalles del propietario -->
            <div id="customerDetails">
                <!-- Aquí se llenarán los datos del cliente seleccionado -->
            </div>
        </div>
        <div class="col-md-5">
            <div class="mb-3">
                <label for="date_invoice" class="form-label">Fecha de Factura</label>
                <input type="text" name="date_invoice"
                    value="<?= !empty($data_invoices['date_invoice']) ? $data_invoices['date_invoice'] : '' ?>"
                    class="form-control" id="date_invoice" autocomplete="off"
                    placeholder="Click Para Seleccionar la fecha" onclick="$('#date_invoice').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            }).datepicker('show');" />
            </div>
        </div>
    </div>
    <div class="mb-3">
        <hr>
        <div class="row">
            <div class="col-12">
                <table class="table table-sm activate-select dt-responsive nowrap w-100" id="invoiceTable">
                    <thead>
                        <tr>
                            <th width="2%"><input id="check_all" class="form-check-input" type="checkbox" /></th>
                            <th width="10%">Item No</th>
                            <th width="10%">Imagen</th>
                            <th width="30%">Item Name</th>
                            <th width="15%">Price</th>
                            <th width="10%">Quantity</th>
                            <th width="20%">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data_invoicesDetails)): ?>
                            <?php foreach ($data_invoicesDetails as $key => $detail): ?>
                                <tr>
                                    <td><input class="form-check-input case" type="checkbox" /></td>
                                    <td><input type="text" data-type="productCode" name="product_id[]"
                                            id="itemNo_<?= $key + 1 ?>" class="form-control autocomplete_txt"
                                            value="<?= $detail['product_id'] ?>" autocomplete="off"></td>
                                    <td>
                                        <img src="<?= base_url('uploads/products/' . $detail['product_image']) ?>"
                                            alt="Product Image" width="50" height="50" id="itemImage_<?= $key + 1 ?>">
                                        <input type="hidden" name="product_image[]" value="<?= $detail['product_image'] ?>">
                                    </td>

                                    <td><input type="text" data-type="productName" name="product_name[]"
                                            id="itemName_<?= $key + 1 ?>" class="form-control autocomplete_txt"
                                            value="<?= $detail['product_name'] ?>" autocomplete="off"></td>
                                    <td><input type="number" step="0.01" name="price[]" id="price_<?= $key + 1 ?>"
                                            class="form-control changesNo" value="<?= $detail['price'] ?>" autocomplete="off">
                                    </td>
                                    <td><input type="number" name="quantity[]" id="quantity_<?= $key + 1 ?>"
                                            class="form-control changesNo" value="<?= $detail['quantity'] ?>"
                                            autocomplete="off"></td>
                                    <td><input type="number" step="0.01" name="data[<?= $key + 1 ?>][total]"
                                            id="total_<?= $key + 1 ?>" class="form-control totalLinePrice"
                                            value="<?= $detail['price'] * $detail['quantity'] ?>" autocomplete="off"></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td><input class="form-check-input case" type="checkbox" /></td>
                                <td><input type="text" data-type="productCode" name="product_id[]" id="itemNo_1"
                                        class="form-control autocomplete_txt" autocomplete="off"></td>
                                <td><img src="" alt="Product Image" width="50" height="50" id="itemImage_1">
                                    <input type="hidden" name="product_image[]" id="productImage_1">
                                </td>
                                <td><input type="text" data-type="productName" name="product_name[]" id="itemName_1"
                                        class="form-control autocomplete_txt" autocomplete="off"></td>
                                <td><input type="number" step="0.01" name="price[]" id="price_1"
                                        class="form-control changesNo" autocomplete="off"></td>
                                <td><input type="number" name="quantity[]" id="quantity_1" class="form-control changesNo"
                                        autocomplete="off"></td>
                                <td><input type="number" step="0.01" name="data[0][total]" id="total_1"
                                        class="form-control totalLinePrice" autocomplete="off"></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row ustify-content-between">
            <div class="col-12 col-sm-8 ">
                <button id="delete" class="btn btn-danger delete" type="button">- Delete</button>
                <button id="addmore" class="btn btn-success addmore" type="button">+ Add More</button>
                <br>
                <br>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notas</label>
                    <textarea class="form-control" rows="10" name="notes" id="notes"
                        placeholder="Your Notes"><?= !empty($data_invoices['notes']) ? $data_invoices['notes'] : '' ?></textarea>
                </div>
            </div>

            <div class="col-sm-3 offset-md-1">
                <div class="mb-3">
                    <label for="subTotal">Subtotal: &nbsp;</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" readonly name="invoice_subtotal"
                            id="subTotal" placeholder="Subtotal" onkeypress="return IsNumeric(event);"
                            ondrop="return false;" onpaste="return false;">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="tax">Tax: &nbsp;</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" name="tax_percent" id="tax"
                            placeholder="Tax" value="<?= setting('App.vat'); ?>" onkeypress="return IsNumeric(event);"
                            ondrop="return false;" onpaste="return false;">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="taxAmount">Tax Amount: &nbsp;</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" name="tax" id="taxAmount"
                            placeholder="Tax Amount" readonly onkeypress="return IsNumeric(event);"
                            ondrop="return false;" onpaste="return false;">
                        <span class="input-group-text">%</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="totalAftertax">Total: &nbsp;</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" name="invoice_total" id="totalAftertax"
                            placeholder="Total" readonly onkeypress="return IsNumeric(event);" ondrop="return false;"
                            onpaste="return false;">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="amountPaid">Amount Paid: &nbsp;</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" name="amount_paid" id="amountPaid"
                            value="<?= !empty($data_invoices['amount_paid']) ? $data_invoices['amount_paid'] : '' ?>"
                            placeholder="Amount Paid" onkeypress="return IsNumeric(event);" ondrop="return false;"
                            onpaste="return false;">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="amountDue">Amount Due: &nbsp;</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control amountDue" name="amount_due" id="amountDue"
                            placeholder="Amount Due" readonly onkeypress="return IsNumeric(event);"
                            ondrop="return false;" onpaste="return false;">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <label for="error"></label>
        </div>
</form>

<script>
    // Agregar más filas
    var i = $('table#invoiceTable tr').length; // Obtiene el número de filas actuales

    $("#addmore").on('click', function() {
        html = '<tr>';
        html += '<td><input class="case" type="checkbox"/></td>';
        html += '<td><input type="text" data-type="productCode" name="product_id[]" id="itemNo_' + i +
            '" class="form-control autocomplete_txt" autocomplete="off"></td>';
        html += '<td><img src="" alt="Product Image" width="50" height="50" id="itemImage_' + i +
            '"><input type="hidden" name="product_image[]" id="productImage_' + i +
            '" class="form-control autocomplete_txt" autocomplete="off"></td>';

        html += '<td><input type="text" data-type="productName" name="product_name[]" id="itemName_' + i +
            '" class="form-control autocomplete_txt" autocomplete="off"></td>';
        html += '<td><input type="number" step="0.01" name="price[]" id="price_' + i +
            '" class="form-control changesNo" autocomplete="off"></td>';
        html += '<td><input type="number" name="quantity[]" id="quantity_' + i +
            '" class="form-control changesNo" autocomplete="off"></td>';
        html += '<td><input type="number" step="0.01" name="data[' + i + '][total]" id="total_' + i +
            '" class="form-control totalLinePrice" autocomplete="off"></td>';
        html += '</tr>';
        $('table#invoiceTable').append(html);
        setDefaultImage(i);
        i++;
    });

    //Seleccionar todas las casillas
    $(document).on('change', '#check_all', function() {
        $('input[class=case]:checkbox').prop("checked", $(this).is(':checked'));
    });

    // Eliminar filas seleccionadas
    $("#delete").on('click', function() {
        $('.case:checkbox:checked').parents("tr").remove();
        $('#check_all').prop("checked", false);
        calculateTotal();
    });

    // Función para establecer la imagen por defecto
    function setDefaultImage(rowId) {
        var imageSrc = $('#itemImage_' + rowId).attr('src');
        if (!imageSrc || imageSrc.trim() === '') {
            $('#itemImage_' + rowId).attr('src', '<?= base_url('uploads/products/default_image.jpg') ?>');
        }
    }

    // Autocomplete script
    $(document).on('focus', '.autocomplete_txt', function() {
        var type = $(this).data('type');

        $(this).autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '<?= base_url('admin/products/autocomplete') ?>', // Ruta a tu controlador
                    dataType: "json",
                    method: 'post',
                    data: {
                        name_startsWith: request.term,
                        type: type
                    },
                    success: function(data) {
                        response($.map(data, function(item) {
                            var code = item.split("|");
                            return {
                                label: code[0] + " - " + code[
                                    2], // Mostrar código y nombre del producto
                                value: type === 'productCode' ? code[0] : code[
                                    2], // Valor según el tipo de campo
                                data: item
                            };
                        }));
                    }
                });
            },
            autoFocus: true,
            minLength: 0,
            appendTo: ".modal-form",
            select: function(event, ui) {
                var names = ui.item.data.split("|");
                var id_arr = $(this).attr('id');
                var id = id_arr.split("_");

                // Asignar los valores a los campos correspondientes
                $('#itemNo_' + id[1]).val(names[0]); // Código del producto
                $('#productImage_' + id[1]).val(names[1]); // Nombre de la imagen

                // Establecer la imagen del producto o la imagen por defecto
                var imagePath = names[1] ? '<?= base_url('uploads/products/') ?>' + names[1] :
                    '<?= base_url('uploads/products/default_image.jpg') ?>';
                $('#itemImage_' + id[1]).attr('src', imagePath);
                $('#itemName_' + id[1]).val(names[2]); // Nombre del producto
                $('#quantity_' + id[1]).val(1); // Cantidad por defecto
                $('#price_' + id[1]).val(names[3]); // Precio del producto
                $('#total_' + id[1]).val((1 * parseFloat(names[3])).toFixed(2)); // Calcular total

                calculateTotal(); // Recalcular el total
            },
            change: function(event, ui) {
                // Si no se selecciona nada, establecer la imagen por defecto
                if (!ui.item) {
                    var id_arr = $(this).attr('id');
                    var id = id_arr.split("_");
                    $('#itemImage_' + id[1]).attr('src',
                        '<?= base_url('uploads/products/default_image.jpg') ?>');
                    $('#productImage_' + id[1]).val('');
                }
            }
        });
    });

    // Llamar a setDefaultImage para todas las filas existentes al cargar la página
    $(document).ready(function() {
        $('table#invoiceTable tbody tr').each(function(index) {
            setDefaultImage(index + 1);
        });
    });


    // Calcular total
    $(document).on('change keyup blur', '.changesNo', function() {
        id_arr = $(this).attr('id');
        id = id_arr.split("_");
        quantity = $('#quantity_' + id[1]).val();
        price = $('#price_' + id[1]).val();
        if (quantity != '' && price != '') $('#total_' + id[1]).val((parseFloat(price) * parseFloat(quantity))
            .toFixed(2));
        calculateTotal();
    });

    $(document).on('change keyup blur', '#tax', function() {
        calculateTotal();
    });

    //Cálculo total
    $(document).ready(function() {
        calculateTotal(); // Cálculo inicial al cargar la página
        calculateAmountDue(); // Calcula el monto debido también al cargar la página
    });

    function calculateTotal() {
        let subTotal = 0;
        let total = 0;
        $('.totalLinePrice').each(function() {
            if ($(this).val() != '') {
                subTotal += parseFloat($(this).val());
            }
        });
        $('#subTotal').val(subTotal.toFixed(2));

        let tax = $('#tax').val();
        let taxAmount = 0;
        if (tax != '' && typeof(tax) !== "undefined") {
            taxAmount = subTotal * (parseFloat(tax) / 100);
            $('#taxAmount').val(taxAmount.toFixed(2));
            total = subTotal + taxAmount;
        } else {
            $('#taxAmount').val(0);
            total = subTotal;
        }

        $('#totalAftertax').val(total.toFixed(2));

        calculateAmountDue(); // Calcula el monto debido
    }

    function calculateAmountDue() {
        let amountPaid = $('#amountPaid').val();
        let total = $('#totalAftertax').val();
        let amountDue = 0;

        if (amountPaid != '' && typeof(amountPaid) !== "undefined") {
            amountDue = parseFloat(total) - parseFloat(amountPaid);
            $('.amountDue').val(amountDue.toFixed(2));
        } else {
            total = parseFloat(total).toFixed(2);
            $('.amountDue').val(total);
        }
    }

    function IsNumeric(e) {
        var keyCode = e.which ? e.which : e.keyCode;
        var ret = ((keyCode >= 48 && keyCode <= 57) || [8, 46].indexOf(keyCode) != -1);
        return ret;
    }

    //Datepicker
    $(function() {
        $('#invoiceDate').datepicker({});
    });

    $(document).ready(function() {
        if (typeof errorFlag !== 'undefined') {
            $('.message_div').delay(5000).slideUp();
        }

        //Cargar los detalles del propietario al cargar la página
        loadCustomerDetailsOnPageLoad();

        // Cargar detalles del propietario cuando se selecciona un cliente
        $('#clientSelect').on('change', function() {
            var clientId = $(this).val();
            if (clientId) {
                loadCustomerDetails(clientId);
            } else {
                $('#customerDetails').html('<p>No se ha seleccionado ningún propietario.</p>');
            }
        });

        function loadCustomerDetailsOnPageLoad() {
            var clientId = $('#clientSelect').val();
            if (clientId) {
                loadCustomerDetails(clientId);
            }
        }

        function loadCustomerDetails(clientId) {
            $.ajax({
                url: '<?= base_url('admin/invoices/getCustomerDetails') ?>',
                type: 'POST',
                data: {
                    client_id: clientId
                },
                dataType: 'json',
                success: function(data) {
                    $('#customerDetails').html(`
                    <table class="table table-sm">
                        <tr>
                            <th>IC</th>
                            <td>${data.ic}</td>
                        </tr>
                        <tr>
                            <th>Nombre</th>
                            <td>${data.first_name} ${data.last_name}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>${data.email}</td>
                        </tr>
                        <tr>
                            <th>Dirección</th>
                            <td>${data.address}</td>
                        </tr>
                        <tr>
                            <th>Teléfono</th>
                            <td>${data.phone}</td>
                        </tr>
                    </table>
                `);
                },
                error: function(xhr, status, error) {
                    console.error("Error al obtener los detalles del propietario:", error);
                    $('#customerDetails').html('<p>Error al obtener los detalles del propietario.</p>');
                }
            });
        }
    });
</script>