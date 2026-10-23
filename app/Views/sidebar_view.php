<div class="app-menu navbar-menu" style="background:url('<?= base_url(); ?>assets/images/fondobarralateral.webp') top left/cover no-repeat;">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="#" class="logo logo-dark">
            <span class="logo-sm">
                <img src="<?= base_url(); ?>assets/images/logo_fiscalia_login.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="<?= base_url(); ?>assets/images/logo_fiscalia_login.png" style="width: 100px;" alt="">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="#" class="logo logo-light">
            <span class="logo-sm">
                <img src="<?= base_url(); ?>assets/images/logo_fiscalia_login.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="<?= base_url(); ?>assets/images/logo_fiscalia_login.png" style="width: 100px;" alt="">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav" style="padding-bottom: 70px;">
                <?php
                $modules     = session()->get('modules') ?: [];
                $is_empleado = session()->get('is_empleado');
                // ponytail: el JS de app.js compara href literal contra location.pathname y
                // nunca matchea con base_url='http://localhost/fiscalia/'. Resolvemos en PHP
                // (aquí ya tenemos $active=service('uri')->getSegment(1..2)).
                $active_root = service('uri')->getSegment(1);
                $act         = isset($active) ? $active : $active_root;
                $is_active   = function($url) use ($act) {
                    // soporta tanto 'detenidos' como 'detenidos/buscar'
                    if ($act === $url) return true;
                    return strpos($act, $url.'/') === 0;
                };
                ?>
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <?php if(in_array('buss', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('buss') ? ' active' : '' ?>" href="<?= base_url(); ?>buss">
                        <i class="mdi mdi-account-search"></i> <span>BUSS</span>
                    </a>
                </li>
                <?php } ?>
                <?php if(in_array('registro', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('registro') && !$is_active('registro/historial') ? ' active' : '' ?>" href="<?= base_url(); ?>registro">
                        <i class="mdi mdi-file-document"></i> <span data-key="t-widgets">Registro</span>
                    </a>
                </li>
                <?php } ?>
                <?php if(in_array('registro_mp', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('registro/historial') ? ' active' : '' ?>" href="<?= base_url(); ?>registro/historial">
                        <i class="mdi mdi-format-list-text"></i> <span data-key="t-widgets">Historial de registro</span>
                    </a>
                </li>
                <?php } ?>
                <?php if(in_array('motivos_visita', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('motivos_visita') ? ' active' : '' ?>" href="<?= base_url(); ?>motivos_visita">
                        <i class="mdi mdi-clipboard-list-outline"></i> <span data-key="t-widgets">Motivos de visita</span>
                    </a>
                </li>
                <?php } ?>
                <?php if(in_array('areas', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('areas') ? ' active' : '' ?>" href="<?= base_url(); ?>areas">
                        <i class="mdi mdi-domain"></i> <span data-key="t-widgets">Áreas</span>
                    </a>
                </li>
                <?php } ?>
                <?php if(in_array('gestiones', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('gestiones') && !$is_active('gestiones/administracion') ? ' active' : '' ?>" href="<?= base_url(); ?>gestiones">
                        <i class="mdi mdi-alert-circle"></i> <span data-key="t-widgets">Gestiones</span>
                    </a>
                </li>
                <?php } ?>
                <?php if(in_array('gestiones_administracion', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('gestiones/administracion') ? ' active' : '' ?>" href="<?= base_url(); ?>gestiones/administracion">
                        <i class="mdi mdi-alert-circle"></i> <span data-key="t-widgets">Administración Gestiones</span>
                    </a>
                </li>
                <?php } ?>
                <?php if(in_array('comisiones', $modules) || in_array('comisiones_reporte', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('comisiones/reporte') ? ' active' : '' ?>" href="<?= base_url(); ?>comisiones/reporte">
                        <i class="mdi mdi-chart-line"></i> <span data-key="t-widgets">Reporte de comisiones</span>
                    </a>
                </li>
                <?php } ?>

                <?php if(in_array('constancias', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('constanciahechosrevision') ? ' active' : '' ?>" href="<?= base_url(); ?>constanciahechosrevision">
                        <i class="mdi mdi-text-box-search-outline"></i> <span data-key="t-widgets">Constancia de hechos</span>
                    </a>
                </li>
                <?php } ?>

                <?php if(in_array('comisiones', $modules) || $is_empleado){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('comisiones') && !$is_active('comisiones/reporte') && !$is_active('comisiones/solicitudes') ? ' active' : '' ?>" href="<?= base_url(); ?>comisiones">
                        <i class="mdi mdi-account-cash"></i> <span data-key="t-widgets">Comisiones</span>
                    </a>
                </li>
                <?php } ?>

                <?php if(in_array('comisiones', $modules) || in_array('comisiones_solicitudes', $modules) || $is_empleado){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('comisiones/solicitudes') ? ' active' : '' ?>" href="<?= base_url(); ?>comisiones/solicitudes">
                        <i class="mdi mdi-briefcase"></i> <span data-key="t-widgets">Solicitudes de comisión</span>
                    </a>
                </li>
                <?php } ?>

                <?php if(in_array('citas', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('administracionCitas') ? ' active' : '' ?>" href="<?= base_url(); ?>administracionCitas">
                        <i class="mdi mdi-calendar"></i> <span data-key="t-widgets">Citas</span>
                    </a>
                </li>
                <?php } ?>

                <?php if(in_array('instituto', $modules) || in_array('instituto_personal', $modules) || in_array('instituto_seguimiento', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('instituto') ? ' active' : '' ?>" href="<?= base_url(); ?>instituto">
                        <i class="mdi mdi-domain"></i> <span data-key="t-widgets">Instituto</span>
                    </a>
                </li>
                <?php } ?>

                <?php if(in_array('mandamientos', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('mandamientosjudiciales') ? ' active' : '' ?>" href="<?= base_url(); ?>mandamientosjudiciales">
                        <i class="mdi mdi-gavel"></i> <span data-key="t-widgets">Mandamientos Judiciales</span>
                    </a>
                </li>
                <?php } ?>

                <?php if(in_array('mpdigital', $modules)){
                    $multas_children = ['mpdigital/inicio','mpdigital/organigrama','multas/dashboard','detenciones','detenidos','multas','expedientes','denuncias','denunciasmedidas','bienesasegurados','cateos','actuacionesministeriales','exportaciondatos',
                        'Regiones','DetenidosCorporaciones','UnidadesInvestigacion','multa_delitos','multa_motivos_salida','ofendidos'];
                    $multas_active = false;
                    foreach ($multas_children as $child) {
                        if ($is_active($child)) { $multas_active = true; break; }
                    }
                ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('mpdigital/inicio') ? ' active' : '' ?>" href="<?= base_url(); ?>mpdigital/inicio">
                        <i class="mdi mdi-cash-multiple"></i> <span data-key="t-widgets">MP Digital</span>
                    </a>
                    <div class="menu-dropdown<?= $multas_active ? ' show' : '' ?>" id="sidebarMultas">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>mpdigital/inicio" class="nav-link<?= $is_active('mpdigital/inicio') ? ' active' : '' ?>" data-key="t-widgets"><i class="mdi mdi-home"></i> Inicio</a>
                            </li>
                            <?php if(in_array('ver_organigrama_mpdigital', (array)session()->get('special_permissions'), true)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>mpdigital/organigrama" class="nav-link<?= $is_active('mpdigital/organigrama') ? ' active' : '' ?>" data-key="t-widgets"><i class="mdi mdi-sitemap-outline"></i> Organigrama</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('expedientes', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>expedientes" class="nav-link<?= $is_active('expedientes') ? ' active' : '' ?>" data-key="t-widgets">Expedientes</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('detenciones', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>detenciones" class="nav-link<?= $is_active('detenciones') || $is_active('detenidos') ? ' active' : '' ?>" data-key="t-widgets">Detenciones</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('multas', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>multas" class="nav-link<?= $is_active('multas') && !$is_active('multas/dashboard') ? ' active' : '' ?>" data-key="t-widgets">Multas</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('denuncias', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>denuncias" class="nav-link<?= $is_active('denuncias') ? ' active' : '' ?>" data-key="t-widgets">Denuncias</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('denuncias_medidas', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>denunciasmedidas" class="nav-link<?= $is_active('denunciasmedidas') ? ' active' : '' ?>" data-key="t-widgets">Medidas de protección</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('bienes_asegurados', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>bienesasegurados" class="nav-link<?= $is_active('bienesasegurados') ? ' active' : '' ?>" data-key="t-widgets">Bienes asegurados</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('cateos', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>cateos" class="nav-link<?= $is_active('cateos') ? ' active' : '' ?>" data-key="t-widgets">Cateos</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('actuaciones_ministeriales', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>actuacionesministeriales" class="nav-link<?= $is_active('actuacionesministeriales') ? ' active' : '' ?>" data-key="t-widgets">Actuaciones Ministeriales</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('exportacion_datos', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>exportaciondatos" class="nav-link<?= $is_active('exportaciondatos') ? ' active' : '' ?>" data-key="t-widgets">Exportación de datos</a>
                            </li>
                            <?php } ?>
                            <?php
                            // ponytail: sub-grupo colapsable "Estadísticas" (reemplaza
                            // el link simple anterior). Cada sub-vista tiene permiso
                            // granular 'estadistica_<cat>' o hereda del legacy
                            // 'multas_dashboard' para usuarios existentes.
                            $est_cats = [
                                ['detenidos','Detenidos'],
                                ['multas','Multas'],
                                ['expedientes','Expedientes'],
                                ['denuncias','Denuncias'],
                                ['medidas','Medidas de protección'],
                                ['bienes','Bienes asegurados'],
                                ['cateos','Cateos'],
                            ];
                            $has_estadistica = false;
                            foreach ($est_cats as $_e) {
                                if (in_array('estadistica_'.$_e[0], $modules) || in_array('multas_dashboard', $modules)) {
                                    $has_estadistica = true; break;
                                }
                            }
                            if ($has_estadistica):
                                $est_children_urls = [];
                                foreach ($est_cats as $_e) {
                                    $est_children_urls[] = 'multas/estadistica/'.$_e[0];
                                }
                                $est_active = false;
                                foreach ($est_children_urls as $c) {
                                    if ($is_active($c)) { $est_active = true; break; }
                                }
                            ?>
                            <li class="nav-item">
                                <a class="nav-link menu-link<?= $est_active ? ' active' : '' ?>"
                                   href="#sidebarEstMultas" data-bs-toggle="collapse" role="button"
                                   aria-expanded="<?= $est_active ? 'true' : 'false' ?>" aria-controls="sidebarEstMultas">
                                    <i class="mdi mdi-chart-bar"></i> <span data-key="t-widgets">Estadísticas</span>
                                </a>
                                <div class="menu-dropdown<?= $est_active ? ' show' : '' ?>" id="sidebarEstMultas">
                                    <ul class="nav nav-sm flex-column">
                                        <?php foreach ($est_cats as $_e):
                                            $cat = $_e[0];
                                            if (in_array('estadistica_'.$cat, $modules) || in_array('multas_dashboard', $modules)): ?>
                                                <li class="nav-item">
                                                    <a href="<?= base_url(); ?>multas/estadistica/<?= $cat ?>"
                                                       class="nav-link<?= $is_active('multas/estadistica/'.$cat) ? ' active' : '' ?>"
                                                       data-key="t-widgets"><?= $_e[1] ?></a>
                                                </li>
                                        <?php endif; endforeach; ?>
                                    </ul>
                                </div>
                            </li>
                            <?php endif; ?>
                            <li class="nav-item ps-4 mt-2"><small class="text-muted fw-bold">— Cat&aacute;logos —</small></li>
                            <?php if(in_array('regiones', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>Regiones" class="nav-link<?= $is_active('Regiones') ? ' active' : '' ?>" data-key="t-widgets">Regiones</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('detenidos_corporaciones', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>DetenidosCorporaciones" class="nav-link<?= $is_active('DetenidosCorporaciones') ? ' active' : '' ?>" data-key="t-widgets">Corporaciones</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('unidades_investigacion', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>UnidadesInvestigacion" class="nav-link<?= $is_active('UnidadesInvestigacion') ? ' active' : '' ?>" data-key="t-widgets">Unidades de investigaci&oacute;n</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('multa_delitos', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>multa_delitos" class="nav-link<?= $is_active('multa_delitos') ? ' active' : '' ?>" data-key="t-widgets">Catálogo de delitos</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('multa_motivos_salida', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>multa_motivos_salida" class="nav-link<?= $is_active('multa_motivos_salida') ? ' active' : '' ?>" data-key="t-widgets">Motivos de salida</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('ofendidos', $modules)){ ?>
                            <li class="nav-item"><a href="<?= base_url(); ?>ofendidos" class="nav-link<?= $is_active('ofendidos') ? ' active' : '' ?>">Ofendidos</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </li>
                <?php } ?>

                <?php
                $has_stats = in_array('estadisticas', $modules)
                          || in_array('estadisticas_gestiones',   $modules)
                          || in_array('estadisticas_constancias', $modules)
                          || in_array('estadisticas_registro',    $modules);
                if($has_stats){
                    $stats_children = ['estadisticas/gestiones','estadisticas/constancias','estadisticas/registro'];
                    $stats_active = false;
                    foreach ($stats_children as $child) {
                        if ($is_active($child)) { $stats_active = true; break; }
                    }
                ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $stats_active ? ' active' : '' ?>" href="#sidebarEstadisticas" data-bs-toggle="collapse" role="button"
                        aria-expanded="<?= $stats_active ? 'true' : 'false' ?>" aria-controls="sidebarEstadisticas">
                        <i class="mdi mdi-chart-pie"></i> <span data-key="t-widgets">Estadísticas</span>
                    </a>
                    <div class=" menu-dropdown<?= $stats_active ? ' show' : '' ?>" id="sidebarEstadisticas">
                        <ul class="nav nav-sm flex-column">
                            <?php if(in_array('estadisticas', $modules) || in_array('estadisticas_gestiones', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>estadisticas/gestiones" class="nav-link<?= $is_active('estadisticas/gestiones') ? ' active' : '' ?>" data-key="t-widgets">Gestiones</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('estadisticas', $modules) || in_array('estadisticas_constancias', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>estadisticas/constancias" class="nav-link<?= $is_active('estadisticas/constancias') ? ' active' : '' ?>" data-key="t-widgets">Constancias</a>
                            </li>
                            <?php } ?>
                            <?php if(in_array('estadisticas', $modules) || in_array('estadisticas_registro', $modules)){ ?>
                            <li class="nav-item">
                                <a href="<?= base_url(); ?>estadisticas/registro" class="nav-link<?= $is_active('estadisticas/registro') ? ' active' : '' ?>" data-key="t-widgets">Registro</a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </li>
                <?php } ?>

                <?php if(in_array('constancias_titulares', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('constancias_titulares') ? ' active' : '' ?>" href="<?= base_url(); ?>constancias_titulares">
                        <i class="mdi mdi-account"></i> <span data-key="t-widgets">Titulares de constancias</span>
                    </a>
                </li>
                <?php } ?>

                <?php if(in_array('colaboracionforanea', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('colaboracionforanea') ? ' active' : '' ?>" href="<?= base_url(); ?>colaboracionforanea">
                        <i class="mdi mdi-handshake"></i> <span data-key="t-widgets">Colaboración Foránea</span>
                    </a>
                </li>
                <?php } ?>

                <?php if(in_array('usuarios', $modules)){ ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $is_active('usuarios') ? ' active' : '' ?>" href="<?= base_url(); ?>usuarios">
                        <i class="mdi mdi-account"></i> <span data-key="t-widgets">Usuarios</span>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
