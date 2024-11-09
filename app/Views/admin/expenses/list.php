<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><?= $title ?></div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <button class="btn btn-sm btn-danger bulk-delete">Eliminar</button>
                        <button class="btn btn-sm btn-primary refresh" purpose="add">Refrescar</button>
                    </div>
                    <button class="btn btn-sm btn-primary form-action" purpose="add">Agregar</button>
                </div>
                <table id="datatable" class="table table-sm activate-select dt-responsive nowrap w-100" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 0px"><input type="checkbox" class="check-items"></th>
                            <th>Titulo</th>
                            <th>Monto</th>
                            <th>Soporte</th>
                            <th>Creado El</th>
                            <th style="width: 0px">#</th>
                        </tr>
                    </thead>
                </table>
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
        responsive: false,
        orderCellsTop: true,
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
                "data": "title"
            },
            {
                "data": "amount"
            },
            {
                "data": "support"
            },
            {
                "data": "created_at"
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

    function refreshTable() {
        datatable.ajax.reload();
    }

    function ajaxRequest(url, data, successMessage, errorMessage) {
        $.ajax({
                type: "POST",
                url: url,
                data: data,
                cache: false,
                processData: false,
                contentType: false,
            })
            .done(function() {
                refreshTable();
                $(".modal-form").modal("hide");
                toastr.success(successMessage);
            })
            .fail(function(res) {
                $(".form-text").remove();
                $(".is-invalid").removeClass("is-invalid");
                const errors = jQuery.parseJSON(res.responseText);
                $.each(errors.messages, function(selector, value) {
                    $('[for="' + selector + '"]').after(
                        '<small class="form-text text-danger">' + value + "</small>"
                    );
                    $('[name="' + selector + '"]').addClass("is-invalid");
                });
                toastr.error(errorMessage);
            });
    }

    function deleteItems(ids) {
        Swal.fire({
            title: "¿Estás seguro?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, ¡eliminar!",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                const requests = ids.map((id) =>
                    $.ajax({
                        url: host + "delete/" + id,
                        type: "POST",
                        dataType: "json",
                    })
                );

                $.when
                    .apply($, requests)
                    .done(() => {
                        refreshTable();
                        Swal.fire(
                            "¡Eliminado!",
                            "Los registros han sido eliminados.",
                            "success"
                        );
                    })
                    .fail((jqXHR, textStatus, errorThrown) => {
                        console.error(
                            "Error al eliminar los registros:",
                            textStatus,
                            errorThrown
                        );
                        Swal.fire(
                            "Error",
                            "Hubo un problema al eliminar los registros. Inténtalo de nuevo.",
                            "error"
                        );
                    });
            }
        });
    }

    datatable.on("draw", function() {
        $(".form-action").on("click", function() {
            const button = $(this);
            const modalForm = $(".modal-form");
            const itemId = button.attr("item-id");
            const purpose = button.attr("purpose");

            let title, url, submitUrl;
            if (purpose === "add") {
                title = "Add Data";
                url = host + "new";
                submitUrl = host + "create";
            } else if (purpose === "edit") {
                title = "Edit Data";
                url = host + "edit/" + itemId;
                submitUrl = host + "update/" + itemId;
            } else {
                title = "Detail Data";
                url = host + "show/" + itemId;
            }

            $.ajax({
                    type: "GET",
                    url: url,
                })
                .done(function(response) {
                    modalForm.find(".modal-title").text(title);
                    modalForm.find(".modal-body").html(response);
                    modalForm.modal("show");
                    initializePlugins();

                    $("#form input:text, #form textarea").first().focus();
                    $("#form").on("submit", function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        ajaxRequest(
                            submitUrl,
                            formData,
                            "Registro guardado con éxito",
                            "Error al guardar el registro"
                        );
                    });
                })
                .fail(function() {
                    alert("Data not found");
                });
        });
    });

    $(".refresh").on("click", refreshTable);

    $(".check-items").on("click", function() {
        $("input:checkbox").not(this).prop("checked", this.checked);
    });

    $(".bulk-delete").on("click", function() {
        const ids = $(".bulk-item:checked")
            .map(function() {
                return $(this).val();
            })
            .get();

        if (ids.length) {
            deleteItems(ids);
        } else {
            Swal.fire({
                icon: "error",
                title: "Nada Seleccionado",
                text: "Por favor selecciona algún registro para borrar!",
            });
        }
    });

    function initializePlugins() {
        $(".select2").select2({
            dropdownParent: $("#form"),
        });

        ClassicEditor
            .create(document.querySelector('#ckeditor-editor'), {
                toolbar: {
                    items: [
                        'undo', 'redo',
                        '|',
                        'heading',
                        '|',
                        'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
                        '|',
                        'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                        '|',
                        'link', 'uploadImage', 'blockQuote', 'codeBlock',
                        '|',
                        'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent'
                    ],
                    shouldNotGroupWhenFull: false
                }

            })
            .catch(error => {
                console.error(error);
            });
    }
});
</script>
<?= $this->endSection() ?>