<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sección de Selección de Usuario y Wallet -->
        <div class="col-12 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-wallet me-2"></i>Gestión de Wallet
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="user_select" class="form-label fw-bold">Seleccionar Usuario:</label>
                        <select id="user_select" class="form-select form-select-lg">
                            <option value="">Seleccione un usuario</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user->id ?>">
                                    <?= esc($user->first_name) ?> - <?= esc($user->email) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="wallet_select" class="form-label fw-bold">Seleccionar Wallet:</label>
                        <select id="wallet_select" class="form-select form-select-lg" disabled>
                            <option value="">Primero seleccione un usuario</option>
                        </select>
                    </div>

                    <!-- Información del Wallet -->
                    <div id="wallet_info" class="d-none">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3 text-muted">Detalles del Wallet</h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="p-3 bg-white rounded shadow-sm">
                                            <small class="text-muted d-block">Monto Total</small>
                                            <span id="total_amount" class="h5 mb-0 d-block">$0.00</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-3 bg-white rounded shadow-sm">
                                            <small class="text-muted d-block">Monto Disponible</small>
                                            <span id="available_amount"
                                                class="h5 mb-0 d-block text-success">$0.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <p class="mb-1">
                                        <strong>Fecha de Depósito:</strong>
                                        <span id="deposit_date" class="text-muted">-</span>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Método:</strong>
                                        <span id="payment_method" class="text-muted">-</span>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Referencia:</strong>
                                        <span id="reference" class="text-muted">-</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Pagos -->
        <div class="col-12 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Realizar Pago
                    </h5>
                </div>
                <div class="card-body">
                    <div id="invoice_alert" class="alert alert-dismissible fade mt-3 d-none" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <div id="invoice_alert_message"></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <form id="payment_form">
                        <div class="mb-3">
                            <label for="invoice_id" class="form-label fw-bold">Número de Factura:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-file-earmark-text"></i></span>
                                <input type="number" id="invoice_id" class="form-control form-control-lg"
                                    placeholder="Ingrese el número de factura" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="form-label fw-bold">Monto a Pagar:</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="amount" class="form-control form-control-lg" step="0.01"
                                    placeholder="0.00" required>
                            </div>
                            <small id="amount_warning" class="text-danger d-none">
                                El monto excede el saldo disponible
                            </small>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100" id="submit_payment" disabled>
                            <i class="fas fa-check-circle me-2"></i>Procesar Pago
                        </button>
                    </form>

                    <!-- Historial de Pagos -->
                    <div id="payment_history" class="mt-4 d-none">
                        <h6 class="border-bottom pb-2">Últimos Pagos</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Factura</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="payment_history_body">
                                    <!-- Se llenará dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="invoice_details" class="card mt-3 d-none">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Detalles de la Factura</h6>
                <div class="row">
                    <div class="col-3">
                        <small>#</small>
                        <div id="invoice_uuid" class="h6"></div>
                    </div>
                    <div class="col-3">
                        <small>Total</small>
                        <div id="invoice_total" class="h6"></div>
                    </div>
                    <div class="col-3">
                        <small>Pagado</small>
                        <div id="invoice_paid" class="h6"></div>
                    </div>
                    <div class="col-3">
                        <small>Pendiente</small>
                        <div id="invoice_due" class="h6"></div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <!-- Modales -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Pago Exitoso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                    <h5 class="mb-2">¡Pago Procesado!</h5>
                    <p class="mb-0" id="success_message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<!-- /.content -->

<!-- / Incluir la libreria toastr  -->
<!-- page script -->
<?= $this->section("js") ?>
<!-- Primero, añade SweetAlert2 en el head de tu layout -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {

        function showBootstrapAlert(message, type = 'warning') {
            const $alert = $('#invoice_alert');
            const $message = $('#invoice_alert_message');

            // Remover todas las clases de tipo
            $alert.removeClass('alert-warning alert-danger alert-success alert-info');

            // Agregar la clase correspondiente al tipo
            $alert.addClass(`alert-${type}`);

            // Actualizar el mensaje
            $message.text(message);

            // Mostrar el alert
            $alert.removeClass('d-none').addClass('show');

            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                $alert.alert('close');
            }, 5000);
        }

        // Funciones de utilidad para SweetAlert
        function showLoading(message = 'Procesando...', duration = 2000) {
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    setTimeout(() => {
                        Swal.close();
                    }, duration);
                }
            });
        }

        function showSuccess(message, details = '') {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: message,
                html: details ? `<div class="text-left">${details}</div>` : '',
                confirmButtonColor: '#28a745'
            });
        }

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonColor: '#dc3545'
            });
        }

        // Cargar wallets cuando cambia el usuario seleccionado
        $('#user_select').change(function() {
            const userId = $(this).val();
            const $walletSelect = $('#wallet_select');

            if (!userId) {
                $walletSelect.prop('disabled', true).html(
                    '<option value="">Primero seleccione un usuario</option>');
                $('#wallet_info').addClass('d-none');
                return;
            }

            showLoading('Cargando wallets...');

            $.ajax({
                url: '<?= base_url('admin/wallets/getUserWallets') ?>',
                method: 'GET',
                data: {
                    user_id: userId
                },
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        $walletSelect.prop('disabled', false);
                        $walletSelect.html('<option value="">Seleccione un wallet</option>');

                        // Recargar los datos del wallet en el `select` de wallets
                        response.wallets.forEach(wallet => {
                            $walletSelect.append(`
                        <option value="${wallet.id}" 
                                data-amount="${wallet.amount}"
                                data-remaining="${wallet.remaining_amount}"
                                data-date="${wallet.deposit_date}"
                                data-payment-method="${wallet.payment_method}"
                                data-reference="${wallet.reference}">
                            Wallet #${wallet.id} - $${wallet.remaining_amount} disponible
                        </option>
                    `);
                        });
                    } else {
                        showError('Error al cargar los wallets');
                    }
                },
                error: function() {
                    Swal.close();
                    showError('Error al comunicarse con el servidor');
                }
            });
        });

        // Validar y procesar el pago al enviar el formulario
        $('#payment_form').submit(function(e) {
            e.preventDefault();

            const walletId = $('#wallet_select').val();
            const invoiceId = $('#invoice_id').val();
            const amount = parseFloat($('#amount').val());
            const availableAmount = parseFloat($('#wallet_select option:selected').data('remaining'));

            if (amount > availableAmount) {
                showError('El monto a pagar excede el saldo disponible en el wallet');
                return;
            }

            Swal.fire({
                title: '¿Confirmar Pago?',
                html: `
                    <div class="text-left">
                        <p><strong>Monto:</strong> $${amount}</p>
                        <p><strong>Factura:</strong> #${invoiceId}</p>
                        <p><strong>Wallet:</strong> #${walletId}</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Sí, Procesar Pago',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    procesarPago(walletId, invoiceId, amount);
                }
            });
        });

        function procesarPago(walletId, invoiceId, amount) {
            showLoading('Procesando pago...');

            $.ajax({
                url: '<?= base_url('admin/wallets/makePayment') ?>',
                method: 'POST',
                data: {
                    wallet_id: walletId,
                    invoice_id: invoiceId,
                    amount: amount
                },
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        const newWalletBalance = response.wallet_balance !== undefined ? response
                            .wallet_balance : 0;

                        showSuccess('Pago Procesado Exitosamente', `
                    <div class="text-left">
                        <p><strong>Monto pagado:</strong> $${amount}</p>
                        <p><strong>Nuevo saldo en wallet:</strong> $${newWalletBalance}</p>
                        <p><strong>Saldo pendiente factura:</strong> $${response.invoice.amount_due}</p>
                    </div>
                `);

                        // Limpiar el formulario y recargar el select de wallets para obtener datos actualizados
                        $('#payment_form')[0].reset();
                        $('#invoice_details').addClass('d-none');

                        // Recargar el select de wallets con datos actualizados
                        reloadWalletSelect(true); // Llamada con true para forzar recarga
                    } else {
                        showError(response.message || 'Error al procesar el pago');
                    }
                },
                error: function() {
                    Swal.close();
                    showError('Error al procesar el pago');
                }
            });
        }


        function reloadWalletSelect(forceReload = false) {
            const userId = $('#user_select').val();
            const $walletSelect = $('#wallet_select');

            if (!userId) {
                $walletSelect.prop('disabled', true).html(
                    '<option value="">Primero seleccione un usuario</option>');
                $('#wallet_info').addClass('d-none');
                return;
            }

            if (forceReload) {
                $walletSelect.empty(); // Elimina las opciones anteriores para forzar la recarga
            }

            showLoading('Cargando wallets...');

            $.ajax({
                url: '<?= base_url('admin/wallets/getUserWallets') ?>',
                method: 'GET',
                data: {
                    user_id: userId
                },
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        $walletSelect.prop('disabled', false);
                        $walletSelect.html('<option value="">Seleccione un wallet</option>');

                        // Agregar wallets actualizados
                        response.wallets.forEach(wallet => {
                            $walletSelect.append(`
                        <option value="${wallet.id}" 
                                data-amount="${wallet.amount}"
                                data-remaining="${wallet.remaining_amount}"
                                data-date="${wallet.deposit_date}"
                                data-payment-method="${wallet.payment_method}"
                                data-reference="${wallet.reference}">
                            Wallet #${wallet.id} - $${wallet.remaining_amount} disponible
                        </option>
                    `);
                        });

                        // Actualizar la información de la primera opción si existe
                        if (response.wallets.length > 0) {
                            const firstWallet = response.wallets[0];
                            $walletSelect.val(firstWallet.id);
                            updateWalletInfoDisplay(firstWallet);
                        }

                        validateForm();
                    } else {
                        showError('Error al cargar los wallets');
                    }
                },
                error: function() {
                    Swal.close();
                    showError('Error al comunicarse con el servidor');
                }
            });
        }



        function updateWalletInfoDisplay(wallet) {
            $('#available_amount').text(`$${wallet.remaining_amount}`);
            $('#deposit_date').text(wallet.deposit_date);
            $('#payment_method').text(wallet.payment_method || '-');
            $('#reference').text(wallet.reference || '-');
            $('#wallet_info').removeClass('d-none');
        }

        function updateWalletDisplay(newBalance) {
            const $selectedOption = $('#wallet_select option:selected');

            // Solo actualizar si `newBalance` es un número válido
            if (typeof newBalance === 'number' && !isNaN(newBalance)) {
                $selectedOption.data('remaining', newBalance);
                $selectedOption.text(`Wallet #${$selectedOption.val()} - $${newBalance} disponible`);
                $('#available_amount').text(`$${newBalance}`);
            } else {
                console.error("Saldo del wallet inválido:", newBalance);
                showError("Error: Saldo del wallet no válido recibido del servidor.");
            }
        }

        function validateForm() {
            const walletId = $('#wallet_select').val();
            const invoiceId = $('#invoice_id').val();
            const amount = parseFloat($('#amount').val());
            const availableAmount = parseFloat($('#wallet_select option:selected').data('remaining') || 0);
            const maxDue = parseFloat($('#amount').attr('max') || 0);

            let errorMessage = '';
            if (!walletId || !invoiceId || !amount) {
                $('#submit_payment').prop('disabled', true);
                return false;
            } else if (amount <= 0) {
                errorMessage = 'El monto debe ser mayor a 0';
            } else if (maxDue && amount > maxDue) {
                errorMessage = 'El monto excede el saldo pendiente de la factura';
            } else if (amount > availableAmount) {
                errorMessage = 'El monto excede el saldo disponible en el wallet';
            }

            $('#amount_warning').text(errorMessage).toggleClass('d-none', !errorMessage);
            $('#submit_payment').prop('disabled', !!errorMessage);
            return !errorMessage;
        }

        function checkInvoice(invoiceId) {
            showLoading('Verificando factura...');

            $.ajax({
                url: '<?= base_url('admin/wallets/getInvoiceDetails') ?>',
                method: 'GET',
                data: {
                    invoice_id: invoiceId
                },
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        $('#invoice_uuid').text(response.invoice.uuid);
                        $('#amount').attr('max', response.invoice.amount_due);
                        $('#invoice_details').removeClass('d-none');
                        $('#invoice_total').text('$' + response.invoice.invoice_total);
                        $('#invoice_paid').text('$' + response.invoice.amount_paid);
                        $('#invoice_due').text('$' + response.invoice.amount_due);

                        if (response.invoice.amount_due > 0) {
                            $('#invoice_alert').addClass('d-none');
                            $('#amount').prop('disabled', false);
                            validateForm();
                        } else {
                            showBootstrapAlert('Esta factura ya está pagada en su totalidad',
                                'warning');
                            $('#amount').prop('disabled', true);
                            $('#submit_payment').prop('disabled', true);
                        }
                    } else {
                        showBootstrapAlert(response.message || 'Error al verificar la factura',
                            'danger');
                        $('#submit_payment').prop('disabled', true);
                        $('#invoice_id').val('');
                    }
                },
                error: function() {
                    showBootstrapAlert('Error al verificar la factura', 'danger');
                    $('#submit_payment').prop('disabled', true);
                    $('#invoice_id').val('');
                }
            });
        }

        // Eventos que disparan la validación
        $('#wallet_select').change(function() {
            const selectedOption = $(this).find('option:selected');
            const walletData = {
                remaining_amount: selectedOption.data('remaining'),
                deposit_date: selectedOption.data('date'),
                payment_method: selectedOption.data('payment-method'),
                reference: selectedOption.data('reference')
            };
            updateWalletInfoDisplay(walletData);
            validateForm();
        });

        // Agregar esta función de utilidad para formatear números
        function formatNumber(number) {
            if (number === undefined || number === null) return '0.00';
            return parseFloat(number).toLocaleString('es-CO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
        $('#invoice_id').on('input', function() {
            clearTimeout(window.invoiceCheckTimeout);
            const invoiceId = $(this).val().trim();
            window.invoiceCheckTimeout = setTimeout(() => {
                if (invoiceId) {
                    checkInvoice(invoiceId);
                } else {
                    $('#submit_payment').prop('disabled', true);
                }
                validateForm();
            }, 1500);
        });

        $('#amount').on('input', validateForm);
    });
</script>

<?= $this->endSection() ?>