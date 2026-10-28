<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>
    <?php echo $head; ?>
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <!--Highcharts CSS-->
    <style>
        .highcharts-figure {
            min-width: 0;
            max-width: 800px;
            margin: 1em auto;
        }
        
        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #ebebeb;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }
        
        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }
        
        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }
        
        .highcharts-data-table td,
        .highcharts-data-table th,
        .highcharts-data-table caption {
            padding: 0.5em;
        }
        
        .highcharts-data-table thead tr,
        .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }
          .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }
        
        /* Custom table styles */
        .chart-data-table {
            width: 100%;
            font-size: 11px;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .chart-data-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        
        .chart-data-table td {
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        
        .chart-data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .chart-data-table tbody tr:hover {
            background-color: #e3f2fd;
        }
        
        .text-right {
            text-align: right;
        }
        .chart-table-scroll {
            max-height: 520px;
            overflow-y: auto;
        }
          .text-center {
            text-align: center;
        }
        
        /* Tarjetas de totales por tópico */
        .topic-card {
            cursor: pointer;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out, transform .15s ease-in-out;
        }
        
        .topic-card:hover {
            border-color: #adb5bd;
            box-shadow: 0 .125rem .5rem rgba(0, 0, 0, .08);
            transform: translateY(-1px);
        }
        
        .topic-card:focus-visible {
            outline: 0;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .25);
        }
        
        .topic-card.active {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, .04);
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15);
        }
        
        .topic-card .topic-total {
            font-size: 1.5rem;
            line-height: 1.1;
        }
        
        .topic-card .topic-chevron {
            transition: transform .15s ease-in-out;
        }
        
        .topic-card.active .topic-chevron {
            transform: rotate(180deg);
        }
        
        /* Responsive improvements */
        @media (max-width: 1199.98px) {
            .chart-data-table {
                font-size: 10px;
            }
            .chart-data-table th,
            .chart-data-table td {
                padding: 4px 6px;
            }
        }
        
        @media (max-width: 767.98px) {
            .chart-data-table {
                font-size: 9px;
            }
            .chart-data-table th,
            .chart-data-table td {
                padding: 3px 4px;
            }
        }
        
        /* Chart container: Highcharts usa el ancho del contenedor */
        #denuncias-chart-container {
            width: 100%;
            height: 350px;
        }
        
        #detenidos-chart-container {
            width: 100%;
            height: 380px;
        }
        
        #detenidos-delitos-chart-container {
            width: 100%;
            height: 380px;
        }
        
        #cateos-corporaciones-chart-container,
        #cateos-regiones-chart-container {
            width: 100%;
            height: 380px;
        }
        
        #expedientes-chart-container {
            width: 100%;
            height: 380px;
        }
        
        #medidas-chart-container {
            width: 100%;
            height: 380px;
        }

        #dashboard-data {
            position: relative;
        }

        .dashboard-loader {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }
        
        /* Improve card spacing on mobile */
        @media (max-width: 575.98px) {
            .mb-4 {
                margin-bottom: 1rem !important;
            }
            #denuncias-chart-container {
                height: 300px;
            }
            #detenidos-chart-container {
                height: 320px;
            }
            #detenidos-delitos-chart-container {
                height: 320px;
            }
            #cateos-corporaciones-chart-container,
            #cateos-regiones-chart-container {
                height: 320px;
            }
            #expedientes-chart-container {
                height: 320px;
            }
            #medidas-chart-container {
                height: 320px;
            }
            .form-control-sm,
            .form-select-sm {
                min-height: 38px;
            }
            .topic-card .topic-total {
                font-size: 1.35rem;
            }
        }
        
        /* Chosen en filtros */
        #filters-row .chosen-container {
            width: 100% !important;
        }
        #filters-row .chosen-container-single .chosen-single {
            height: 38px;
            line-height: 22px;
            padding: 7px 8px;
            border: 1px solid #ced4da;
            border-radius: .2rem;
            background: #fff;
            box-shadow: none;
            font-size: .875rem;
        }
        #filters-row .chosen-container-single .chosen-single span {
            line-height: 22px;
            margin-right: 20px;
        }
        #filters-row .chosen-container-single .chosen-single div {
            top: 8px;
        }
        #filters-row .chosen-container-single .chosen-search input[type="text"] {
            font-size: .8125rem;
        }
        #filters-row .chosen-container .chosen-results {
            max-height: 240px;
        }
        #filters-row .chosen-container-single .chosen-drop {
            z-index: 1055;
        }
    </style>
</head>

<body>
    <div id="layout-wrapper">
        <?php echo $header; ?>
        <?php echo $sidebar; ?>
        <div class="vertical-overlay"></div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Informe Ejecutivo</h5>
                                </div>

                                <div class="card-header bg-light border-bottom">
                                    <div class="row g-3" id="filters-row">
                                        <div class="col-12 col-sm-6 col-xl">
                                            <div class="form-group mb-0">
                                                <label for="fecha-inicio" class="form-label small mb-1">Fecha Inicio:</label>
                                                <input type="date" id="fecha-inicio" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-xl">
                                            <div class="form-group mb-0">
                                                <label for="fecha-fin" class="form-label small mb-1">Fecha Fin:</label>
                                                <input type="date" id="fecha-fin" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-xl">
                                            <div class="form-group mb-0">
                                                <label for="region-filter" class="form-label small mb-1">Delegación:</label>
                                                <select id="region-filter" class="form-select form-select-sm">
                                                    <option value="">Cargando...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-xl">
                                            <div class="form-group mb-0">
                                                <label for="municipio-filter" class="form-label small mb-1">Municipio:</label>
                                                <select id="municipio-filter" class="form-select form-select-sm">
                                                    <option value="">Cargando...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-xl">
                                            <div class="form-group mb-0">
                                                <label for="unidades-investigacion-filter" class="form-label small mb-1">Unidad de investigación:</label>
                                                <select id="unidades-investigacion-filter" class="form-select form-select-sm">
                                                    <option value="">Cargando...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-xl">
                                            <div class="form-group mb-0">
                                                <label for="estado-filter" class="form-label small mb-1">Estado:</label>
                                                <select id="estado-filter" class="form-select form-select-sm">
                                                    <option value="activo">Solo activos</option>
                                                    <option value="inactivo">Solo inactivos</option>
                                                    <option value="todos" selected>Todos</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-xl">
                                            <div class="form-group mb-0">
                                                <label for="usuario-filter" class="form-label small mb-1">Usuario MP Digital:</label>
                                                <select id="usuario-filter" class="form-select form-select-sm">
                                                    <option value="">Cargando...</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" id="dashboard-data">
                                    <div id="dashboard-loader" class="dashboard-loader d-none">
                                        <div class="d-flex align-items-center gap-2 text-muted">
                                            <span class="spinner-border text-primary" role="status" aria-hidden="true"></span>
                                            <span>Cargando información...</span>
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-2" id="topic-cards">
                                        <div class="col-12 col-sm-6 col-md-4 col-xl">
                                            <div class="card topic-card border h-100" data-topic="denuncias"
                                                role="button" tabindex="0" aria-pressed="false"
                                                aria-label="Mostrar u ocultar las gráficas de denuncias">
                                                <div class="card-body d-flex align-items-center gap-3">
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-primary-subtle text-primary rounded fs-4">
                                                            <i class="ri-file-list-3-line"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <p class="text-muted mb-1 small">Denuncias</p>
                                                        <h4 class="mb-0 topic-total" id="total-denuncias">0</h4>
                                                        <small class="text-muted">registros</small>
                                                    </div>
                                                    <i class="ri-arrow-down-s-line text-muted topic-chevron"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-xl">
                                            <div class="card topic-card border h-100" data-topic="detenidos"
                                                role="button" tabindex="0" aria-pressed="false"
                                                aria-label="Mostrar u ocultar las gráficas de detenidos">
                                                <div class="card-body d-flex align-items-center gap-3">
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-info-subtle text-info rounded fs-4">
                                                            <i class="ri-group-line"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <p class="text-muted mb-1 small">Detenidos</p>
                                                        <h4 class="mb-0 topic-total" id="total-detenidos">0</h4>
                                                        <small class="text-muted">registros</small>
                                                    </div>
                                                    <i class="ri-arrow-down-s-line text-muted topic-chevron"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-xl">
                                            <div class="card topic-card border h-100" data-topic="cateos"
                                                role="button" tabindex="0" aria-pressed="false"
                                                aria-label="Mostrar u ocultar las gráficas de cateos">
                                                <div class="card-body d-flex align-items-center gap-3">
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-warning-subtle text-warning rounded fs-4">
                                                            <i class="ri-home-search-line"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <p class="text-muted mb-1 small">Cateos</p>
                                                        <h4 class="mb-0 topic-total" id="total-cateos">0</h4>
                                                        <small class="text-muted">registros</small>
                                                    </div>
                                                    <i class="ri-arrow-down-s-line text-muted topic-chevron"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-xl">
                                            <div class="card topic-card border h-100" data-topic="expedientes"
                                                role="button" tabindex="0" aria-pressed="false"
                                                aria-label="Mostrar u ocultar las gráficas de expedientes">
                                                <div class="card-body d-flex align-items-center gap-3">
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-success-subtle text-success rounded fs-4">
                                                            <i class="ri-folder-3-line"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <p class="text-muted mb-1 small">Expedientes</p>
                                                        <h4 class="mb-0 topic-total" id="total-expedientes">0</h4>
                                                        <small class="text-muted">registros</small>
                                                    </div>
                                                    <i class="ri-arrow-down-s-line text-muted topic-chevron"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-xl">
                                            <div class="card topic-card border h-100" data-topic="medidas"
                                                role="button" tabindex="0" aria-pressed="false"
                                                aria-label="Mostrar u ocultar las gráficas de medidas de protección">
                                                <div class="card-body d-flex align-items-center gap-3">
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-danger-subtle text-danger rounded fs-4">
                                                            <i class="ri-shield-check-line"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <p class="text-muted mb-1 small">Medidas de Protección</p>
                                                        <h4 class="mb-0 topic-total" id="total-medidas">0</h4>
                                                        <small class="text-muted">registros</small>
                                                    </div>
                                                    <i class="ri-arrow-down-s-line text-muted topic-chevron"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="chart-topic" data-topic="denuncias">
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <div class="card border">
                                                    <div class="card-header bg-light">
                                                        <h6 class="card-title mb-0">Denuncias por Instrumento</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12 col-lg-8">
                                                                <figure class="highcharts-figure mb-0">
                                                                    <div id="denuncias-chart-container"></div>
                                                                </figure>
                                                            </div>
                                                            <div class="col-12 col-lg-4">
                                                                <div id="denuncias-table-container" class="mt-3 mt-lg-0 table-responsive"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="chart-topic" data-topic="detenidos">
                                        <div class="row mb-4">
                                            <div class="col-12 col-xl-6 mb-4 mb-xl-0">
                                                <div class="card border h-100">
                                                    <div class="card-header bg-light">
                                                        <h6 class="card-title mb-0">Detenidos por Rango de Edad</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div id="detenidos-chart-container"></div>
                                                        <div id="detenidos-table-container" class="mt-3 table-responsive"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-6">
                                                <div class="card border h-100">
                                                    <div class="card-header bg-light">
                                                        <h6 class="card-title mb-0">Detenidos por Delito</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div id="detenidos-delitos-chart-container"></div>
                                                        <div id="detenidos-delitos-table-container" class="mt-3 table-responsive"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="chart-topic" data-topic="cateos">
                                        <div class="row mb-4">
                                            <div class="col-12 col-xl-6 mb-4 mb-xl-0">
                                                <div class="card border h-100">
                                                    <div class="card-header bg-light">
                                                        <h6 class="card-title mb-0">Cateos por Corporación</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div id="cateos-corporaciones-chart-container"></div>
                                                        <div id="cateos-corporaciones-table-container" class="mt-3 table-responsive"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-6">
                                                <div class="card border h-100">
                                                    <div class="card-header bg-light">
                                                        <h6 class="card-title mb-0">Cateos por Región</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div id="cateos-regiones-chart-container"></div>
                                                        <div id="cateos-regiones-table-container" class="mt-3 table-responsive"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="chart-topic" data-topic="expedientes">
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <div class="card border">
                                                    <div class="card-header bg-light">
                                                        <h6 class="card-title mb-0">Expedientes por Unidad de Investigación</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12 col-lg-8">
                                                                <figure class="highcharts-figure mb-0">
                                                                    <div id="expedientes-chart-container"></div>
                                                                </figure>
                                                            </div>
                                                            <div class="col-12 col-lg-4">
                                                                <div id="expedientes-table-container" class="mt-3 mt-lg-0 table-responsive chart-table-scroll"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="chart-topic" data-topic="medidas">
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <div class="card border">
                                                    <div class="card-header bg-light">
                                                        <h6 class="card-title mb-0">Medidas de Protección por Delito</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12 col-lg-8">
                                                                <figure class="highcharts-figure mb-0">
                                                                    <div id="medidas-chart-container"></div>
                                                                </figure>
                                                            </div>
                                                            <div class="col-12 col-lg-4">
                                                                <div id="medidas-table-container" class="mt-3 mt-lg-0 table-responsive chart-table-scroll"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
    </div>

    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>

    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <?php echo $js; ?>
    <script src="<?= base_url();?>assets/cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url();?>assets/cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?= base_url();?>assets/cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="<?= base_url();?>assets/cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>

    <!--Highcharts JS-->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/treemap.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            initFilterChosen();
            loadDenunciasRegiones();
            loadDenunciasMunicipios();
            loadDenunciasUnidades();
            loadUsuariosMpDigital();

            $('#fecha-inicio, #fecha-fin, #region-filter, #municipio-filter, #unidades-investigacion-filter, #estado-filter, #usuario-filter').on('change', function () {
                loadDenunciasChart();
                loadDetenidosChart();
                loadDetenidosDelitosChart();
                loadCateosCorporacionesChart();
                loadCateosRegionesChart();
                loadExpedientesChart();
                loadMedidasChart();
            });

            $('#topic-cards').on('click', '.topic-card', function () {
                const card = $(this);
                const wasActive = card.hasClass('active');

                $('#topic-cards .topic-card').removeClass('active').attr('aria-pressed', 'false');

                if (!wasActive) {
                    card.addClass('active').attr('aria-pressed', 'true');
                }

                applyTopicFilter();
            });

            $('#topic-cards').on('keydown', '.topic-card', function (event) {
                if (event.key === 'Enter' || event.key === ' ' || event.key === 'Spacebar') {
                    event.preventDefault();
                    $(this).trigger('click');
                }
            });

            loadDenunciasChart();
            loadDetenidosChart();
            loadDetenidosDelitosChart();
            loadCateosCorporacionesChart();
            loadCateosRegionesChart();
            loadExpedientesChart();
            loadMedidasChart();
        });

        function applyTopicFilter() {
            const activeTopic = $('#topic-cards .topic-card.active').first().data('topic');
            const hasSelection = activeTopic !== undefined && activeTopic !== '';

            $('.chart-topic').each(function () {
                const topic = String($(this).data('topic'));
                $(this).toggle(!hasSelection || topic === String(activeTopic));
            });

            // Highcharts necesita recalcular su tamaño al mostrarse de nuevo.
            window.setTimeout(function () {
                if (window.Highcharts && Highcharts.charts) {
                    Highcharts.charts.forEach(function (chart) {
                        if (chart) chart.reflow();
                    });
                }
            }, 50);
        }

        function setTopicTotal(topic, total) {
            const $total = $('#total-' + topic);
            if ($total.length) {
                $total.text(Number(total).toLocaleString('es-MX'));
            }
        }

        function initFilterChosen() {
            if (!$.fn.chosen) {
                return;
            }

            $('#region-filter, #municipio-filter, #unidades-investigacion-filter, #estado-filter, #usuario-filter').chosen({
                width: '100%',
                search_contains: true,
                no_results_text: 'No se encontraron resultados'
            });
        }

        function refreshFilterChosen(selector) {
            if ($.fn.chosen) {
                $(selector).trigger('chosen:updated');
            }
        }

        let dashboardLoadingCount = 0;

        function showDashboardLoader() {
            dashboardLoadingCount++;
            $('#dashboard-loader').removeClass('d-none');
        }

        function hideDashboardLoader() {
            dashboardLoadingCount = Math.max(dashboardLoadingCount - 1, 0);

            if (dashboardLoadingCount === 0) {
                $('#dashboard-loader').addClass('d-none');
            }
        }

        function loadDenunciasRegiones() {
            $.ajax({
                url: '<?php echo base_url('estadisticas/getOficinasGestiones'); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let options = '<option value="">Todas</option>';
                    response.forEach(function(item) {
                        options += '<option value="' + item.Id + '">' + item.oficina + '</option>';
                    });
                    $('#region-filter').html(options);
                    refreshFilterChosen('#region-filter');
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar oficinas:', error);
                    $('#region-filter').html('<option value="">Error al cargar</option>');
                    refreshFilterChosen('#region-filter');
                }
            });
        }

        function loadDenunciasMunicipios() {
            $.ajax({
                url: '<?php echo base_url('estadisticas/getMunicipios'); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let options = '<option value="">Todos</option>';
                    response.forEach(function(municipio) {
                        options += '<option value="' + municipio.municipio + '">' + municipio.municipio + '</option>';
                    });
                    $('#municipio-filter').html(options);
                    refreshFilterChosen('#municipio-filter');
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar municipios:', error);
                    $('#municipio-filter').html('<option value="">Error al cargar</option>');
                    refreshFilterChosen('#municipio-filter');
                }
            });
        }

        function loadDenunciasUnidades() {
            $.ajax({
                url: '<?php echo base_url('UnidadesInvestigacion/getUnidadesInvestigacion'); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let options = '<option value="">Todas</option>';
                    response.forEach(function(unidad) {
                        options += '<option value="' + unidad.id + '">' + unidad.nombre + '</option>';
                    });
                    $('#unidades-investigacion-filter').html(options);
                    refreshFilterChosen('#unidades-investigacion-filter');
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar unidades de investigación:', error);
                    $('#unidades-investigacion-filter').html('<option value="">Error al cargar</option>');
                    refreshFilterChosen('#unidades-investigacion-filter');
                }
            });
        }

        function loadUsuariosMpDigital() {
            $.ajax({
                url: '<?php echo base_url('InformeEjecutivo/getUsuariosMpDigital'); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const profileLabels = {
                        director: 'Director',
                        fiscal_general_delegado: 'Fiscal - Delegado',
                        fiscal_especializado_coordinador: 'Coordinador',
                        ministerio_publico: 'Ministerio Público'
                    };
                    const profileOrder = ['director', 'fiscal_general_delegado', 'fiscal_especializado_coordinador', 'ministerio_publico'];
                    const grouped = {};

                    (response || []).forEach(function(user) {
                        const profile = user.multas_mini_profile || 'otros';
                        if (!grouped[profile]) grouped[profile] = [];
                        grouped[profile].push(user);
                    });

                    const profiles = Object.keys(grouped).sort(function(a, b) {
                        const ia = profileOrder.indexOf(a);
                        const ib = profileOrder.indexOf(b);
                        return (ia === -1 ? 99 : ia) - (ib === -1 ? 99 : ib);
                    });

                    let options = '<option value="">Todos</option>';
                    profiles.forEach(function(profile) {
                        options += '<optgroup label="' + (profileLabels[profile] || 'MP Digital') + '">';
                        grouped[profile].forEach(function(user) {
                            const name = user.name || user.email || ('Usuario ' + user.Id);
                            options += '<option value="' + user.Id + '">' + $('<div>').text(name).html() + '</option>';
                        });
                        options += '</optgroup>';
                    });
                    $('#usuario-filter').html(options);
                    refreshFilterChosen('#usuario-filter');
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar usuarios MP Digital:', error);
                    $('#usuario-filter').html('<option value="">Error al cargar</option>');
                    refreshFilterChosen('#usuario-filter');
                }
            });
        }

        function getFiltrosParams() {
            let params = [];
            const inicio    = $('#fecha-inicio').val();
            const fin       = $('#fecha-fin').val();
            const region    = $('#region-filter').val();
            const municipio = $('#municipio-filter').val();
            const unidad    = $('#unidades-investigacion-filter').val();
            const estado    = $('#estado-filter').val();
            const usuario   = $('#usuario-filter').val();

            if (inicio)    params.push('fechaInicio=' + encodeURIComponent(inicio));
            if (fin)       params.push('fechaFin=' + encodeURIComponent(fin));
            if (region)    params.push('region=' + encodeURIComponent(region));
            if (municipio) params.push('municipio=' + encodeURIComponent(municipio));
            if (unidad)    params.push('unidad=' + encodeURIComponent(unidad));
            if (estado)    params.push('estado=' + encodeURIComponent(estado));
            if (usuario)   params.push('usuario=' + encodeURIComponent(usuario));
            return params;
        }

        function loadDenunciasChart() {
            let url = '<?php echo base_url('InformeEjecutivo/getDenunciasPorInstrumento'); ?>';
            const params = getFiltrosParams();
            if (params.length > 0) url += '?' + params.join('&');

            $('#denuncias-table-container').html('<div class="text-center text-muted small py-3">Cargando...</div>');
            showDashboardLoader();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    createDenunciasChart(response);
                    hideDashboardLoader();
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar denuncias por instrumento:', error);
                    $('#denuncias-table-container').html('<div class="text-center text-danger small py-3">No fue posible cargar la información.</div>');
                    hideDashboardLoader();
                }
            });
        }

        function loadDetenidosChart() {
            let url = '<?php echo base_url('InformeEjecutivo/getDetenidosPorRangoEdad'); ?>';
            const params = getFiltrosParams();
            if (params.length > 0) url += '?' + params.join('&');

            showDashboardLoader();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    createDetenidosChart(response);
                    hideDashboardLoader();
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar detenidos por rango de edad:', error);
                    hideDashboardLoader();
                }
            });
        }

        function loadDetenidosDelitosChart() {
            let url = '<?php echo base_url('InformeEjecutivo/getDetenidosPorDelito'); ?>';
            const params = getFiltrosParams();
            if (params.length > 0) url += '?' + params.join('&');

            showDashboardLoader();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    createDetenidosDelitosChart(response);
                    hideDashboardLoader();
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar detenidos por delito:', error);
                    hideDashboardLoader();
                }
            });
        }

        function loadCateosCorporacionesChart() {
            let url = '<?php echo base_url('InformeEjecutivo/getCateosPorCorporacion'); ?>';
            const params = getFiltrosParams();
            if (params.length > 0) url += '?' + params.join('&');

            showDashboardLoader();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    createCateosCorporacionesChart(response);
                    hideDashboardLoader();
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar cateos por corporación:', error);
                    hideDashboardLoader();
                }
            });
        }

        function loadCateosRegionesChart() {
            let url = '<?php echo base_url('InformeEjecutivo/getCateosPorRegion'); ?>';
            const params = getFiltrosParams();
            if (params.length > 0) url += '?' + params.join('&');

            showDashboardLoader();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    createCateosRegionesChart(response);
                    hideDashboardLoader();
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar cateos por región:', error);
                    hideDashboardLoader();
                }
            });
        }

        function loadExpedientesChart() {
            let url = '<?php echo base_url('InformeEjecutivo/getExpedientesPorUnidad'); ?>';
            const params = getFiltrosParams();
            if (params.length > 0) url += '?' + params.join('&');

            showDashboardLoader();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    createExpedientesChart(response);
                    hideDashboardLoader();
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar expedientes por unidad:', error);
                    hideDashboardLoader();
                }
            });
        }

        function loadMedidasChart() {
            let url = '<?php echo base_url('InformeEjecutivo/getMedidasProteccionPorDelito'); ?>';
            const params = getFiltrosParams();
            if (params.length > 0) url += '?' + params.join('&');

            showDashboardLoader();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    createMedidasChart(response);
                    hideDashboardLoader();
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar medidas de protección por delito:', error);
                    hideDashboardLoader();
                }
            });
        }

        function createDenunciasChart(instrumentData) {
            const palette = ['#E74C3C', '#E67E22', '#F39C12', '#27AE60', '#1ABC9C',
                             '#3498DB', '#9B59B6', '#34495E', '#95A5A6', '#7F8C8D'];

            const seriesData = (instrumentData || []).map(function(item, index) {
                return {
                    name: item.instrumento,
                    y: parseInt(item.total, 10) || 0,
                    color: palette[index % palette.length]
                };
            });

            const totalDenuncias = seriesData.reduce((sum, item) => sum + item.y, 0);
            setTopicTotal('denuncias', totalDenuncias);

            Highcharts.chart('denuncias-chart-container', {
                chart: { type: 'bar', backgroundColor: 'transparent' },
                title: {
                    text: 'Denuncias por Instrumento',
                    style: { fontSize: '16px', fontWeight: 'bold' }
                },
                subtitle: {
                    text: seriesData.length
                        ? 'Distribución según el instrumento del delito'
                        : 'Sin información para los filtros seleccionados'
                },
                xAxis: {
                    categories: seriesData.map(function(item) { return item.name; }),
                    crosshair: true,
                    labels: { style: { fontSize: '11px' } }
                },
                yAxis: {
                    min: 0,
                    title: { text: 'Cantidad de denuncias' }
                },
                legend: { enabled: false },
                tooltip: {
                    headerFormat: '<span style="font-size:14px;font-weight:bold">{point.key}</span><br/>',
                    pointFormat: 'Denuncias: <b>{point.y:,.0f}</b>'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 5,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:,.0f}',
                            style: { fontSize: '11px', fontWeight: 'bold' }
                        }
                    }
                },
                series: [{
                    name: 'Denuncias',
                    data: seriesData,
                    colorByPoint: true
                }],
                credits: { enabled: false },
                exporting: { enabled: true },
                noData: { style: { fontWeight: 'normal', fontSize: '13px', color: '#666' } },
                responsive: {
                    rules: [{
                        condition: { maxWidth: 575 },
                        chartOptions: {
                            chart: { height: 300 },
                            title: { style: { fontSize: '14px' } },
                            subtitle: { style: { fontSize: '10px' } },
                            xAxis: {
                                labels: { style: { fontSize: '9px' } }
                            },
                            yAxis: {
                                title: { text: null },
                                labels: { style: { fontSize: '9px' } }
                            },
                            plotOptions: {
                                bar: {
                                    dataLabels: { style: { fontSize: '9px' } }
                                }
                            }
                        }
                    }]
                }
            });

            createDenunciasTable('denuncias-table-container', seriesData);
        }

        function createDenunciasTable(containerId, data, options) {
            const config = options || {};
            const categoryLabel = config.categoryLabel || 'Instrumento';
            const countLabel = config.countLabel || 'Denuncias';

            if (!data.length) {
                document.getElementById(containerId).innerHTML =
                    '<div class="text-center text-muted small py-3">Sin información para los filtros seleccionados.</div>';
                return;
            }

            const total = data.reduce((sum, item) => sum + item.y, 0);
            const maxValue = Math.max.apply(null, data.map(item => item.y));

            let tableHTML = `
                <table class="chart-data-table">
                    <thead>
                        <tr>
                            <th style="width: 20px;"></th>
                            <th>${categoryLabel}</th>
                            <th class="text-right">${countLabel}</th>
                            <th class="text-right">%</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach((item) => {
                const percentage = total > 0 ? ((item.y / total) * 100).toFixed(1) : '0.0';
                const isMax = item.y === maxValue;
                const rowStyle = isMax ? 'background-color: #d4edda; font-weight: bold;' : '';
                const color = item.color || '#95A5A6';
                tableHTML += `
                    <tr style="${rowStyle}">
                        <td style="padding: 6px 4px;">
                            <div style="width: 12px; height: 12px; background-color: ${color}; border-radius: 2px; display: inline-block;"></div>
                        </td>
                        <td>${item.name}${isMax ? ' ⭐' : ''}</td>
                        <td class="text-right">${item.y.toLocaleString()}</td>
                        <td class="text-right">${percentage}%</td>
                    </tr>
                `;
            });

            tableHTML += `
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold; background-color: #e9ecef;">
                            <td></td>
                            <td>Total</td>
                            <td class="text-right">${total.toLocaleString()}</td>
                            <td class="text-right">100.0%</td>
                        </tr>
                    </tfoot>
                </table>
            `;

            document.getElementById(containerId).innerHTML = tableHTML;
        }

        function createDetenidosChart(ageData) {
            const rangeColors = {
                '0-17': '#9B59B6',
                '18-25': '#3498DB',
                '26-35': '#1ABC9C',
                '36-45': '#F39C12',
                '46-60': '#E67E22',
                '61+': '#E74C3C',
                'Sin dato': '#95A5A6'
            };

            const seriesData = (ageData || []).map(function (item) {
                return {
                    name: item.rango,
                    y: parseInt(item.total, 10) || 0,
                    color: rangeColors[item.rango] || '#7F8C8D'
                };
            });

            const totalDetenidos = seriesData.reduce((sum, item) => sum + item.y, 0);
            setTopicTotal('detenidos', totalDetenidos);

            createDenunciasTable('detenidos-table-container', seriesData, {
                categoryLabel: 'Rango de edad',
                countLabel: 'Detenidos'
            });

            Highcharts.chart('detenidos-chart-container', {
                chart: { type: 'pie', backgroundColor: 'transparent' },
                title: {
                    text: 'Detenidos por Rango de Edad',
                    style: { fontSize: '16px', fontWeight: 'bold' }
                },
                subtitle: {
                    text: seriesData.length
                        ? 'Distribución por grupos de edad'
                        : 'Sin información para los filtros seleccionados'
                },
                tooltip: {
                    pointFormat: '<b>{point.y:,.0f}</b> detenidos ({point.percentage:.1f}%)'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b><br>{point.y:,.0f} ({point.percentage:.1f}%)',
                            style: { fontSize: '11px' }
                        }
                    }
                },
                legend: {
                    enabled: true,
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    itemStyle: { fontSize: '11px' }
                },
                series: [{
                    name: 'Detenidos',
                    colorByPoint: true,
                    data: seriesData
                }],
                credits: { enabled: false },
                exporting: { enabled: true },
                noData: { style: { fontWeight: 'normal', fontSize: '13px', color: '#666' } },
                responsive: {
                    rules: [{
                        condition: { maxWidth: 575 },
                        chartOptions: {
                            chart: { height: 320 },
                            title: { style: { fontSize: '14px' } },
                            subtitle: { style: { fontSize: '10px' } },
                            plotOptions: {
                                pie: {
                                    dataLabels: {
                                        distance: 12,
                                        format: '<b>{point.name}</b>',
                                        style: { fontSize: '9px' }
                                    }
                                }
                            },
                            legend: { itemStyle: { fontSize: '10px' } }
                        }
                    }]
                }
            });
        }

        function createDetenidosDelitosChart(delitoData) {
            const palette = ['#E74C3C', '#E67E22', '#F39C12', '#27AE60', '#3498DB', '#95A5A6'];

            const seriesData = (delitoData || []).map(function (item, index) {
                return {
                    name: item.delito,
                    y: parseInt(item.total, 10) || 0,
                    color: item.delito === 'Resto' ? '#95A5A6' : palette[index % palette.length]
                };
            });

            createDenunciasTable('detenidos-delitos-table-container', seriesData, {
                categoryLabel: 'Delito',
                countLabel: 'Detenidos'
            });

            Highcharts.chart('detenidos-delitos-chart-container', {
                chart: { type: 'pie', backgroundColor: 'transparent' },
                title: {
                    text: 'Detenidos por Delito',
                    style: { fontSize: '16px', fontWeight: 'bold' }
                },
                subtitle: {
                    text: seriesData.length
                        ? 'Top 5 delitos con más registros y el resto'
                        : 'Sin información para los filtros seleccionados'
                },
                tooltip: {
                    pointFormat: '<b>{point.y:,.0f}</b> registros ({point.percentage:.1f}%)'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b><br>{point.y:,.0f} ({point.percentage:.1f}%)',
                            style: { fontSize: '11px' }
                        }
                    }
                },
                legend: {
                    enabled: true,
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    itemStyle: { fontSize: '11px' }
                },
                series: [{
                    name: 'Registros',
                    colorByPoint: true,
                    data: seriesData
                }],
                credits: { enabled: false },
                exporting: { enabled: true },
                noData: { style: { fontWeight: 'normal', fontSize: '13px', color: '#666' } },
                responsive: {
                    rules: [{
                        condition: { maxWidth: 575 },
                        chartOptions: {
                            chart: { height: 320 },
                            title: { style: { fontSize: '14px' } },
                            subtitle: { style: { fontSize: '10px' } },
                            plotOptions: {
                                pie: {
                                    dataLabels: {
                                        distance: 12,
                                        format: '<b>{point.name}</b>',
                                        style: { fontSize: '9px' }
                                    }
                                }
                            },
                            legend: { itemStyle: { fontSize: '10px' } }
                        }
                    }]
                }
            });
        }

        function createCateosCorporacionesChart(corporacionData) {
            const palette = ['#E74C3C', '#E67E22', '#F39C12', '#27AE60', '#1ABC9C',
                             '#3498DB', '#9B59B6', '#34495E', '#95A5A6', '#7F8C8D'];

            const seriesData = (corporacionData || []).map(function (item, index) {
                return {
                    name: item.corporacion,
                    y: parseInt(item.total, 10) || 0,
                    color: palette[index % palette.length]
                };
            });

            createDenunciasTable('cateos-corporaciones-table-container', seriesData, {
                categoryLabel: 'Corporación',
                countLabel: 'Cateos'
            });

            Highcharts.chart('cateos-corporaciones-chart-container', {
                chart: { type: 'bar', backgroundColor: 'transparent' },
                title: {
                    text: 'Cateos por Corporación',
                    style: { fontSize: '16px', fontWeight: 'bold' }
                },
                subtitle: {
                    text: seriesData.length
                        ? 'Cateos en los que participó cada corporación'
                        : 'Sin información para los filtros seleccionados'
                },
                xAxis: {
                    categories: seriesData.map(function (item) { return item.name; }),
                    crosshair: true,
                    labels: { style: { fontSize: '11px' } }
                },
                yAxis: {
                    min: 0,
                    allowDecimals: false,
                    title: { text: 'Cantidad de cateos' }
                },
                legend: { enabled: false },
                tooltip: {
                    headerFormat: '<span style="font-size:14px;font-weight:bold">{point.key}</span><br/>',
                    pointFormat: 'Cateos: <b>{point.y:,.0f}</b>'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 5,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:,.0f}',
                            style: { fontSize: '11px', fontWeight: 'bold' }
                        }
                    }
                },
                series: [{
                    name: 'Cateos',
                    colorByPoint: true,
                    data: seriesData
                }],
                credits: { enabled: false },
                exporting: { enabled: true },
                noData: { style: { fontWeight: 'normal', fontSize: '13px', color: '#666' } },
                responsive: {
                    rules: [{
                        condition: { maxWidth: 575 },
                        chartOptions: {
                            chart: { height: 320 },
                            title: { style: { fontSize: '14px' } },
                            subtitle: { style: { fontSize: '10px' } },
                            xAxis: {
                                labels: { style: { fontSize: '9px' } }
                            },
                            yAxis: {
                                title: { text: null },
                                labels: { style: { fontSize: '9px' } }
                            },
                            plotOptions: {
                                bar: {
                                    dataLabels: { style: { fontSize: '9px' } }
                                }
                            }
                        }
                    }]
                }
            });
        }

        function createCateosRegionesChart(regionData) {
            const palette = ['#3498DB', '#1ABC9C', '#F39C12', '#E74C3C', '#9B59B6',
                             '#E67E22', '#27AE60', '#34495E', '#95A5A6', '#7F8C8D'];

            const seriesData = (regionData || []).map(function (item, index) {
                return {
                    name: item.region,
                    y: parseInt(item.total, 10) || 0,
                    color: palette[index % palette.length]
                };
            });

            const totalCateos = seriesData.reduce((sum, item) => sum + item.y, 0);
            setTopicTotal('cateos', totalCateos);

            createDenunciasTable('cateos-regiones-table-container', seriesData, {
                categoryLabel: 'Región',
                countLabel: 'Cateos'
            });

            Highcharts.chart('cateos-regiones-chart-container', {
                chart: { type: 'pie', backgroundColor: 'transparent' },
                title: {
                    text: 'Cateos por Región',
                    style: { fontSize: '16px', fontWeight: 'bold' }
                },
                subtitle: {
                    text: seriesData.length
                        ? 'Distribución de cateos por región'
                        : 'Sin información para los filtros seleccionados'
                },
                tooltip: {
                    pointFormat: '<b>{point.y:,.0f}</b> cateos ({point.percentage:.1f}%)'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b><br>{point.y:,.0f} ({point.percentage:.1f}%)',
                            style: { fontSize: '11px' }
                        }
                    }
                },
                legend: {
                    enabled: true,
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    itemStyle: { fontSize: '11px' }
                },
                series: [{
                    name: 'Cateos',
                    colorByPoint: true,
                    data: seriesData
                }],
                credits: { enabled: false },
                exporting: { enabled: true },
                noData: { style: { fontWeight: 'normal', fontSize: '13px', color: '#666' } },
                responsive: {
                    rules: [{
                        condition: { maxWidth: 575 },
                        chartOptions: {
                            chart: { height: 320 },
                            title: { style: { fontSize: '14px' } },
                            subtitle: { style: { fontSize: '10px' } },
                            plotOptions: {
                                pie: {
                                    dataLabels: {
                                        distance: 12,
                                        format: '<b>{point.name}</b>',
                                        style: { fontSize: '9px' }
                                    }
                                }
                            },
                            legend: { itemStyle: { fontSize: '10px' } }
                        }
                    }]
                }
            });
        }

        function createExpedientesChart(expedienteData) {
            const palette = ['#3498DB', '#1ABC9C', '#F39C12', '#E74C3C', '#9B59B6',
                             '#E67E22', '#27AE60', '#34495E', '#95A5A6', '#7F8C8D'];

            const seriesData = (expedienteData || []).map(function (item, index) {
                return {
                    name: item.unidad,
                    value: parseInt(item.total, 10) || 0,
                    color: palette[index % palette.length]
                };
            });

            const totalExpedientes = seriesData.reduce((sum, item) => sum + item.value, 0);
            setTopicTotal('expedientes', totalExpedientes);

            createDenunciasTable('expedientes-table-container', seriesData.map(function (item) {
                return { name: item.name, y: item.value, color: item.color };
            }), {
                categoryLabel: 'Unidad de investigación',
                countLabel: 'Expedientes'
            });

            const chartHeight = seriesData.length > 12 ? 520 : 420;

            Highcharts.chart('expedientes-chart-container', {
                chart: {
                    type: 'treemap',
                    height: chartHeight,
                    backgroundColor: 'transparent'
                },
                title: {
                    text: 'Expedientes por Unidad de Investigación',
                    style: { fontSize: '16px', fontWeight: 'bold' }
                },
                subtitle: {
                    text: seriesData.length
                        ? 'Distribución de expedientes según la unidad que los registró'
                        : 'Sin información para los filtros seleccionados'
                },
                tooltip: {
                    pointFormat: '<b>{point.value:,.0f}</b> expedientes'
                },
                series: [{
                    type: 'treemap',
                    name: 'Expedientes',
                    colorByPoint: true,
                    layoutAlgorithm: 'squarified',
                    data: seriesData,
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b><br>{point.value:,.0f}',
                        style: { fontSize: '11px', textOutline: 'none' }
                    },
                    levels: [{
                        level: 1,
                        borderWidth: 3,
                        dataLabels: {
                            enabled: true,
                            align: 'left',
                            verticalAlign: 'top'
                        }
                    }]
                }],
                credits: { enabled: false },
                exporting: { enabled: true },
                noData: { style: { fontWeight: 'normal', fontSize: '13px', color: '#666' } },
                responsive: {
                    rules: [{
                        condition: { maxWidth: 575 },
                        chartOptions: {
                            title: { style: { fontSize: '14px' } },
                            subtitle: { style: { fontSize: '10px' } },
                            series: [{
                                dataLabels: { style: { fontSize: '9px' } }
                            }]
                        }
                    }]
                }
            });
        }

        function createMedidasChart(medidaData) {
            const palette = ['#E74C3C', '#E67E22', '#F39C12', '#27AE60', '#1ABC9C',
                             '#3498DB', '#9B59B6', '#34495E', '#95A5A6', '#7F8C8D'];

            const seriesData = (medidaData || []).map(function (item, index) {
                return {
                    name: item.delito,
                    y: parseInt(item.total, 10) || 0,
                    color: palette[index % palette.length]
                };
            });

            const totalMedidas = seriesData.reduce((sum, item) => sum + item.y, 0);
            setTopicTotal('medidas', totalMedidas);

            createDenunciasTable('medidas-table-container', seriesData, {
                categoryLabel: 'Delito',
                countLabel: 'Medidas'
            });

            const chartHeight = Math.max(380, seriesData.length * 30 + 140);

            Highcharts.chart('medidas-chart-container', {
                chart: {
                    type: 'bar',
                    height: chartHeight,
                    backgroundColor: 'transparent'
                },
                title: {
                    text: 'Medidas de Protección por Delito',
                    style: { fontSize: '16px', fontWeight: 'bold' }
                },
                subtitle: {
                    text: seriesData.length
                        ? 'Distribución de medidas de protección según el delito principal'
                        : 'Sin información para los filtros seleccionados'
                },
                xAxis: {
                    categories: seriesData.map(function (item) { return item.name; }),
                    crosshair: true,
                    labels: { style: { fontSize: '11px' } }
                },
                yAxis: {
                    min: 0,
                    allowDecimals: false,
                    title: { text: 'Cantidad de medidas' }
                },
                legend: { enabled: false },
                tooltip: {
                    headerFormat: '<span style="font-size:14px;font-weight:bold">{point.key}</span><br/>',
                    pointFormat: 'Medidas: <b>{point.y:,.0f}</b>'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 5,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:,.0f}',
                            style: { fontSize: '11px', fontWeight: 'bold' }
                        }
                    }
                },
                series: [{
                    name: 'Medidas de protección',
                    colorByPoint: true,
                    data: seriesData
                }],
                credits: { enabled: false },
                exporting: { enabled: true },
                noData: { style: { fontWeight: 'normal', fontSize: '13px', color: '#666' } },
                responsive: {
                    rules: [{
                        condition: { maxWidth: 575 },
                        chartOptions: {
                            title: { style: { fontSize: '14px' } },
                            subtitle: { style: { fontSize: '10px' } },
                            xAxis: {
                                labels: { style: { fontSize: '9px' } }
                            },
                            yAxis: {
                                title: { text: null },
                                labels: { style: { fontSize: '9px' } }
                            },
                            plotOptions: {
                                bar: {
                                    dataLabels: { style: { fontSize: '9px' } }
                                }
                            }
                        }
                    }]
                }
            });
        }
    </script>
</body>
</html>
