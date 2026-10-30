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

        /* Leyenda externa de la gráfica de pastel */
        .pie-legend {
            display: flex;
            flex-wrap: wrap;
            gap: .15rem .6rem;
            max-height: 78px;
            overflow-y: auto;
            font-size: 10px;
            line-height: 1.2;
        }

        .pie-legend-item {
            display: flex;
            align-items: center;
            gap: .25rem;
            min-width: 0;
        }

        .pie-legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 2px;
            flex: 0 0 auto;
        }

        .pie-legend-name {
            max-width: 110px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pie-legend-value {
            color: #6c757d;
            flex: 0 0 auto;
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
        
        #topic-cards {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            margin-left: 0;
            margin-right: 0;
            padding-bottom: .5rem;
            scrollbar-width: thin;
            -webkit-overflow-scrolling: touch;
        }

        .topic-card .card-body {
            width: auto;
            min-height: 0;
            padding: .6rem .75rem;
            gap: .35rem !important;
            flex: 0 0 auto;
            margin: auto;
        }

        .topic-card .avatar-sm {
            width: 2rem;
            height: 2rem;
        }

        .topic-card .avatar-title {
            font-size: 1.1rem !important;
        }

        .topic-card .card-body p {
            line-height: 1.15;
            white-space: nowrap;
        }

        /* Colapso de filtros solo en mobile */
        .mobile-filters-toggle {
            display: flex;
            align-items: center;
            gap: .5rem;
            min-width: 0;
        }

        .mobile-filters-summary {
            flex: 1 1 auto;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: .72rem;
            color: #6c757d;
        }

        @media (min-width: 768px) {
            #filters-collapsible {
                display: block !important;
            }
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

            /* Todas las tarjetas de tópicos en una sola línea */
            #topic-cards {
                gap: .25rem;
                padding-bottom: .25rem;
            }

            #topic-cards > .col-auto {
                flex: 1 1 0;
                width: 0;
                min-width: 0;
                padding-left: 0;
                padding-right: 0;
            }

            #topic-cards .topic-card .card-body {
                width: 100%;
                min-width: 0;
                padding: .3rem .15rem;
                gap: .15rem !important;
            }

            #topic-cards .topic-card .card-body > div {
                width: 100%;
                min-width: 0;
            }

            #topic-cards .topic-card {
                min-width: 0;
            }

            #topic-cards .topic-card .avatar-sm {
                width: 1.45rem;
                height: 1.45rem;
            }

            #topic-cards .topic-card .avatar-title {
                font-size: .8rem !important;
            }

            #topic-cards .topic-card .card-body p {
                font-size: .5rem;
                line-height: 1.05;
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* KPIs (Total, Activos, Concluidos) en una sola línea */
            .metric-kpi-row .card-body {
                padding: .45rem .35rem;
                gap: .2rem !important;
            }

            .metric-kpi-row .card-body > .avatar-title {
                display: none !important;
            }

            .metric-kpi-row .card-body p {
                font-size: .55rem;
                line-height: 1.1;
                margin-bottom: .1rem !important;
            }

            .metric-kpi-row .card-body h3 {
                font-size: .95rem;
                line-height: 1.1;
            }

            .metric-kpi-row .card-body h3 small {
                font-size: .55rem !important;
            }

            /* Tarjeta de variación compacta en mobile */
            [id^="variacion-"] .card-body {
                align-items: flex-start;
                gap: .35rem !important;
                padding: .5rem .6rem;
            }

            [id^="variacion-"] .card-body > i {
                display: none !important;
            }

            [id^="variacion-"] h4 {
                font-size: 1rem;
            }

            [id^="variacion-"] p {
                font-size: .6rem !important;
            }
        }
        
        /* Chart container: Highcharts usa el ancho del contenedor */
        .metric-chart {
            width: 100%;
            height: 360px;
        }

        #dashboard-data {
            position: relative;
        }

        .chart-topic {
            display: none;
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
            .metric-chart {
                height: 300px;
            }
            .form-control-sm,
            .form-select-sm {
                min-height: 38px;
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
            margin-top: 0px;
        }
        #filters-row .chosen-container-single .chosen-single div {
            top: 4px;
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

        /* Select Chosen */
        .chosen-container-single .chosen-single,
        .chosen-container-single .chosen-single:hover,
        .chosen-container-active .chosen-single,
        .chosen-container-active.chosen-with-drop .chosen-single {
            height: 38px;
            line-height: 36px;
            padding: 0 14px;

            background: #fff !important;
            border: 2px solid #0B2341 !important;
            border-radius: 50rem !important;

            box-shadow: none;
            color: #0B2341 !important;
            font-size: 1rem;
        }

        /* Texto */
        .chosen-container-single .chosen-single span {
            color: #0B2341 !important;
        }

        /* Flecha: cambiar el color del indicador de Chosen */
        .chosen-container-single .chosen-single div b {
            background: none !important;
            position: relative;
        }

        .chosen-container-single .chosen-single div b::before {
            content: "";
            position: absolute;
            top: 10px;
            left: 50%;
            width: 8px;
            height: 8px;
            border-right: 2px solid #0B2341;
            border-bottom: 2px solid #0B2341;
            transform: translateX(-50%) rotate(45deg);
        }

        /* Menú desplegable */
        .chosen-container .chosen-drop {
            background: #fff !important;
            border: 2px solid #0B2341 !important;
            border-radius: 0.75rem !important;
            overflow: hidden;
            box-shadow: 0 0.25rem 0.5rem rgba(11, 35, 65, 0.12);
        }

        /* Opciones */
        .chosen-container .chosen-results li {
            color: #0B2341;
        }

        .chosen-container .chosen-results li.highlighted {
            background: #0B2341 !important;
            color: #fff !important;
        }

        .chosen-container .chosen-results li.result-selected {
            color: #0B2341;
            font-weight: 600;
        }

        /* Inputs tipo date */
        input[type="date"] {
            height: 38px;
            padding: 0 14px;

            background-color: #fff;
            border: 2px solid #0B2341;
            border-radius: 50rem;

            box-shadow: none;
            color: #0B2341;
            font-size: 1rem;
        }

        /* Hover */
        input[type="date"]:hover {
            background-color: #f5f7fa;
            border-color: #0B2341;
        }

        /* Focus */
        input[type="date"]:focus {
            background-color: #fff;
            border-color: #0B2341;
            box-shadow: 0 0 0 2px rgba(11, 35, 65, 0.15);
            outline: none;
        }

        /* Icono del calendario en navegadores compatibles */
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 1;
            filter: brightness(0) saturate(100%)
                    invert(10%) sepia(25%) saturate(2500%)
                    hue-rotate(180deg) brightness(85%) contrast(105%);
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
                                    
                                    <div class="row g-3 mb-2" id="topic-cards">
                                        <div class="col-auto">
                                            <div class="card topic-card border h-100" data-topic="denuncias"
                                                role="button" tabindex="0" aria-pressed="false"
                                                aria-label="Mostrar u ocultar las gráficas de denuncias">
                                                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center gap-2">
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-primary-subtle text-primary rounded fs-4">
                                                            <i class="ri-file-list-3-line"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="text-muted mb-0 small">Denuncias</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="card topic-card border h-100" data-topic="detenidos"
                                                role="button" tabindex="0" aria-pressed="false"
                                                aria-label="Mostrar u ocultar las gráficas de detenidos">
                                                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center gap-2">
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-info-subtle text-info rounded fs-4">
                                                            <i class="ri-group-line"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="text-muted mb-0 small">Detenidos</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="card topic-card border h-100" data-topic="cateos"
                                                role="button" tabindex="0" aria-pressed="false"
                                                aria-label="Mostrar u ocultar las gráficas de cateos">
                                                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center gap-2">
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-warning-subtle text-warning rounded fs-4">
                                                            <i class="ri-home-gear-line"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="text-muted mb-0 small">Cateos</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="card topic-card border h-100" data-topic="expedientes" data-short="Exp."
                                                role="button" tabindex="0" aria-pressed="false"
                                                aria-label="Mostrar u ocultar las gráficas de expedientes">
                                                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center gap-2">
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-success-subtle text-success rounded fs-4">
                                                            <i class="ri-folder-3-line"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="text-muted mb-0 small">Expedientes</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="card topic-card border h-100" data-topic="medidas" data-short="Medidas"
                                                role="button" tabindex="0" aria-pressed="false"
                                                aria-label="Mostrar u ocultar las gráficas de medidas de protección">
                                                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center gap-2">
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-danger-subtle text-danger rounded fs-4">
                                                            <i class="ri-shield-check-line"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="text-muted mb-0 small">Medidas <br/>de Protección</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="card topic-card border h-100" data-topic="bienes" data-short="Bienes"
                                                role="button" tabindex="0" aria-pressed="false"
                                                aria-label="Mostrar u ocultar la gráfica de bienes asegurados">
                                                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center gap-2">
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-secondary-subtle text-secondary rounded fs-4">
                                                            <i class="ri-archive-2-line"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="text-muted mb-0 small">Bienes <br/>Asegurados</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="card topic-card border h-100" data-topic="droga" data-short="Droga"
                                                role="button" tabindex="0" aria-pressed="false"
                                                aria-label="Mostrar u ocultar la gráfica de droga asegurada">
                                                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center gap-2">
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-dark-subtle text-dark rounded fs-4">
                                                            <i class="ri-flask-line"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="text-muted mb-0 small">Droga <br/>asegurada (peso)</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-md-none mobile-filters-toggle mb-2">
                                        <button type="button" class="btn btn-sm btn-light border" id="toggle-filters-mobile"
                                            aria-expanded="true" aria-controls="filters-collapsible">
                                            <i class="ri-filter-3-line me-1"></i><span>Filtros</span>
                                            <i class="ri-arrow-up-s-line toggle-filters-icon ms-1"></i>
                                        </button>
                                        <div id="mobile-filters-summary" class="mobile-filters-summary d-none"></div>
                                    </div>
                                    <div id="filters-collapsible">
                                        <div class="row g-3" id="filters-row">
                                        <div class="col-12 col-sm-6 col-xl">
                                            <div class="form-group mb-0">
                                                <label for="periodo-filter" class="form-label small mb-1">Periodo:</label>
                                                <select id="periodo-filter" class="form-select form-select-sm">
                                                    <option value="lastweek">Últimos 7 días</option>
                                                    <option value="lastmonth">Últimos 30 días</option>
                                                    <option value="yeartodate">Último año</option>
                                                    <option value="historico">Histórico</option>
                                                    <option value="custom">Personalizado</option>
                                                </select>
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
                                                <label for="usuario-filter" class="form-label small mb-1">Usuario MP Digital:</label>
                                                <select id="usuario-filter" class="form-select form-select-sm">
                                                    <option value="">Cargando...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-xl mt-auto">
                                            <div class="form-group mb-0">
                                               <button class="btn btn-secondary" id="apply-filter">
                                                    <i class="ri-filter-2-fill"></i> Aplicar
                                                </button>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row g-3" id="filters-date-row">
                                        <div class="col-12 col-sm-2 col-xl-2">
                                            <div class="form-group mb-0">
                                                <label for="fecha-inicio" class="form-label small mb-1">Fecha Inicio:</label>
                                                <input type="date" id="fecha-inicio" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-2 col-xl-2">
                                            <div class="form-group mb-0">
                                                <label for="fecha-fin" class="form-label small mb-1">Fecha Fin:</label>
                                                <input type="date" id="fecha-fin" class="form-control form-control-sm">
                                            </div>
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

                                    <div id="metric-sections"></div>
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
        
        const METRICAS = [
            {
                key: 'denuncias',
                titulo: 'Denuncias',
                endpoint: '<?php echo base_url('InformeEjecutivo/getDenuncias'); ?>',
                unidad: ''
            },
            {
                key: 'detenidos',
                titulo: 'Detenidos',
                endpoint: '<?php echo base_url('InformeEjecutivo/getDetenidos'); ?>',
                unidad: ''
            },
            {
                key: 'cateos',
                titulo: 'Cateos',
                endpoint: '<?php echo base_url('InformeEjecutivo/getCateos'); ?>',
                unidad: ''
            },
            {
                key: 'expedientes',
                titulo: 'Expedientes',
                endpoint: '<?php echo base_url('InformeEjecutivo/getExpedientes'); ?>',
                unidad: ''
            },
            {
                key: 'medidas',
                titulo: 'Medidas de Protección',
                endpoint: '<?php echo base_url('InformeEjecutivo/getMedidas'); ?>',
                unidad: ''
            },
            {
                key: 'bienes',
                titulo: 'Bienes Asegurados',
                endpoint: '<?php echo base_url('InformeEjecutivo/getBienes'); ?>',
                unidad: ''
            },
            {
                key: 'droga',
                titulo: 'Droga Asegurada',
                endpoint: '<?php echo base_url('InformeEjecutivo/getDroga'); ?>',
                unidad: 'kg'
            }
        ];

        const PALETA = ['#E74C3C', '#E67E22', '#F39C12', '#27AE60', '#1ABC9C',
                        '#3498DB', '#9B59B6', '#34495E', '#95A5A6', '#7F8C8D',
                        '#D35400', '#C0392B', '#16A085', '#BDC3C7'];

        let topicCharts = {};
        let topicFilters = {};
        let currentTopic = null;
        let currentParams = [];

        const FILTROS_STORAGE_KEY = 'informeEjecutivoFiltros';
        let filtrosListos = false;

        $(document).ready(function () {
            if (window.Highcharts) {
                Highcharts.setOptions({
                    lang: { noData: 'Sin información para los filtros seleccionados' }
                });
            }

            restaurarFiltros();
            initFilterChosen();

            if (window.matchMedia('(max-width: 767.98px)').matches) {
                $('#filters-collapsible').hide();
                $('#mobile-filters-summary').removeClass('d-none');
                $('#toggle-filters-mobile')
                    .attr('aria-expanded', 'false')
                    .find('.toggle-filters-icon')
                    .attr('class', 'ri-arrow-down-s-line toggle-filters-icon ms-1');
                actualizarResumenFiltros();
            }

            const cargasFiltros = [
                loadDenunciasRegiones(),
                loadDenunciasMunicipios(),
                loadDenunciasUnidades(),
                loadUsuariosMpDigital()
            ];

            buildMetricSections();

            METRICAS.forEach(function (metrica) {
                topicCharts[metrica.key] = [function () { loadMetrica(metrica.key); }];
            });

            $('#periodo-filter').on('change', function () {
                actualizarFechasPeriodo($(this).val());
            });

            $('#periodo-filter, #region-filter, #municipio-filter, #unidades-investigacion-filter, #usuario-filter, #fecha-inicio, #fecha-fin')
                .on('change', function () {
                    guardarFiltros();
                    actualizarResumenFiltros();
                });

            $('#periodo-filter').trigger('change');

            $('#apply-filter').click(function () {
                construirParametros();
                guardarFiltros();
                actualizarResumenFiltros();
                applyTopicFilter();
            });

            $('#apply-filter').trigger('click');

            $.when.apply($, cargasFiltros).always(function () {
                filtrosListos = true;
                construirParametros();
                guardarFiltros();
                actualizarResumenFiltros();
                applyTopicFilter();
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

            $('#metric-sections').on('click', '.btn-detalles', function () {
                const $btn = $(this);
                const $tabla = $('#' + $btn.data('target'));

                if (!$tabla.length) {
                    return;
                }

                $tabla.stop(true, true).slideToggle(180, function () {
                    const abierta = $tabla.is(':visible');

                    $btn.attr('aria-expanded', abierta ? 'true' : 'false');
                    $btn.find('i').attr('class', (abierta ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line') + ' me-1');
                    $btn.find('span').text(abierta ? 'Ocultar detalles' : 'Detalles');
                });
            });

            $('#toggle-filters-mobile').on('click', function () {
                const $btn = $(this);
                const $filtros = $('#filters-collapsible');
                const $resumen = $('#mobile-filters-summary');
                const abierta = $filtros.is(':visible');

                if (abierta) {
                    $filtros.stop(true, true).slideUp(180);
                    actualizarResumenFiltros();
                    $resumen.removeClass('d-none');
                    $btn.attr('aria-expanded', 'false');
                    $btn.find('.toggle-filters-icon').attr('class', 'ri-arrow-down-s-line toggle-filters-icon ms-1');
                } else {
                    $resumen.addClass('d-none');
                    $filtros.stop(true, true).slideDown(180);
                    $btn.attr('aria-expanded', 'true');
                    $btn.find('.toggle-filters-icon').attr('class', 'ri-arrow-up-s-line toggle-filters-icon ms-1');
                }
            });

            sincronizarEtiquetasTopicos();
            window.addEventListener('resize', sincronizarEtiquetasTopicos);

            applyTopicFilter();
        });

        function construirParametros() {
            currentParams = [];

            const periodo   = $('#periodo-filter').val() || 'lastmonth';
            const region    = $('#region-filter').val();
            const municipio = $('#municipio-filter').val();
            const unidad    = $('#unidades-investigacion-filter').val();
            const usuario   = $('#usuario-filter').val();

            currentParams.push('periodo=' + encodeURIComponent(periodo));

            if (periodo !== 'historico') {
                const inicio = $('#fecha-inicio').val();
                const fin    = $('#fecha-fin').val();

                if (inicio) currentParams.push('fechaInicio=' + encodeURIComponent(inicio));
                if (fin)    currentParams.push('fechaFin=' + encodeURIComponent(fin));
            }

            if (region)    currentParams.push('region=' + encodeURIComponent(region));
            if (municipio) currentParams.push('municipio=' + encodeURIComponent(municipio));
            if (unidad)    currentParams.push('unidad=' + encodeURIComponent(unidad));
            if (usuario)   currentParams.push('usuario=' + encodeURIComponent(usuario));
        }

        function guardarFiltros() {
            if (!filtrosListos) {
                return;
            }

            try {
                localStorage.setItem(FILTROS_STORAGE_KEY, JSON.stringify({
                    periodo: $('#periodo-filter').val() || 'lastmonth',
                    region: $('#region-filter').val() || '',
                    municipio: $('#municipio-filter').val() || '',
                    unidad: $('#unidades-investigacion-filter').val() || '',
                    usuario: $('#usuario-filter').val() || '',
                    fechaInicio: $('#fecha-inicio').val() || '',
                    fechaFin: $('#fecha-fin').val() || ''
                }));
            } catch (error) {
                console.warn('No se pudieron guardar los filtros en localStorage:', error);
            }
        }

        function obtenerFiltrosGuardados() {
            try {
                const raw = localStorage.getItem(FILTROS_STORAGE_KEY);

                return raw ? JSON.parse(raw) : null;
            } catch (error) {
                console.warn('No se pudieron leer los filtros de localStorage:', error);
                return null;
            }
        }

        function restaurarFiltros() {
            const guardados = obtenerFiltrosGuardados();

            if (!guardados) {
                return;
            }

            if (guardados.periodo) {
                $('#periodo-filter').val(guardados.periodo);
            }

            if (guardados.fechaInicio) {
                $('#fecha-inicio').val(guardados.fechaInicio);
            }

            if (guardados.fechaFin) {
                $('#fecha-fin').val(guardados.fechaFin);
            }

            restaurarFiltrosSelects(guardados);
        }

        function restaurarFiltrosSelects(guardados) {
            if (!guardados) {
                return;
            }

            const mapa = {
                '#region-filter': guardados.region,
                '#municipio-filter': guardados.municipio,
                '#unidades-investigacion-filter': guardados.unidad,
                '#usuario-filter': guardados.usuario
            };

            Object.keys(mapa).forEach(function (selector) {
                const valor = mapa[selector];

                if (valor === undefined || valor === null || valor === '') {
                    return;
                }

                const $select = $(selector);
                const existe = $select.find('option').filter(function () {
                    return this.value === String(valor);
                }).length > 0;

                if (existe) {
                    $select.val(String(valor));
                    refreshFilterChosen(selector);
                }
            });
        }

        function actualizarResumenFiltros() {
            const $resumen = $('#mobile-filters-summary');

            if (!$resumen.length) {
                return;
            }

            const periodo = $('#periodo-filter').val();
            const periodoTexto = ($('#periodo-filter option:selected').text() || '').trim();
            const partes = [];

            if (periodo === 'custom') {
                const inicio = $('#fecha-inicio').val();
                const fin = $('#fecha-fin').val();
                partes.push('Personalizado: ' + (inicio || 'sin inicio') + ' a ' + (fin || 'sin fin'));
            } else if (periodoTexto) {
                partes.push(periodoTexto);
            }

            const filtros = [
                ['Delegación', $('#region-filter option:selected').text()],
                ['Municipio', $('#municipio-filter option:selected').text()],
                ['Unidad', $('#unidades-investigacion-filter option:selected').text()],
                ['Usuario', $('#usuario-filter option:selected').text()]
            ];

            filtros.forEach(function (filtro) {
                const etiqueta = filtro[0];
                const valor = (filtro[1] || '').trim();

                if (!valor || valor === 'Todas' || valor === 'Todos'
                    || valor === 'Cargando...' || valor === 'Error al cargar') {
                    return;
                }

                partes.push(etiqueta + ': ' + valor);
            });

            const texto = partes.join(' · ');
            $resumen.text(texto).attr('title', texto);
        }

        function sincronizarEtiquetasTopicos() {
            const esMobile = window.matchMedia('(max-width: 767.98px)').matches;

            $('#topic-cards .topic-card').each(function () {
                const $card = $(this);
                const $label = $card.find('.card-body p').first();

                if (!$label.length) {
                    return;
                }

                if ($card.data('textoLargo') === undefined) {
                    $card.data('textoLargo', $label.html());
                }

                const textoLargo = $card.data('textoLargo');
                const textoCorto = $card.attr('data-short') || $label.text().trim();

                $label.html(esMobile ? textoCorto : textoLargo);
            });
        }

        function actualizarFechasPeriodo(valor) {
            const esCustom = valor === 'custom';
            $('#filters-date-row').css('display', esCustom ? '' : 'none');

            if (valor === 'historico') {
                $('#fecha-inicio').val('');
                $('#fecha-fin').val('');
                return;
            }

            if (esCustom) {
                return;
            }

            const hoy = new Date();
            const fechaFin = formatearFechaLocal(hoy);
            let fechaInicio = null;

            if (valor === 'lastweek') {
                const inicio = new Date(hoy);
                inicio.setDate(hoy.getDate() - 7);
                fechaInicio = formatearFechaLocal(inicio);
            } else if (valor === 'lastmonth') {
                const inicio = new Date(hoy);
                inicio.setDate(hoy.getDate() - 30);
                fechaInicio = formatearFechaLocal(inicio);
            } else if (valor === 'yeartodate') {
                fechaInicio = `${hoy.getFullYear()}-01-01`;
            }

            if (fechaInicio !== null) {
                $('#fecha-inicio').val(fechaInicio);
                $('#fecha-fin').val(fechaFin);
            }
        }

        function formatearFechaLocal(date) {
            const year  = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day   = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        function metricByKey(key) {
            return METRICAS.find(function (metrica) { return metrica.key === key; });
        }

        function buildMetricSections() {
            const html = METRICAS.map(function (metrica) {
                return [
                    '<div class="chart-topic" data-topic="' + metrica.key + '">',
                    '<div class="row g-3 mb-3 metric-kpi-row">',
                    kpiCard(metrica, 'total', 'Total', 'primary', 'stack-line'),
                    kpiCard(metrica, 'activos', 'Activos', 'success', 'checkbox-circle-line'),
                    kpiCard(metrica, 'concluidos', 'Concluidos', 'secondary', 'checkbox-blank-circle-line'),
                    '</div>',
                    '<div class="row g-3">',
                    chartCard(metrica, 'comparativa', 'Periodo Actual vs Anterior'),
                    chartCard(metrica, 'region', 'Por Delegación / Región'),
                    chartCard(metrica, 'municipio', 'Por Municipio'),
                    chartCard(metrica, 'delito', 'Por Delito'),
                    '</div>',
                    '</div>'
                ].join('');
            }).join('');

            $('#metric-sections').html(html);
        }

        function kpiCard(metrica, campo, etiqueta, color, icono) {
            const unidad = metrica.unidad
                ? ' <small class="text-muted fs-6">' + metrica.unidad + '</small>'
                : '';

            return [
                '<div class="col-4">',
                '<div class="card border h-100">',
                '<div class="card-body d-flex align-items-center justify-content-between gap-3">',
                '<div>',
                '<p class="text-muted mb-1 small text-uppercase">' + etiqueta + '</p>',
                '<h3 class="mb-0"><span id="kpi-' + metrica.key + '-' + campo + '">0</span>' + unidad + '</h3>',
                '</div>',
                '<span class="avatar-title bg-' + color + '-subtle text-' + color + ' rounded fs-3" style="width:3rem;height:3rem;display:flex;align-items:center;justify-content:center;">',
                '<i class="ri-' + icono + '"></i>',
                '</span>',
                '</div>',
                '</div>',
                '</div>'
            ].join('');
        }

        function chartCard(metrica, dimension, titulo) {
            const esComparativa = dimension === 'comparativa';
            const antes = esComparativa
                ? '<div id="variacion-' + metrica.key + '" class="mb-3"></div>'
                : '';
            const leyenda = dimension === 'region'
                ? '<div id="legend-' + metrica.key + '-region" class="pie-legend mt-2"></div>'
                : '';
            const despues = esComparativa
                ? ''
                : [
                    '<button type="button" class="btn btn-sm btn-outline-secondary mt-3 w-100 btn-detalles"',
                    ' data-target="table-' + metrica.key + '-' + dimension + '"',
                    ' aria-expanded="false" aria-controls="table-' + metrica.key + '-' + dimension + '">',
                    '<i class="ri-arrow-down-s-line me-1"></i><span>Detalles</span>',
                    '</button>',
                    '<div id="table-' + metrica.key + '-' + dimension + '" class="mt-2 table-responsive chart-table-scroll" style="display:none;"></div>'
                ].join('');

            return [
                '<div class="col-12 col-md-6 col-xl-3">',
                '<div class="card border h-100">',
                '<div class="card-header bg-light">',
                '<h6 class="card-title mb-0">' + metrica.titulo + ' · ' + titulo + '</h6>',
                '</div>',
                '<div class="card-body">',
                antes,
                '<div id="chart-' + metrica.key + '-' + dimension + '" class="metric-chart"></div>',
                leyenda,
                despues,
                '</div>',
                '</div>',
                '</div>'
            ].join('');
        }

        function applyTopicFilter() {
            const activeTopic = $('#topic-cards .topic-card.active').first().data('topic');
            const hasSelection = activeTopic !== undefined && activeTopic !== '';

            currentTopic = hasSelection ? activeTopic : null;

            if (hasSelection && topicCharts[activeTopic]) {
                if (!topicFilters[activeTopic]) {
                    topicFilters[activeTopic] = [];
                }

                if (currentParams.join('&') !== topicFilters[activeTopic].join('&')) {
                    topicFilters[activeTopic] = currentParams.slice();
                    topicCharts[activeTopic].forEach(function (fn) { fn(); });

                    // Highcharts necesita recalcular su tamaño al mostrarse de nuevo.
                    window.setTimeout(reflowCharts, 50);
                }
            }

            $('.chart-topic').each(function () {
                const topic = String($(this).data('topic'));
                $(this).toggle(hasSelection && topic === String(activeTopic));
            });

            window.setTimeout(reflowCharts, 50);
        }

        function reflowCharts() {
            if (window.Highcharts && Highcharts.charts) {
                Highcharts.charts.forEach(function (chart) {
                    if (chart) chart.reflow();
                });
            }
        }

        function initFilterChosen() {
            if (!$.fn.chosen) {
                return;
            }

            $('#region-filter, #municipio-filter, #unidades-investigacion-filter, #usuario-filter, #periodo-filter').chosen({
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
            return $.ajax({
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
                    restaurarFiltrosSelects(obtenerFiltrosGuardados());
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar oficinas:', error);
                    $('#region-filter').html('<option value="">Error al cargar</option>');
                    refreshFilterChosen('#region-filter');
                }
            });
        }

        function loadDenunciasMunicipios() {
            return $.ajax({
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
                    restaurarFiltrosSelects(obtenerFiltrosGuardados());
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar municipios:', error);
                    $('#municipio-filter').html('<option value="">Error al cargar</option>');
                    refreshFilterChosen('#municipio-filter');
                }
            });
        }

        function loadDenunciasUnidades() {
            return $.ajax({
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
                    restaurarFiltrosSelects(obtenerFiltrosGuardados());
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar unidades de investigación:', error);
                    $('#unidades-investigacion-filter').html('<option value="">Error al cargar</option>');
                    refreshFilterChosen('#unidades-investigacion-filter');
                }
            });
        }

        function loadUsuariosMpDigital() {
            return $.ajax({
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
                    restaurarFiltrosSelects(obtenerFiltrosGuardados());
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar usuarios MP Digital:', error);
                    $('#usuario-filter').html('<option value="">Error al cargar</option>');
                    refreshFilterChosen('#usuario-filter');
                }
            });
        }

        function loadMetrica(key) {
            const metrica = metricByKey(key);

            if (!metrica) {
                return;
            }

            let url = metrica.endpoint;
            if (currentParams.length > 0) {
                url += '?' + currentParams.join('&');
            }

            showDashboardLoader();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json'
            }).done(function (response) {
                renderMetrica(metrica, response || {});
            }).fail(function (xhr, status, error) {
                console.error('Error al cargar ' + metrica.titulo + ':', error);
                renderMetrica(metrica, {});
            }).always(function () {
                hideDashboardLoader();
            });
        }

        function renderMetrica(metrica, data) {
            const unidad = data.unidad !== undefined ? data.unidad : metrica.unidad;

            setKpi(metrica, 'total', data.total, unidad);
            setKpi(metrica, 'activos', data.activos, unidad);
            setKpi(metrica, 'concluidos', data.concluidos, unidad);

            renderDimensionChart(metrica, 'delito', data.porDelito || [], 'treemap', unidad);
            renderDimensionChart(metrica, 'region', data.porRegion || [], 'pie', unidad);
            renderDimensionChart(metrica, 'municipio', data.porMunicipio || [], 'bar', unidad);
            renderComparativaChart(metrica, data.comparativa || {}, unidad);
        }

        function setKpi(metrica, campo, valor, unidad) {
            $('#kpi-' + metrica.key + '-' + campo).text(formatNumber(valor, unidad));
        }

        function formatNumber(valor, unidad) {
            const numero = Number(valor) || 0;

            if (unidad === 'kg') {
                return numero.toLocaleString('es-MX', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 3
                });
            }

            return Math.round(numero).toLocaleString('es-MX');
        }

        function renderDimensionChart(metrica, dimension, series, tipo, unidad) {
            const containerId = 'chart-' + metrica.key + '-' + dimension;

            const data = (series || []).map(function (item) {
                return {
                    name: item.name || 'Sin dato',
                    y: Number(item.total) || 0
                };
            }).filter(function (item) {
                return !esCategoriaSinDefinir(item.name, dimension);
            });

            const total = data.reduce(function (sum, item) { return sum + item.y; }, 0);
            const porcentaje = function (valor) {
                return total > 0 ? (valor / total) * 100 : 0;
            };

            const etiquetasDimension = {
                delito: 'Delito',
                region: 'Región / Delegación',
                municipio: 'Municipio'
            };

            createDimensionTable('table-' + metrica.key + '-' + dimension, data, {
                categoria: etiquetasDimension[dimension] || 'Categoría',
                unidad: unidad
            });

            if (tipo === 'pie') {
                Highcharts.chart(containerId, {
                    chart: { type: 'pie', backgroundColor: 'transparent', height: 360 },
                    title: { text: null },
                    colors: PALETA,
                    lang: { noData: 'Sin información para los filtros seleccionados' },
                    tooltip: {
                        pointFormat: '<b>{point.name}</b><br>{point.percentage:.1f}%'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            size: '95%',
                            borderWidth: 1,
                            borderColor: '#ffffff',
                            dataLabels: {
                                enabled: true,
                                distance: -30,
                                formatter: function () {
                                    const pct = total > 0 ? (this.point.y / total) * 100 : 0;
                                    const nombre = this.point.name || '';

                                    if (pct >= 12 && nombre.length <= 12) {
                                        return '<b>' + nombre + '</b><br>' + pct.toFixed(1) + '%';
                                    }

                                    if (pct >= 5) {
                                        return pct.toFixed(1) + '%';
                                    }

                                    return '';
                                },
                                style: {
                                    fontSize: '9px',
                                    fontWeight: '600',
                                    color: '#ffffff',
                                    textOutline: '1px rgba(0,0,0,.5)'
                                }
                            },
                            showInLegend: false
                        }
                    },
                    legend: { enabled: false },
                    series: [{
                        name: metrica.titulo,
                        colorByPoint: true,
                        data: data
                    }],
                    credits: { enabled: false },
                    exporting: { enabled: true },
                    noData: { style: { fontWeight: 'normal', fontSize: '13px', color: '#666' } }
                });

                createPieLegend('legend-' + metrica.key + '-region', data, total);

                return;
            }

            if (tipo === 'treemap') {
                Highcharts.chart(containerId, {
                    chart: { type: 'treemap', backgroundColor: 'transparent', height: 360 },
                    title: { text: null },
                    colors: PALETA,
                    lang: { noData: 'Sin información para los filtros seleccionados' },
                    tooltip: {
                        headerFormat: '',
                        pointFormat: '<b>{point.name}</b><br>{point.value:.1f}%'
                    },
                    series: [{
                        type: 'treemap',
                        name: metrica.titulo,
                        colorByPoint: true,
                        layoutAlgorithm: 'squarified',
                        data: data.map(function (item) {
                            return { name: item.name, value: Number(porcentaje(item.y).toFixed(3)) };
                        }),
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b><br>{point.value:.1f}%',
                            style: { fontSize: '10px', textOutline: 'none' }
                        },
                        levels: [{
                            level: 1,
                            borderWidth: 2,
                            dataLabels: {
                                enabled: true,
                                align: 'left',
                                verticalAlign: 'top'
                            }
                        }]
                    }],
                    credits: { enabled: false },
                    exporting: { enabled: true },
                    noData: { style: { fontWeight: 'normal', fontSize: '13px', color: '#666' } }
                });

                return;
            }

            Highcharts.chart(containerId, {
                chart: { type: 'bar', backgroundColor: 'transparent', height: 360 },
                title: { text: null },
                colors: PALETA,
                lang: { noData: 'Sin información para los filtros seleccionados' },
                xAxis: {
                    categories: data.map(function (item) { return item.name; }),
                    crosshair: true,
                    labels: { style: { fontSize: '10px' } }
                },
                yAxis: {
                    min: 0,
                    allowDecimals: true,
                    title: { text: null },
                    labels: { format: '{value}%', style: { fontSize: '10px' } }
                },
                legend: { enabled: false },
                tooltip: {
                    headerFormat: '<span style="font-size:13px;font-weight:bold">{point.key}</span><br/>',
                    pointFormat: 'Porcentaje: <b>{point.y:.1f}%</b>'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 5,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:.1f}%',
                            style: { fontSize: '10px', fontWeight: 'bold' }
                        }
                    }
                },
                series: [{
                    name: metrica.titulo,
                    colorByPoint: true,
                    data: data.map(function (item) {
                        return { name: item.name, y: Number(porcentaje(item.y).toFixed(3)) };
                    })
                }],
                credits: { enabled: false },
                exporting: { enabled: true },
                noData: { style: { fontWeight: 'normal', fontSize: '13px', color: '#666' } }
            });
        }

        function createPieLegend(containerId, data, total) {
            const container = document.getElementById(containerId);

            if (!container) {
                return;
            }

            if (!data || !data.length) {
                container.innerHTML = '';
                return;
            }

            container.innerHTML = data.map(function (item, index) {
                const pct = total > 0 ? (item.y / total) * 100 : 0;
                const nombre = escapeHtml(item.name);
                const nombreAttr = nombre.replace(/"/g, '&quot;');

                return '<div class="pie-legend-item">'
                    + '<span class="pie-legend-dot" style="background-color:' + PALETA[index % PALETA.length] + ';"></span>'
                    + '<span class="pie-legend-name" title="' + nombreAttr + '">' + nombre + '</span>'
                    + '<span class="pie-legend-value">' + pct.toFixed(1) + '%</span>'
                    + '</div>';
            }).join('');
        }

        function esCategoriaSinDefinir(nombre, dimension) {
            if (dimension !== 'delito' && dimension !== 'municipio') {
                return false;
            }

            const valor = String(nombre || '').trim().toLowerCase();
            const excluidas = dimension === 'delito'
                ? ['sin delito', 'sin dato']
                : ['sin municipio', 'sin dato'];

            return excluidas.indexOf(valor) !== -1;
        }

        function createDimensionTable(containerId, data, options) {
            const container = document.getElementById(containerId);

            if (!container) {
                return;
            }

            const config    = options || {};
            const categoria = config.categoria || 'Categoría';
            const esPeso    = config.unidad === 'kg';

            if (!data || !data.length) {
                container.innerHTML = '<div class="text-center text-muted small py-3">Sin información para los filtros seleccionados.</div>';
                return;
            }

            const total  = data.reduce(function (sum, item) { return sum + (Number(item.y) || 0); }, 0);
            const maximo = Math.max.apply(null, data.map(function (item) { return item.y; }));

            let html = '<table class="chart-data-table">'
                + '<thead><tr>'
                + '<th style="width:20px;"></th>'
                + '<th>' + categoria + '</th>'
                + '<th class="text-right">' + (esPeso ? 'Total (kg)' : 'Total') + '</th>'
                + '<th class="text-right">%</th>'
                + '</tr></thead><tbody>';

            data.forEach(function (item, index) {
                const porcentaje = total > 0 ? ((item.y / total) * 100).toFixed(1) : '0.0';
                const esMaximo   = item.y === maximo;
                const estiloFila = esMaximo ? 'background-color:#d4edda;font-weight:bold;' : '';
                const color      = PALETA[index % PALETA.length];

                html += '<tr style="' + estiloFila + '">'
                    + '<td style="padding:6px 4px;"><div style="width:12px;height:12px;background-color:' + color + ';border-radius:2px;display:inline-block;"></div></td>'
                    + '<td>' + escapeHtml(item.name) + (esMaximo ? ' ⭐' : '') + '</td>'
                    + '<td class="text-right">' + formatNumber(item.y, config.unidad) + '</td>'
                    + '<td class="text-right">' + porcentaje + '%</td>'
                    + '</tr>';
            });

            html += '</tbody>'
                + '<tfoot><tr style="font-weight:bold;background-color:#e9ecef;">'
                + '<td></td>'
                + '<td>Total</td>'
                + '<td class="text-right">' + formatNumber(total, config.unidad) + '</td>'
                + '<td class="text-right">100.0%</td>'
                + '</tr></tfoot></table>';

            container.innerHTML = html;
        }

        function escapeHtml(value) {
            return $('<div>').text(value == null ? '' : String(value)).html();
        }

        function renderComparativaChart(metrica, comparativa, unidad) {
            const containerId = 'chart-' + metrica.key + '-comparativa';
            const esPeso = unidad === 'kg';
            const formatoValor = esPeso ? '{point.y:,.3f} kg' : '{point.y:,.0f}';
            const actual = Number(comparativa.actual) || 0;
            const anterior = (comparativa.anterior === null || comparativa.anterior === undefined)
                ? null
                : (Number(comparativa.anterior) || 0);

            const categorias = ['Periodo actual'];
            const valores = [{ y: actual, color: '#0d6efd' }];

            if (anterior !== null) {
                categorias.push('Periodo anterior');
                valores.push({ y: anterior, color: '#adb5bd' });
            }

            const subtitulo = anterior === null
                ? 'Histórico: no existe un periodo anterior comparable'
                : (comparativa.etiquetaActual || 'Periodo actual') + ' vs ' + (comparativa.etiquetaAnterior || 'Periodo anterior');

            Highcharts.chart(containerId, {
                chart: { type: 'column', backgroundColor: 'transparent', height: 360 },
                title: { text: null },
                subtitle: { text: subtitulo, style: { fontSize: '10px' } },
                lang: { noData: 'Sin información para los filtros seleccionados' },
                xAxis: {
                    categories: categorias,
                    labels: { style: { fontSize: '11px' } }
                },
                yAxis: {
                    min: 0,
                    allowDecimals: esPeso,
                    title: { text: null },
                    labels: { style: { fontSize: '10px' } }
                },
                legend: { enabled: false },
                tooltip: {
                    headerFormat: '<span style="font-size:13px;font-weight:bold">{point.key}</span><br/>',
                    pointFormat: 'Total: <b>' + formatoValor + '</b>'
                },
                plotOptions: {
                    column: {
                        borderRadius: 5,
                        dataLabels: {
                            enabled: true,
                            format: formatoValor,
                            style: { fontSize: '11px', fontWeight: 'bold' }
                        }
                    }
                },
                series: [{
                    name: metrica.titulo,
                    colorByPoint: true,
                    data: valores
                }],
                credits: { enabled: false },
                exporting: { enabled: true },
                noData: { style: { fontWeight: 'normal', fontSize: '13px', color: '#666' } }
            });

            renderVariacionCard(metrica, comparativa, unidad);
        }

        function renderVariacionCard(metrica, comparativa, unidad) {
            const container = document.getElementById('variacion-' + metrica.key);

            if (!container) {
                return;
            }

            const actual = Number(comparativa.actual) || 0;
            const anterior = (comparativa.anterior === null || comparativa.anterior === undefined)
                ? null
                : (Number(comparativa.anterior) || 0);

            if (anterior === null) {
                container.innerHTML = '<div class="card border-0 bg-secondary-subtle">'
                    + '<div class="card-body d-flex align-items-center gap-3 py-2">'
                    + '<i class="ri-history-line fs-3 text-secondary"></i>'
                    + '<div>'
                    + '<p class="text-muted mb-0 small text-uppercase">Variación vs periodo anterior</p>'
                    + '<p class="mb-0 text-secondary">Histórico: no existe un periodo anterior comparable.</p>'
                    + '</div>'
                    + '</div>'
                    + '</div>';
                return;
            }

            const diferencia = actual - anterior;
            const subio = diferencia > 0;
            const bajo = diferencia < 0;
            const color = subio ? 'success' : (bajo ? 'danger' : 'secondary');
            const icono = subio ? 'arrow-up-line' : (bajo ? 'arrow-down-line' : 'subtract-line');
            const signo = subio ? '+' : (bajo ? '-' : '');
            const titulo = subio
                ? 'Aumentó vs periodo anterior'
                : (bajo ? 'Disminuyó vs periodo anterior' : 'Sin variación vs periodo anterior');

            const porcentaje = anterior > 0
                ? (diferencia / anterior) * 100
                : (diferencia === 0 ? 0 : null);
            const porcentajeTexto = porcentaje === null
                ? 'N/A'
                : signo + Math.abs(porcentaje).toLocaleString('es-MX', {
                    minimumFractionDigits: 1,
                    maximumFractionDigits: 1
                }) + '%';
            const diferenciaTexto = signo + formatNumber(Math.abs(diferencia), unidad)
                + (unidad === 'kg' ? ' kg' : '');

            container.innerHTML = '<div class="card border-0 bg-' + color + '-subtle">'
                + '<div class="card-body d-flex align-items-center justify-content-between gap-3 py-2">'
                + '<div>'
                + '<p class="text-muted mb-1 small text-uppercase">' + titulo + '</p>'
                + '<h4 class="mb-0 text-' + color + '">' + porcentajeTexto
                + ' <span class="fs-6">(' + diferenciaTexto + ')</span></h4>'
                + '<p class="mb-0 text-muted small">Actual: ' + formatNumber(actual, unidad)
                + (unidad === 'kg' ? ' kg' : '') + ' · Anterior: ' + formatNumber(anterior, unidad)
                + (unidad === 'kg' ? ' kg' : '') + '</p>'
                + '</div>'
                + '<i class="ri-' + icono + ' fs-3 text-' + color + '"></i>'
                + '</div>'
                + '</div>';
        }
    </script>
</body>
</html>
