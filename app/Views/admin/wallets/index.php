<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>
<div class="container-fluid py-4">
    <!-- Main Content Row -->
    <div class="row">
        <!-- Left Column: User & Wallet Selection -->
        <div class="col-12 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-wallet me-2"></i>Gestión de Wallet
                    </h5>
                </div>
                <div class="card-body">
                    <!-- User Selection -->
                    <div class="mb-4">
                        <label for="user_select" class="form-label fw-semibold">
                            <i class="fas fa-user me-1"></i>Seleccionar Usuario:
                        </label>
                        <select id="user_select" class="form-select form-select-lg" required>
                            <option value="">Seleccione un usuario</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= esc($user->id) ?>">
                                    <?= esc($user->first_name . ' ' . $user->last_name) ?> - <?= esc($user->email) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un usuario</div>
                    </div>

                    <!-- Wallet Selection -->
                    <div class="mb-4">
                        <label for="wallet_select" class="form-label fw-semibold">
                            <i class="fas fa-credit-card me-1"></i>Seleccionar Wallet:
                        </label>
                        <select id="wallet_select" class="form-select form-select-lg" disabled required>
                            <option value="">Primero seleccione un usuario</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un wallet</div>
                    </div>

                    <!-- Wallet Information Card -->
                    <div id="wallet_info" class="d-none">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3 text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Detalles del Wallet
                                </h6>
                                <div class="row g-3">
                                    <!-- Total Amount Card -->
                                    <div class="col-6">
                                        <div class="p-3 bg-white rounded shadow-sm h-100">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-money-bill-wave me-1"></i>Monto Total
                                            </small>
                                            <span id="total_amount" class="h5 mb-0 d-block fw-bold">$0.00</span>
                                        </div>
                                    </div>
                                    <!-- Available Amount Card -->
                                    <div class="col-6">
                                        <div class="p-3 bg-white rounded shadow-sm h-100">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-wallet me-1"></i>Monto Disponible
                                            </small>
                                            <span id="available_amount"
                                                class="h5 mb-0 d-block text-success fw-bold">$0.00</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Wallet Details -->
                                <div class="mt-4">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item bg-transparent px-0">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            <strong>Fecha de Depósito:</strong>
                                            <span id="deposit_date" class="text-muted ms-2">-</span>
                                        </li>
                                        <li class="list-group-item bg-transparent px-0">
                                            <i class="fas fa-money-check-alt me-2"></i>
                                            <strong>Método de Pago:</strong>
                                            <span id="payment_method" class="text-muted ms-2">-</span>
                                        </li>
                                        <li class="list-group-item bg-transparent px-0">
                                            <i class="fas fa-hashtag me-2"></i>
                                            <strong>Referencia:</strong>
                                            <span id="reference" class="text-muted ms-2">-</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Payment Processing -->
        <div class="col-12 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Realizar Pago
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Alert Container -->
                    <div id="invoice_alert" class="alert alert-dismissible fade mt-3 d-none" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <div id="invoice_alert_message"></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <!-- Payment Form -->
                    <form id="payment_form" class="needs-validation" novalidate>
                        <!-- Invoice Input -->
                        <div class="mb-4">
                            <label for="invoice_id" class="form-label fw-semibold">
                                Número de Factura:
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">
                                    <i class="bi bi-file-earmark-text"></i>
                                </span>
                                <input type="number" id="invoice_id" class="form-control form-control-lg"
                                    placeholder="Ingrese el número de factura" required min="1">
                                <div class="invalid-feedback">
                                    Por favor ingrese un número de factura válido
                                </div>
                            </div>
                        </div>

                        <!-- Amount Input -->
                        <div class="mb-4">
                            <label for="amount" class="form-label fw-semibold">
                                <i class="fas fa-dollar-sign me-1"></i>Monto a Pagar:
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">$</span>
                                <input type="number" id="amount" class="form-control form-control-lg" step="0.01"
                                    placeholder="0.00" required min="0.01">
                                <div class="invalid-feedback">
                                    Por favor ingrese un monto válido
                                </div>
                            </div>
                            <small id="amount_warning" class="text-danger d-none">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                El monto excede el saldo disponible
                            </small>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-success btn-lg w-100" id="submit_payment" disabled>
                            <i class="fas fa-check-circle me-2"></i>Procesar Pago
                        </button>
                    </form>

                    <!-- Invoice Details Card -->
                    <div id="invoice_details" class="card mt-4 border-0 bg-light d-none">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-3 text-muted">
                                <i class="fas fa-file-invoice me-1"></i>Detalles de la Factura
                            </h6>
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <div class="p-2 bg-white rounded shadow-sm text-center">
                                        <small class="text-muted d-block mb-1">Referencia</small>
                                        <div id="invoice_uuid" class="h6 mb-0 text-truncate"></div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 bg-white rounded shadow-sm text-center">
                                        <small class="text-muted d-block mb-1">Total</small>
                                        <div id="invoice_total" class="h6 mb-0 text-primary"></div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 bg-white rounded shadow-sm text-center">
                                        <small class="text-muted d-block mb-1">Pagado</small>
                                        <div id="invoice_paid" class="h6 mb-0 text-success"></div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 bg-white rounded shadow-sm text-center">
                                        <small class="text-muted d-block mb-1">Pendiente</small>
                                        <div id="invoice_due" class="h6 mb-0 text-danger"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Pago Exitoso
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success fa-4x"></i>
                </div>
                <h5 class="mb-3">¡Pago Procesado!</h5>
                <p class="mb-0" id="success_message"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section("js") ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize form validation
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

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
                        // Actualizar la interfaz con los nuevos valores
                        updateWalletDisplay(response.wallet_balance);
                        updateInvoiceDisplay(response.invoice);

                        showSuccess('Pago Procesado Exitosamente', `
                    <div class="text-start">
                        <p><strong>Monto pagado:</strong> $${formatNumber(amount)}</p>
                        <p><strong>Nuevo saldo en wallet:</strong> $${formatNumber(response.wallet_balance)}</p>
                        <p><strong>Saldo pendiente factura:</strong> $${formatNumber(response.invoice.amount_due)}</p>
                    </div>
                `);

                        // Limpiar formulario
                        $('#payment_form')[0].reset();

                        // Recargar los datos del wallet
                        const userId = $('#user_select').val();
                        refreshWalletData(userId, walletId);
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


        function updateInvoiceDisplay(invoice) {
            $('#invoice_total').text(`$${formatNumber(invoice.invoice_total)}`);
            $('#invoice_paid').text(`$${formatNumber(invoice.amount_paid)}`);
            $('#invoice_due').text(`$${formatNumber(invoice.amount_due)}`);
        }


        // Función para refrescar los datos del wallet
        function refreshWalletData(userId, walletId) {
            $.ajax({
                url: '<?= base_url('admin/wallets/getUserWallets') ?>',
                method: 'GET',
                data: {
                    user_id: userId
                },
                success: function(response) {
                    if (response.success) {
                        const $walletSelect = $('#wallet_select');
                        $walletSelect.html('<option value="">Seleccione un wallet</option>');

                        response.wallets.forEach(wallet => {
                            const option = `
                        <option value="${wallet.id}" 
                                data-amount="${wallet.amount}"
                                data-remaining="${wallet.remaining_amount}"
                                data-date="${wallet.deposit_date}"
                                data-payment-method="${wallet.payment_method}"
                                data-reference="${wallet.reference}">
                            Wallet #${wallet.id} - $${formatNumber(wallet.remaining_amount)} disponible
                        </option>
                    `;
                            $walletSelect.append(option);
                        });

                        // Seleccionar el wallet que estaba activo
                        if (walletId) {
                            $walletSelect.val(walletId);
                            const selectedWallet = response.wallets.find(w => w.id == walletId);
                            if (selectedWallet) {
                                updateWalletInfoDisplay(selectedWallet);
                            }
                        }
                    }
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



        // Función auxiliar para formatear números
        function formatNumber(number) {
            if (number === undefined || number === null) return '0.00';
            return parseFloat(number).toLocaleString('es-CO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Función para actualizar la información del wallet
        function updateWalletInfoDisplay(wallet) {
            $('#available_amount').text(`$${formatNumber(wallet.remaining_amount)}`);
            $('#total_amount').text(`$${formatNumber(wallet.amount)}`);
            $('#deposit_date').text(wallet.deposit_date || '-');
            $('#payment_method').text(wallet.payment_method || '-');
            $('#reference').text(wallet.reference || '-');
            $('#wallet_info').removeClass('d-none');
        }

        function updateWalletDisplay(newBalance) {
            const formattedBalance = formatNumber(newBalance);
            const $selectedOption = $('#wallet_select option:selected');
            const walletId = $selectedOption.val();

            // Actualizar el texto de la opción seleccionada
            $selectedOption.text(`Wallet #${walletId} - $${formattedBalance} disponible`);

            // Actualizar los datos del wallet
            $selectedOption.data('remaining', newBalance);

            // Actualizar la visualización
            $('#total_amount').text(`$${formattedBalance}`);
            $('#available_amount').text(`$${formattedBalance}`);
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