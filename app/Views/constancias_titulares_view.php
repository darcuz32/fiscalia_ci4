<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>
    <?php echo $head; ?>
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
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
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5 class="card-title mb-0">Titulares de constancias</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Delegación</th>
                                                <th>Opciones</th>
                                                <th>Agente</th>
                                                <th>Firma</th>
                                                <th>Domicilio</th>
                                                <th>Teléfono</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
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
    <script src="<?= base_url();?>assets/cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="<?= base_url();?>assets/cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="<?= base_url();?>assets/cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="<?= base_url();?>assets/cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

    <script src="assets/js/pages/datatables.init.js"></script>
    <script type="text/javascript">
            $(document).ready(function() {
                $('#datatable').DataTable({
                    dom: 
                    "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    buttons: [],
                // buttons: [
                //     { className: "btn btn-secondary btn-light", extend: 'copyHtml5', name: 'Copiar' },
                //     { className: "btn btn-secondary btn-light", extend: 'excelHtml5', name: 'Excel' },
                //     { className: "btn btn-secondary btn-light", extend: 'print', name: 'Imprimir' }
                // ],
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                              url: "<?php echo base_url(); ?>constancias_titulares/loaddata",
                              type: "GET"
                            },
                    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/2.1.8/i18n/es-MX.json',
                    },
                });
                $('#datatable').delegate('.actualizar', 'click', function(event) {
                    var id=$(this).data('id');
                    if(confirm('¿Seguro de actualizar?')){
                        var form = $('<form method="post" action="<?php echo base_url(); ?>constancias_titulares/actualizar"></form>');
                        form.append('<input type="hidden" name="id" value="' + id + '">');
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        </script>
</body>

</html>
