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
                                        <strong>Método de Pago:</strong>
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
                    <form id="payment_form">
                        <div class="mb-3">
                            <label for="invoice_id" class="form-label fw-bold">Número de Factura:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                                <input type="text" id="invoice_id" class="form-control form-control-lg"
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
<script>
    $(document).ready(function() {
        // Cargar wallets cuando se selecciona un usuario
        $('#user_select').change(function() {
            const userId = $(this).val();
            const $walletSelect = $('#wallet_select');

            if (!userId) {
                $walletSelect.prop('disabled', true).html(
                    '<option value="">Primero seleccione un usuario</option>');
                $('#wallet_info').addClass('d-none');
                return;
            }

            $.ajax({
                url: '<?= base_url('admin/wallets/getUserWallets') ?>',
                method: 'GET',
                data: {
                    user_id: userId
                },
                success: function(response) {
                    if (!response.success) {
                        alert('Error al cargar los wallets');
                        return;
                    }

                    $walletSelect.prop('disabled', false);
                    $walletSelect.html(
                        '<option value="">Seleccione un wallet</option>');

                    response.wallets.forEach(wallet => {
                        $walletSelect.append(`
                        <option value="${wallet.id}" 
                                data-amount="${wallet.amount}"
                                data-remaining="${wallet.remaining_amount}"
                                data-date="${wallet.deposit_date}">
                            Wallet #${wallet.id} - $${wallet.remaining_amount} disponible
                        </option>
                    `);
                    });
                },
                error: function() {
                    alert('Error al comunicarse con el servidor');
                }
            });
        });

        // Mostrar información del wallet seleccionado
        $('#wallet_select').change(function() {
            const $selectedOption = $(this).find('option:selected');
            const $walletInfo = $('#wallet_info');
            const $submitBtn = $('#submit_payment');

            if (!$selectedOption.val()) {
                $walletInfo.addClass('d-none');
                $submitBtn.prop('disabled', true);
                return;
            }

            $('#available_amount').text(`$${$selectedOption.data('remaining')}`);
            $('#deposit_date').text($selectedOption.data('date'));
            $walletInfo.removeClass('d-none');
            $submitBtn.prop('disabled', false);
        });

        // Procesar el pago
        $('#payment_form').submit(function(e) {
            e.preventDefault();

            const walletId = $('#wallet_select').val();
            const invoiceId = $('#invoice_id').val();
            const amount = parseFloat($('#amount').val());
            const availableAmount = parseFloat($('#wallet_select option:selected').data(
                'remaining'));

            if (amount > availableAmount) {
                alert('El monto a pagar excede el saldo disponible en el wallet');
                return;
            }

            $.ajax({
                url: '<?= base_url('admin/wallets/makePayment') ?>',
                method: 'POST',
                data: {
                    wallet_id: walletId,
                    invoice_id: invoiceId,
                    amount: amount
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        // Recargar los wallets para actualizar los montos
                        $('#user_select').trigger('change');
                        $('#payment_form')[0].reset();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error al procesar el pago');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>