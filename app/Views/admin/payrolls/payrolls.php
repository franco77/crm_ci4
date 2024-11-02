<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>

<style>
#preloader {
    display: none;
    justify-content: center;
    align-items: center;
    margin-top: 2rem;
}

.progress-container {
    width: 100%;
    max-width: 1200%;
}

.progress-bar {
    width: 0%;
    height: 1.5rem;
    background-color: #007bff;
    color: white;
    text-align: center;
    line-height: 1.5rem;
    font-weight: bold;
    transition: width 0.1s ease-in-out;
}

.progress-bar-striped {
    background-image: linear-gradient(45deg,
            rgba(255, 255, 255, 0.15) 25%,
            transparent 25%,
            transparent 50%,
            rgba(255, 255, 255, 0.15) 50%,
            rgba(255, 255, 255, 0.15) 75%,
            transparent 75%,
            transparent);
    background-size: 1rem 1rem;
}

.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}

@keyframes progress-bar-stripes {
    0% {
        background-position: 1rem 0;
    }

    100% {
        background-position: 0 0;
    }
}

.total-payroll-box {
    background-color: #f8f9fa;
    border: 1px solid #ccc;
    border-radius: 1px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 10px;
    text-align: center;
}

#total-payroll {
    font-size: 1.2rem;
    color: #dd8615;
    font-weight: bold;
}

.fa-money-bill-wave {
    font-size: 1rem;
    margin-right: 10px;
}
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4"><?= $title; ?></h5>

                <div id="alert-container"></div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                    <button id="generate-payroll-btn" class="btn btn-primary"><i class='bx bx-cart-download'></i>
                        Generar Nomina</button>
                </div>

                <div id="preloader">
                    <div class="progress-container">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progress-bar"
                            role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            Progreso</div>
                    </div>
                </div>

                <div id="payroll-results-container" style="display: none; margin-top: 20px;">
                    <?php if (!empty($payrollResults)): ?>
                    <!-- Contenedor del formulario de nómina -->
                    <form id="payroll-form" action="<?= base_url('Payrolls/savePayroll') ?>" method="post">
                        <div class="table-responsive">
                            <table id="state-saving-datatable" class="table activate-select dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th scope="col">Selecciona</th>
                                        <th scope="col">Empleado</th>
                                        <th scope="col">Salario</th>

                                        <?php foreach ($payrollResults[0]['deductions'] as $deduction): ?>
                                        <th><?= esc($deduction['description']) ?></th>
                                        <?php endforeach; ?>

                                        <th scope="col">Prestamos</th>
                                        <th scope="col">Bono</th>
                                        <th scope="col">Salario Neto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payrollResults as $index => $result): ?>
                                    <tr>
                                        <td class="text-center">
                                            <input class="form-check-input" type="checkbox" name="selected_employees[]"
                                                value="<?= esc($result['employee_id']) ?>">
                                        </td>
                                        <td><?= esc($result['employee']) ?></td>
                                        <td><?= number_format($result['gross_salary'], 2) ?></td>

                                        <?php foreach ($result['deductions'] as $deduction): ?>
                                        <td><?= number_format($deduction['amount'], 2) ?></td>
                                        <?php endforeach; ?>

                                        <td><?= number_format($result['loan_deductions'], 2) ?></td>

                                        <td style="width: 10%;">
                                            <input type="number" class="form-control bonus-input"
                                                data-index="<?= $index ?>" name="bonus[<?= $result['employee_id'] ?>]"
                                                value="0" step="0.01">
                                        </td>

                                        <td id="net-salary-<?= $index ?>"><?= number_format($result['net_salary'], 2) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mostrar el total de la nómina -->
                        <div id="total-payroll-container"
                            class="total-payroll-box mt-2 p-1 rounded shadow-sm bg-light text-center">
                            <h5 class="text-primary mb-0">
                                <i class="fas fa-money-bill-wave"></i> Total Nomina:
                                <span id="total-payroll">$0.00</span>
                            </h5>
                        </div>

                        <input type="hidden" name="payrollResults" id="payrollResults"
                            value="<?= htmlspecialchars(json_encode($payrollResults), ENT_QUOTES, 'UTF-8') ?>">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-warning" role="alert">Hubo un error
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section("js") ?>
<script>
$(document).ready(function() {

    $('#generate-payroll-btn').on('click', function() {
        $('#payroll-results-container').hide();
        $('#preloader').show();

        setTimeout(function() {
            $('#preloader').hide();
            $('#payroll-results-container').show();
        }, 2000);
    });

    $('#payroll-form').on('submit', function(e) {
        e.preventDefault();
        $('#preloader').show();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#preloader').hide();
                if (response.success) {
                    showAlert('success', response.success);
                } else if (response.error) {
                    showAlert('danger', response.error);
                }
            },
            error: function() {
                $('#preloader').hide();
                showAlert('danger', 'Ocurrió un error al guardar la nómina.');
            }
        });
    });

    $('.bonus-input').on('input', function() {
        var index = $(this).data('index');
        var netSalaryBase = parseFloat(<?= json_encode(array_column($payrollResults, 'net_salary')) ?>[
            index]) || 0;
        var bonus = parseFloat($(this).val()) || 0;
        var netSalaryFinal = (netSalaryBase + bonus).toFixed(2);

        $('#net-salary-' + index).text(netSalaryFinal);
    });

    function showAlert(type, message) {
        var alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
        $('#alert-container').html(alertHtml);
    }

    function updateTotalPayroll() {
        var payrollResults = <?= json_encode($payrollResults) ?>;

        $('input.bonus-input').each(function() {
            var index = $(this).data('index');
            var bonus = parseFloat($(this).val()) || 0;
            payrollResults[index].bonus = bonus;
        });

        $.ajax({
            url: '<?= base_url("admin/Payrolls/calculateTotalPayroll") ?>',
            type: 'POST',
            data: {
                payrollResults: JSON.stringify(payrollResults)
            },
            success: function(response) {

                $('#total-payroll').text(response.totalPayroll);
            },
            error: function() {
                alert('Error al calcular el total de la nómina.');
            }
        });
    }

    updateTotalPayroll();

    $('.bonus-input').on('input', function() {
        var index = $(this).data('index');
        var netSalaryBase = parseFloat(<?= json_encode(array_column($payrollResults, 'net_salary')) ?>[
            index]) || 0;
        var bonus = parseFloat($(this).val()) || 0;
        var netSalaryFinal = (netSalaryBase + bonus).toFixed(2);

        $('#net-salary-' + index).text(netSalaryFinal);

        updateTotalPayroll();
    });

});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var progressBar = document.getElementById("progress-bar");
    var progress = 0;

    function updateProgress() {
        if (progress < 100) {
            progress++;
            progressBar.style.width = progress + '%';
            progressBar.setAttribute('aria-valuenow', progress);
            requestAnimationFrame(updateProgress);
        }
    }

    requestAnimationFrame(updateProgress);
});
</script>

<?= $this->endSection() ?>