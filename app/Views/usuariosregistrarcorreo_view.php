<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>
    <?php echo $head; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
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
                        <div class="col">

                            <div class="h-100">
                                <div class="row mb-3 pb-1">
                                    <div class="col-12">
                                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                            <div class="flex-grow-1">
                                                <form method="POST" action="<?php echo base_url(); ?>usuarios/actualizar/<?php echo session()->get('userid'); ?>" id="form1">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="header-title">Registra un correo para continuar e iniciar sesión en el futuro</h4>
                                                                <br/><br/>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group mb-3">
                                                                            <label for="email">Correo electrónico</label>
                                                                            <input type="email" id="email" name="email" class="form-control" required="">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <button type="submit" class="btn btn-primary waves-effect waves-light">Guardar</button>
                                                                <!-- end row-->

                                                            </div> <!-- end card body-->
                                                        </div> <!-- end card -->
                                                    </div><!-- end col-->
                                                </div>
                                                </form>


                                            </div>
                                        </div><!-- end card header -->
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->

                            </div> <!-- end .h-100-->

                        </div> <!-- end col -->

                        <?php echo $rightsidebar; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/i18n/defaults-es_ES.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('select[name="region[]"]').selectpicker();
            $('#form1').submit(function(event) {
                event.preventDefault();
                $('button[type="submit"]').prop('disabled', true).html('Guardando...');
                $.post($('#form1').prop('action'), {data: $('#form1').serialize()}, function(data, textStatus, xhr) {
                    if(data=='ok'){
                        toastr.success('Cambio registrado correctamente');
                        setTimeout(function(){
                            window.location.href='<?= base_url(); ?>login';
                        },2000);
                    }
                }).fail(function(){
                    $('button[type="submit"]').prop('disabled', false).html('Guardar');
                    toastr.error('Algo anda mal');
                });
            });
        });
    </script>
</body>

</html>