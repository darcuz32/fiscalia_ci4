<header id="page-topbar" style="background:url('<?= base_url(); ?>assets/images/fondobarralateral.webp') top left/cover no-repeat;">
    <style>
        #overlay{	
            position: fixed;
            top: 0;
            z-index: 1100;
            width: 100%;
            height:100%;
            display: none;
            background: rgba(0,0,0,0.6);
        }
        .cv-spinner {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;  
        }
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px #ddd solid;
            border-top: 4px #2e93e6 solid;
            border-radius: 50%;
            animation: sp-anime 0.8s infinite linear;
        }
        @keyframes sp-anime {
            100% {
                transform: rotate(360deg);
            }
        }
        #topnav-hamburger-icon .hamburger-icon span { background: #fff; }
    </style>
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        

                        <button type="button"
                            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none"
                            id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>

                        <!-- LOGO -->
                        <div class="">
                            <a href="#" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="<?= base_url(); ?>assets/images/logo_fiscalia_login.png" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="<?= base_url(); ?>assets/images/logo_fiscalia_login.png" style="width: 50px;" alt="">
                                </span>
                            </a>

                            <a href="#" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="<?= base_url(); ?>assets/images/logo_fiscalia_login.png" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="<?= base_url(); ?>assets/images/logo_fiscalia_login.png" style="width: 50px;" alt="">
                                </span>
                            </a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">

                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" data-toggle="fullscreen">
                                <i class='bx bx-fullscreen fs-22' style="color:#fff"></i>
                            </button>
                        </div>

                        <!-- <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                                <i class='bx bx-moon fs-22'></i>
                            </button>
                        </div> -->

                        <div class="dropdown ms-sm-3 header-item topbar-user" style="background:url('<?= base_url(); ?>assets/images/fondobarralateral.webp') top left/cover no-repeat;">
                            <button type="button" class="btn shadow-none" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background:transparent">
                                <span class="d-flex align-items-center">
                                    <i class="bx bx-user-circle fs-22" style="color:#fff"></i>
                                    <span class="text-start ms-xl-2">
                                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text" style="color:#fff"><?= session()->get('name'); ?></span>
                                        <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text" style="color:#fff"><?= session()->get('profile'); ?></span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="<?= base_url(); ?>micuenta/firma"><i
                                        class="mdi mdi-draw text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle" data-key="t-firma">Mi firma</span></a>
                                <a class="dropdown-item" href="<?= base_url(); ?>login/logout"><i
                                        class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle" data-key="t-logout">Salir</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
