<script src="https://code.jquery.com/jquery-3.6.1.min.js" crossorigin="anonymous"></script>
<!-- Popper JS -->
<script src="<?= base_url('admin/assets/libs/@popperjs/core/umd/popper.min.js') ?>"></script>

<!-- Bootstrap JS -->
<script src="<?= base_url('admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
<!-- Defaultmenu JS -->
<script src="<?= base_url('admin/assets/js/defaultmenu.min.js') ?>"></script>

<!-- Node Waves JS-->
<script src="<?= base_url('admin/assets/libs/node-waves/waves.min.js') ?>"></script>

<!-- Sticky JS -->
<script src="<?= base_url('admin/assets/js/sticky.js') ?>"></script>

<!-- Simplebar JS -->
<script src="<?= base_url('admin/assets/libs/simplebar/simplebar.min.js') ?>"></script>
<script src="<?= base_url('admin/assets/js/simplebar.js') ?>"></script>

<!-- Color Picker JS -->
<script src="<?= base_url('admin/assets/libs/@simonwep/pickr/pickr.es5.min.js') ?>"></script>



<!-- Custom-Switcher JS -->
<script src="<?= base_url('admin/assets/js/custom-switcher.min.js') ?>"></script>

<!-- Datatables Cdn -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.6/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>


<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Sweetalerts JS -->
<script src="<?= base_url('admin/assets/libs/sweetalert2/sweetalert2.min.js') ?>"></script>
<script src="<?= base_url('admin/assets/js/sweet-alerts.js') ?>"></script>
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#datatableUser').DataTable({
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
            },
            "pageLength": 10,
            scrollX: true
        });
        $('.select2').select2();

    });
</script>
<!-- Custom JS -->
<script src="<?= base_url('admin/assets/js/custom.js') ?>"></script>

<?php $this->renderSection('js') ?>