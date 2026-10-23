<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Fiscalia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/favicon.ico">

    <!-- Layout config Js -->
    <script src="<?php echo base_url(); ?>assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?php echo base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="<?php echo base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="<?php echo base_url(); ?>assets/css/custom.min.css" rel="stylesheet" type="text/css" />


</head>

<body>

    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position" style="background-image:url('<?php echo base_url(); ?>assets/images/fondoHeaderDos.webp'); background-size:cover; background-position:center;" >
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mb-4 text-white-50">
                            <div>
                                <img src="<?php echo base_url(); ?>assets/images/logo_fiscalia_login.png" style="width: 12rem">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Bienvenido</h5>
                                    <p class="text-muted">Ingrese sus credenciales para acceder.</p>
                                </div>
                                <div class="p-2 mt-4">
                                <?php
                                $username = array('name' => 'username', 'placeholder' => 'Ingresa tu usuario', 'class' => 'form-control');
                                $password = array('name' => 'password',    'placeholder' => 'Ingresa tu contraseña', 'class' => 'form-control');
                                $submit = array('name' => 'submit', 'value' => 'Entrar', 'class' => 'btn btn-primary w-md waves-effect waves-light');
                                ?>
                                    <form method="POST" action="<?php echo base_url(); ?>login/new_user">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Usuario</label>
                                            <?php echo form_input($username)?><p><?php echo validation_show_error('username')?>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="password-input">Contraseña</label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                            <?php echo form_password($password)?><p><?php echo validation_show_error('password')?>
                                                <!-- <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted shadow-none password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button> -->
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                        <?php echo form_hidden('token',$token)?>
                                            <button class="btn btn-success w-100" type="submit">Acceder</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                <script>document.write(new Date().getFullYear())</script> Novarum.app
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="<?php echo base_url(); ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/libs/simplebar/simplebar.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/libs/node-waves/waves.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/libs/feather-icons/feather.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/plugins.js"></script>

    <!-- particles js -->
    <script src="<?php echo base_url(); ?>assets/libs/particles.js/particles.js"></script>
    <!-- particles app js -->
    <script src="<?php echo base_url(); ?>assets/js/pages/particles.app.js"></script>
    <!-- password-addon init -->
    <script src="<?php echo base_url(); ?>assets/js/pages/password-addon.init.js"></script>
</body>

</html>