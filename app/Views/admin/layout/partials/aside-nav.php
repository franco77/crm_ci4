<aside class="app-sidebar sticky" id="sidebar">

    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header">
        <a href="index.html" class="header-logo">
            <img src="<?= base_url('uploads/logo/') . setting('App.file'); ?>" alt="logo" class="desktop-logo">
            <img src="<?= base_url('uploads/logo/') . setting('App.file'); ?>" alt="logo" class="toggle-logo">
            <img src="<?= base_url('uploads/logo/') . setting('App.file'); ?>" alt="logo" class="desktop-dark">
            <img src="<?= base_url('uploads/logo/') . setting('App.file'); ?>" alt="logo" class="toggle-dark">
        </a>
    </div>
    <!-- End::main-sidebar-header -->

    <!-- Start::main-sidebar -->
    <div class="main-sidebar" id="sidebar-scroll">

        <!-- Start::nav -->
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg>
            </div>
            <ul class="main-menu">
                <!-- Start::slide__category -->
                <li class="slide__category"><span class="category-name">Main</span></li>
                <!-- End::slide__category -->


                <li class="slide">
                    <a href="<?= base_url('admin/dashboard/') ?>" class="side-menu__item">
                        <i class="bx bx-store-alt side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>

                <!-- Start::slide -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="bx bxs-user side-menu__icon"></i>
                        <span class="side-menu__label">Users</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">Users</a>
                        </li>
                        <li class="slide">
                            <a href="<?= base_url('auth/') ?>" class="side-menu__item">Users</a>
                        </li>
                        <li class="slide">
                            <a href="<?= base_url('auth/create_user') ?>" class="side-menu__item">Create Users</a>
                        </li>
                    </ul>
                </li>



                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="bx bxs-user side-menu__icon"></i>
                        <span class="side-menu__label">HRM</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">HRM</a>
                        </li>
                        <li class="slide">
                            <a href="<?= base_url('admin/employees') ?>" class="side-menu__item">Empleados</a>
                        </li>
                        <li class="slide">
                            <a href="<?= base_url('admin/positions') ?>" class="side-menu__item">Cargos</a>
                        </li>
                        <li class="slide">
                            <a href="<?= base_url('admin/payrollslist') ?>" class="side-menu__item">Lista de Pagos</a>
                        </li>
                        <li class="slide">
                            <a href="<?= base_url('admin/employeeloans') ?>" class="side-menu__item">Prestamos</a>
                        </li>
                        <li class="slide">
                            <a href="<?= base_url('admin/loanpayments') ?>" class="side-menu__item">Pagos Prestamos</a>
                        </li>
                        <li class="slide">
                            <a href="<?= base_url('admin/deductions') ?>" class="side-menu__item">Deducciones</a>
                        </li>

                    </ul>
                </li>
                <!-- End::slide -->

                <!-- Start::slide__category -->
                <li class="slide__category"><span class="category-name">Pages</span></li>
                <!-- End::slide__category -->




                <!-- Start::slide -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="bx bx-table side-menu__icon"></i>
                        <span class="side-menu__label">CRM</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">CRM</a>
                        </li>
                        <li class="slide">
                            <a href="<?= base_url('admin/vendors') ?>" class="side-menu__item">Marcas</a>
                        </li>
                        <li class="slide">
                            <a href="<?= base_url('admin/products') ?>" class="side-menu__item">Productos</a>
                        </li>
                        <li class="slide">
                            <a href="<?= base_url('admin/products/list') ?>" class="side-menu__item">Lista De
                                Productos</a>
                        </li>
                        <li class="slide">
                            <a href="<?= base_url('admin/invoices') ?>" class="side-menu__item">Facturas</a>
                        </li>
                    </ul>
                </li>
                <!-- End::slide -->

                <!-- End::slide -->

                <!-- Start::slide -->
                <li class="slide">
                    <a href="icons.html" class="side-menu__item">
                        <i class="bx bx-store-alt side-menu__icon"></i>
                        <span class="side-menu__label">Icons</span>
                    </a>
                </li>
                <!-- End::slide -->
            </ul>
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
                    height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                </svg></div>
        </nav>
        <!-- End::nav -->

    </div>
    <!-- End::main-sidebar -->

</aside>