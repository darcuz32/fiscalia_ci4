<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>
    <?php echo $head; ?>
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <style>
        .users-card .card-header { padding: 1rem 1.25rem; }
        .users-card .card-body { padding: 1.25rem; }
        .users-card .table-responsive { min-height: 280px; }
        .users-card #datatable th { white-space: nowrap; }
        .users-card #datatable td:nth-child(2) { white-space: nowrap; }
        .users-card #datatable td:nth-child(2) .btn {
            padding: .35rem .55rem;
            font-size: .78rem;
        }
        .users-card #datatable td:nth-child(2) .btn i { margin-left: .25rem; }
        .users-card .dataTables_filter label,
        .users-card .dataTables_length label { margin-bottom: 0; }
        .users-card .dataTables_filter input { min-width: 220px; }
        @media (max-width: 767.98px) {
            .users-card .card-header .btn { width: 100%; margin-top: .75rem; }
            .users-card .dataTables_filter,
            .users-card .dataTables_length { text-align: left !important; }
            .users-card .dataTables_filter input { min-width: 0; width: 100%; margin: .4rem 0 0 !important; }
        }
    </style>
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php echo $header; ?>
        <!-- ========== App Menu ========== -->
        <?php echo $sidebar; ?>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card users-card">
                                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                                    <div>
                                        <h5 class="card-title mb-1"><i class="mdi mdi-account-group-outline me-1"></i>Usuarios</h5>
                                        <p class="text-muted mb-0">Administre cuentas, módulos, permisos y alcances operativos.</p>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <?php if (!empty($puede_auditar_expedientes)): ?>
                                            <a href="<?= base_url(); ?>expedientes/auditoria" class="btn btn-light">
                                                <i class="ri-history-line me-1"></i>Auditoría de expedientes
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url(); ?>usuarios/nuevo" class="btn btn-primary waves-effect waves-light">
                                            <i class="mdi mdi-account-plus-outline me-1"></i>Nuevo usuario
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($alertas_adscripcion['mp_sin_unidad']) || !empty($alertas_adscripcion['unidades_sin_coordinador'])): ?>
                                    <div class="alert alert-warning d-flex align-items-start" role="alert">
                                        <i class="ri-alert-line me-2 mt-1"></i>
                                        <div><strong>Configuración de expedientes pendiente.</strong>
                                            Hay <?= (int)$alertas_adscripcion['mp_sin_unidad'] ?> MP sin unidad y
                                            <?= (int)$alertas_adscripcion['unidades_sin_coordinador'] ?> unidades activas sin coordinador.
                                            Estos usuarios no podrán crear o recibir envíos hasta completar su adscripción.
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-bordered nowrap table-hover table-striped align-middle mb-0" style="width:100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Id</th>
                                                    <th>Opciones</th>
                                                    <th>Nombre</th>
                                                    <th>Usuario</th>
													<th>Perfil MP Digital</th>
                                                    <th>Región</th>
                                                    <th>Fecha creación</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © Novarum.app
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <?php echo $js; ?>

    <!--datatable js-->
    <script src="<?= base_url();?>assets/cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url();?>assets/cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?= base_url();?>assets/cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="<?= base_url();?>assets/cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="<?= base_url();?>assets/cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

    <script src="assets/js/pages/datatables.init.js"></script>
    <script type="text/javascript">
            $(document).ready(function() {
                $('#datatable').DataTable({
                    dom: 
                    "<'row g-2 align-items-center mb-3'<'col-12 col-md-6'l><'col-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row g-2 align-items-center mt-3'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
                    buttons: [],
                    columns: [
                        { data: 0 },
                        { data: 6, orderable: false, searchable: false },
                        { data: 1 },
                        { data: 2 },
                        { data: 7 },
                        { data: 4 },
                        { data: 5 }
                    ],
                // buttons: [
                //     { className: "btn btn-secondary btn-light", extend: 'copyHtml5', name: 'Copiar' },
                //     { className: "btn btn-secondary btn-light", extend: 'excelHtml5', name: 'Excel' },
                //     { className: "btn btn-secondary btn-light", extend: 'print', name: 'Imprimir' }
                // ],
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                              url: "<?php echo base_url(); ?>usuarios/loaddata",
                              type: "GET"
                            },
                    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/2.1.8/i18n/es-MX.json',
                    },
                });
                $('#datatable').delegate('.eliminar', 'click', function(event) {
                    var id=$(this).data('id');
                    if(confirm('¿Seguro de eliminar?')){
                        window.location.href='<?php echo base_url(); ?>usuarios/eliminar/'+id;
                    }
                });
            });
        </script>
</body>

</html>
