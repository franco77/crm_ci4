<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>
<div class="row">
    <div class="col-md-12">
        <div class="card custom-card">
            <div class="card-header">
                <h5 class="card-title mb-0">Right Aligned Nav</h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-pills nav-style-3 mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#payments" role="tab"
                            aria-controls="payments" aria-selected="true">
                            Pagos
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="about-tab" data-bs-toggle="tab" href="#totalPay" role="tab"
                            aria-controls="totalPay" aria-selected="false">
                            Total Pagos
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <!-- Home Tab -->
                    <div class="tab-pane fade show active text-muted" id="payments" role="tabpanel"
                        aria-labelledby="home-tab">
                        <div class="d-flex justify-content-between mb-3">
                            <button class="btn btn-sm btn-primary refresh" data-purpose="add">Refrescar</button>
                        </div>
                        <table id="datatable" class="table table-sm activate-select dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="check-items"></th>
                                    <th># Factura</th>
                                    <th>Mono Pago</th>
                                    <th>Fecha Pago</th>
                                    <th>Referencia</th>
                                    <th>Generado Por</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- About Tab -->
                    <div class="tab-pane fade text-muted" id="totalPay" role="tabpanel" aria-labelledby="about-tab">
                        <table id="totals-table" class="table table-sm activate-select">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="check-items"></th>
                                    <th># Factura</th>
                                    <th>Total Pagado</th>
                                    <th># Pagos</th>
                                    <th>Último Pago</th>
                                    <th>Pagado Por</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal modal-form fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

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
    const host = "<?= $host ?>";
    const datatable = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        scrollY: '45vh',
        responsive: true,
        orderCellsTop: true,
        destroy: true,
        stateSave: false,
        ajax: {
            url: host + "data",
            type: "POST"
        },
        "columns": [{
                "data": "column_bulk",
                "searchable": false,
                "orderable": false
            },
            {
                data: "invoice_id",
                render: function(data, type, row) {
                    return `FAC-${data}`;
                }
            },
            {
                "data": "amount_paid"
            },
            {
                "data": "payment_date"
            },
            {
                "data": "payment_reference"
            },
            {
                "data": "paid_by"
            },
            {
                "data": "column_action",
                "searchable": false,
                "orderable": false
            }
        ],
        "order": [
            [1, "DESC"]
        ]
    });

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr("href"); // La pestaña activa

        if (target === "#totalPay" && !$.fn.DataTable.isDataTable("#totals-table")) {

            const totalsTable = $('#totals-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                scrollY: '45vh',
                responsive: false,
                orderCellsTop: true,
                destroy: true,
                stateSave: false,
                ajax: {
                    url: host + "invoiceTotals",
                    type: "POST"
                },
                columns: [{
                        data: "column_bulk",
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: "invoice_id",
                        render: function(data, type, row) {
                            return `FAC-${data}`;
                        }
                    },

                    {
                        data: "total_amount"
                    },
                    {
                        data: "payment_count"
                    },
                    {
                        data: "last_payment_date",

                    },
                    {
                        data: "paid_by_users"
                    },
                    {
                        data: "column_action",
                        searchable: false,
                        orderable: false
                    }
                ],
                order: [
                    [1, "DESC"]
                ]
            });
        }
    });

    function refreshTable() {
        datatable.ajax.reload();
    }

    function ajaxRequest(url, data, successMessage, errorMessage) {
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
        }).done(function() {
            refreshTable();
            $('.modal-form').modal('hide');
            toastr.success(successMessage);
        }).fail(function(res) {
            $('.form-text').remove();
            $('.is-invalid').removeClass('is-invalid');
            const errors = jQuery.parseJSON(res.responseText);
            $.each(errors.messages, function(selector, value) {
                $('[for="' + selector + '"]').after(
                    '<small class="form-text text-danger">' + value + '</small>'
                );
                $('[name="' + selector + '"]').addClass('is-invalid');
            });
            toastr.error(errorMessage);
        });
    }

    function deleteItems(ids) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, ¡eliminar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const requests = ids.map(id => $.ajax({
                    url: host + 'delete/' + id,
                    type: 'POST',
                    dataType: 'json'
                }));

                $.when.apply($, requests).done(() => {
                    refreshTable();
                    Swal.fire(
                        '¡Eliminado!',
                        'Los registros han sido eliminados.',
                        'success'
                    );
                }).fail((jqXHR, textStatus, errorThrown) => {
                    console.error('Error al eliminar los registros:', textStatus,
                        errorThrown);
                    Swal.fire(
                        'Error',
                        'Hubo un problema al eliminar los registros. Inténtalo de nuevo.',
                        'error'
                    );
                });
            }
        });
    }


    datatable.on('draw', function() {
        $('.form-action').on('click', function() {
            const button = $(this);
            const modalForm = $('.modal-form');
            const itemId = button.attr('item-id');
            const purpose = button.attr('purpose');

            let title, url, submitUrl;
            if (purpose === "add") {
                title = "Add Data";
                url = host + 'new';
                submitUrl = host + 'create';
            } else if (purpose === "edit") {
                title = "Edit Data";
                url = host + 'edit/' + itemId;
                submitUrl = host + 'update/' + itemId;
            } else {
                title = "Detail Data";
                url = host + 'show/' + itemId;
            }

            $.ajax({
                type: "GET",
                url: url
            }).done(function(response) {
                modalForm.find('.modal-title').text(title);
                modalForm.find('.modal-body').html(response);
                modalForm.modal('show');
                initializePlugins();

                $('#form input:text, #form textarea').first().focus();
                $('#form').on('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    ajaxRequest(submitUrl, formData,
                        'Registro guardado con éxito',
                        'Error al guardar el registro');
                });
            }).fail(function() {
                alert("Data not found");
            });
        });
    });

    $('.refresh').on('click', refreshTable);

    $('.check-items').on('click', function() {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    $('.bulk-delete').on('click', function() {
        const ids = $(".bulk-item:checked").map(function() {
            return $(this).val();
        }).get();

        if (ids.length) {
            deleteItems(ids);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Nada Seleccionado',
                text: 'Por favor selecciona algún registro para borrar!'
            });
        }
    });

    function initializePlugins() {

        $('.select2').select2({
            dropdownParent: $("#form")
        });
    }

});
</script>
<?= $this->endSection() ?>