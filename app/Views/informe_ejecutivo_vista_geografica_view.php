<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>
    <?php echo $head; ?>
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <style>
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

        @media (max-width: 575.98px) {
            .form-control-sm,
            .form-select-sm {
                min-height: 38px;
            }
        }

        /* Capas del mapa */
        .map-layer-toggle {
            cursor: pointer;
            padding: .45rem .55rem;
            border: 1px solid #dee2e6;
            border-radius: .5rem;
            background: #fff;
            transition: background-color .15s ease-in-out, border-color .15s ease-in-out;
        }

        .map-layer-toggle:hover {
            background-color: #f8f9fa;
            border-color: #adb5bd;
        }

        .map-layer-toggle .form-check-input {
            cursor: pointer;
        }

        .map-layer-toggle .layer-count {
            min-width: 2.25rem;
            font-variant-numeric: tabular-nums;
        }

        .geographic-map-wrapper {
            position: relative;
            display: flex;
            width: 100%;
            min-height: 420px;
        }

        #map {
            flex: 1 1 auto;
            width: 100%;
            min-height: 420px;
            overflow: hidden;
            border-radius: .5rem;
        }

        @media (max-width: 767.98px) {
            .geographic-map-wrapper,
            #map {
                min-height: 60vh;
            }
        }

        .map-overlay {
            position: absolute;
            inset: 0;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(255, 255, 255, .86);
            border-radius: .5rem;
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

        input[type="date"]:hover {
            background-color: #f5f7fa;
            border-color: #0B2341;
        }

        input[type="date"]:focus {
            background-color: #fff;
            border-color: #0B2341;
            box-shadow: 0 0 0 2px rgba(11, 35, 65, 0.15);
            outline: none;
        }

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
                                    <h5 class="card-title mb-0">Vista Geográfica</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-4 col-lg-3 col-xl-2">
                                            <div class="map-layer-toggles mb-3">
                                                <div class="small text-muted text-uppercase fw-semibold mb-2">Tipos de registro</div>

                                                <label class="map-layer-toggle d-flex align-items-center gap-2" for="capa-denuncias">
                                                    <input class="form-check-input m-0 flex-shrink-0" type="checkbox" id="capa-denuncias" value="denuncias" checked>
                                                    <i class="ri-map-pin-fill text-primary fs-5 flex-shrink-0"></i>
                                                    <span class="flex-grow-1 small fw-semibold">Denuncias</span>
                                                    <span class="badge rounded-pill bg-primary-subtle text-primary layer-count" id="count-denuncias">--</span>
                                                </label>

                                                <label class="map-layer-toggle d-flex align-items-center gap-2 mt-2" for="capa-detenidos">
                                                    <input class="form-check-input m-0 flex-shrink-0" type="checkbox" id="capa-detenidos" value="detenidos" checked>
                                                    <i class="ri-map-pin-fill text-danger fs-5 flex-shrink-0"></i>
                                                    <span class="flex-grow-1 small fw-semibold">Detenidos</span>
                                                    <span class="badge rounded-pill bg-danger-subtle text-danger layer-count" id="count-detenidos">--</span>
                                                </label>

                                                <label class="map-layer-toggle d-flex align-items-center gap-2 mt-2" for="capa-cateos">
                                                    <input class="form-check-input m-0 flex-shrink-0" type="checkbox" id="capa-cateos" value="cateos" checked>
                                                    <i class="ri-map-pin-fill text-success fs-5 flex-shrink-0"></i>
                                                    <span class="flex-grow-1 small fw-semibold">Cateos</span>
                                                    <span class="badge rounded-pill bg-success-subtle text-success layer-count" id="count-cateos">--</span>
                                                </label>
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
                                                    <div class="col-12">
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

                                                    <div class="col-12" id="filters-date-row" style="display: none;">
                                                        <div class="row g-2">
                                                            <div class="col-12">
                                                                <div class="form-group mb-0">
                                                                    <label for="fecha-inicio" class="form-label small mb-1">Fecha Inicio:</label>
                                                                    <input type="date" id="fecha-inicio" class="form-control form-control-sm">
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group mb-0">
                                                                    <label for="fecha-fin" class="form-label small mb-1">Fecha Fin:</label>
                                                                    <input type="date" id="fecha-fin" class="form-control form-control-sm">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group mb-0">
                                                            <label for="region-filter" class="form-label small mb-1">Delegación:</label>
                                                            <select id="region-filter" class="form-select form-select-sm">
                                                                <option value="">Cargando...</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group mb-0">
                                                            <label for="municipio-filter" class="form-label small mb-1">Municipio:</label>
                                                            <select id="municipio-filter" class="form-select form-select-sm">
                                                                <option value="">Cargando...</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group mb-0">
                                                            <label for="delito-filter" class="form-label small mb-1">Delito:</label>
                                                            <select id="delito-filter" class="form-select form-select-sm">
                                                                <option value="">Cargando...</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group mb-0">
                                                            <label for="unidades-investigacion-filter" class="form-label small mb-1">Unidad de investigación:</label>
                                                            <select id="unidades-investigacion-filter" class="form-select form-select-sm">
                                                                <option value="">Cargando...</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group mb-0">
                                                            <label for="usuario-filter" class="form-label small mb-1">Usuario MP Digital:</label>
                                                            <select id="usuario-filter" class="form-select form-select-sm">
                                                                <option value="">Cargando...</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group mb-0 d-grid gap-2">
                                                            <button type="button" class="btn btn-secondary" id="apply-filter">
                                                                <i class="ri-filter-2-fill me-1"></i> Aplicar
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary" id="clear-filters">
                                                                <i class="ri-refresh-line me-1"></i> Limpiar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-8 col-lg-9 col-xl-10 d-flex">
                                            <div class="geographic-map-wrapper flex-grow-1">
                                                <div id="map"></div>
                                                <div id="geographic-status" class="map-overlay d-none">
                                                    <div class="text-center text-muted px-3">
                                                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                                                        <div class="small">Cargando mapa...</div>
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
        const FILTROS_STORAGE_KEY = 'informeEjecutivoFiltros';
        let filtrosListos = false;
        let currentParams = [];

        const GOOGLE_MAPS_CONFIGURADO = <?= !empty($googleMapsApiKey) ? 'true' : 'false' ?>;
        const MAPA_CENTRO_COAHUILA = { lat: 25.4232, lng: -101.0053 };
        const MAPA_ZOOM_INICIAL = 8;
        const CAPAS_MAPA = {
            denuncias: { etiqueta: 'Denuncias', color: '#0d6efd' },
            detenidos: { etiqueta: 'Detenidos', color: '#dc3545' },
            cateos: { etiqueta: 'Cateos', color: '#198754' }
        };
        let mapa = null;
        let infoWindowMapa = null;
        let datosMapa = null;
        let marcadoresMapa = { denuncias: [], detenidos: [], cateos: [] };
        let solicitudMapa = null;

        window.initMap = function () {
            inicializarMapa();
        };

        $(document).ready(function () {
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
                loadDelitosMpDigital(),
                loadDenunciasUnidades(),
                loadUsuariosMpDigital()
            ];

            $('#periodo-filter').on('change', function () {
                actualizarFechasPeriodo($(this).val());
            });

            $('#periodo-filter, #region-filter, #municipio-filter, #delito-filter, #unidades-investigacion-filter, #usuario-filter, #fecha-inicio, #fecha-fin')
                .on('change', function () {
                    guardarFiltros();
                    actualizarResumenFiltros();
                });

            $('#periodo-filter').trigger('change');

            $('#apply-filter').on('click', function () {
                aplicarFiltros();
            });

            $('#clear-filters').on('click', function () {
                limpiarFiltros();
            });

            $('#capa-denuncias, #capa-detenidos, #capa-cateos').on('change', function () {
                $(document).trigger('informeEjecutivo:capasCambiadas', [obtenerCapasActivas()]);
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

            $(document).on('informeEjecutivo:filtrosAplicados', function (event, params) {
                cargarPuntosMapa(params);
            });

            $(document).on('informeEjecutivo:capasCambiadas', function () {
                renderizarPuntosMapa();
            });

            $.when.apply($, cargasFiltros).always(function () {
                filtrosListos = true;
                aplicarFiltros();
            });

            actualizarResumenFiltros();

            if (!GOOGLE_MAPS_CONFIGURADO) {
                mostrarEstadoMapa(
                    '<div class="text-center text-muted px-3">' +
                    '<i class="ri-map-pin-line fs-2 d-block mb-2"></i>' +
                    '<div class="small">Configura <code>google.maps.api_key</code> en <code>.env</code> para mostrar el mapa.</div>' +
                    '</div>'
                );
            } else {
                window.setTimeout(function () {
                    if (!mapa && typeof google === 'undefined') {
                        mostrarEstadoMapa(
                            '<div class="text-center text-danger px-3">' +
                            '<i class="ri-error-warning-line fs-3 d-block mb-1"></i>' +
                            '<div class="small">No se pudo cargar Google Maps. Verifica la API key y la conexión.</div>' +
                            '</div>'
                        );
                    }
                }, 10000);
            }
        });

        function aplicarFiltros() {
            construirParametros();
            guardarFiltros();
            actualizarResumenFiltros();

            // Punto de integración para que la vista geográfica cargue sus datos.
            $(document).trigger('informeEjecutivo:filtrosAplicados', [currentParams.slice()]);
        }

        function limpiarFiltros() {
            filtrosListos = false;

            $('#periodo-filter').val('lastweek');
            $('#region-filter').val('');
            $('#municipio-filter').val('');
            $('#delito-filter').val('');
            $('#unidades-investigacion-filter').val('');
            $('#usuario-filter').val('');
            $('#fecha-inicio').val('');
            $('#fecha-fin').val('');

            ['#periodo-filter', '#region-filter', '#municipio-filter', '#delito-filter', '#unidades-investigacion-filter', '#usuario-filter']
                .forEach(refreshFilterChosen);

            actualizarFechasPeriodo('lastweek');
            actualizarResumenFiltros();

            filtrosListos = true;
            aplicarFiltros();
            localStorage.removeItem(FILTROS_STORAGE_KEY);
        }

        function obtenerCapasActivas() {
            return {
                denuncias: $('#capa-denuncias').is(':checked'),
                detenidos: $('#capa-detenidos').is(':checked'),
                cateos: $('#capa-cateos').is(':checked')
            };
        }

        function construirParametros() {
            currentParams = [];

            const periodo   = $('#periodo-filter').val() || 'lastmonth';
            const region    = $('#region-filter').val();
            const municipio = $('#municipio-filter').val();
            const delito    = $('#delito-filter').val();
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
            if (delito)    currentParams.push('delito=' + encodeURIComponent(delito));
            if (unidad)    currentParams.push('unidad=' + encodeURIComponent(unidad));
            if (usuario)   currentParams.push('usuario=' + encodeURIComponent(usuario));

            return currentParams;
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
                    delito: $('#delito-filter').val() || '',
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
                '#delito-filter': guardados.delito,
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
                ['Delito', $('#delito-filter option:selected').text()],
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

        function initFilterChosen() {
            if (!$.fn.chosen) {
                return;
            }

            $('#region-filter, #municipio-filter, #delito-filter, #unidades-investigacion-filter, #usuario-filter, #periodo-filter').chosen({
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

        function loadDenunciasRegiones() {
            return $.ajax({
                url: '<?php echo base_url('estadisticas/getOficinasGestiones'); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let options = '<option value="">Todas</option>';
                    (response || []).forEach(function(item) {
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
                    (response || []).forEach(function(municipio) {
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

        function loadDelitosMpDigital() {
            return $.ajax({
                url: '<?php echo base_url('InformeEjecutivo/getDelitosMpDigital'); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const $select = $('#delito-filter').empty();
                    $select.append($('<option>', { value: '', text: 'Todos' }));

                    (response || []).forEach(function(delito) {
                        const nombre = String(delito.nombre || '').trim();

                        if (nombre === '') {
                            return;
                        }

                        $select.append($('<option>', { value: nombre, text: nombre }));
                    });

                    refreshFilterChosen('#delito-filter');
                    restaurarFiltrosSelects(obtenerFiltrosGuardados());
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar delitos de MP Digital:', error);
                    $('#delito-filter').html('<option value="">Error al cargar</option>');
                    refreshFilterChosen('#delito-filter');
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
                    (response || []).forEach(function(unidad) {
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

        function inicializarMapa() {
            if (mapa || typeof google === 'undefined') {
                return;
            }

            const contenedor = document.getElementById('map');

            if (!contenedor) {
                return;
            }

            mapa = new google.maps.Map(contenedor, {
                center: MAPA_CENTRO_COAHUILA,
                zoom: MAPA_ZOOM_INICIAL,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                clickableIcons: false
            });

            infoWindowMapa = new google.maps.InfoWindow();
            ocultarEstadoMapa();
            renderizarPuntosMapa();

            if (window.ResizeObserver) {
                const contenedorMapa = document.querySelector('.geographic-map-wrapper');

                if (contenedorMapa) {
                    new ResizeObserver(function () {
                        if (mapa) {
                            google.maps.event.trigger(mapa, 'resize');
                        }
                    }).observe(contenedorMapa);
                }
            }

            $(window).on('resize.mapaGeografico', function () {
                if (mapa) {
                    google.maps.event.trigger(mapa, 'resize');
                }
            });
        }

        function cargarPuntosMapa(params) {
            const query = (params || currentParams || []).join('&');

            if (solicitudMapa) {
                solicitudMapa.abort();
            }

            if (GOOGLE_MAPS_CONFIGURADO) {
                mostrarCargandoMapa();
            }

            solicitudMapa = $.ajax({
                url: '<?php echo base_url('InformeEjecutivo/getPuntosMapa'); ?>' + (query ? '?' + query : ''),
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    datosMapa = response || {};
                    actualizarContadoresCapas(datosMapa);
                    renderizarPuntosMapa();

                    if (mapa) {
                        ocultarEstadoMapa();
                    }
                },
                error: function (xhr, status) {
                    if (status === 'abort') {
                        return;
                    }

                    datosMapa = null;
                    actualizarContadoresCapas(null);

                    if (GOOGLE_MAPS_CONFIGURADO) {
                        mostrarEstadoMapa(
                            '<div class="text-center text-danger px-3">' +
                            '<i class="ri-error-warning-line fs-3 d-block mb-1"></i>' +
                            '<div class="small">No se pudieron cargar los puntos del mapa.</div>' +
                            '</div>'
                        );
                    }
                }
            });
        }

        function renderizarPuntosMapa() {
            if (!mapa || typeof google === 'undefined') {
                return;
            }

            Object.keys(marcadoresMapa).forEach(function (metrica) {
                marcadoresMapa[metrica].forEach(function (marcador) {
                    marcador.setMap(null);
                });
                marcadoresMapa[metrica] = [];
            });

            const capas = obtenerCapasActivas();
            const bounds = new google.maps.LatLngBounds();
            let totalPuntos = 0;

            Object.keys(capas).forEach(function (metrica) {
                if (!capas[metrica]) {
                    return;
                }

                const puntos = (datosMapa && datosMapa[metrica] && datosMapa[metrica].puntos) || [];

                puntos.forEach(function (punto) {
                    const marcador = new google.maps.Marker({
                        position: { lat: Number(punto.lat), lng: Number(punto.lng) },
                        map: mapa,
                        title: construirTituloPunto(punto, metrica),
                        icon: crearIconoPin(CAPAS_MAPA[metrica].color)
                    });

                    marcador.addListener('click', function () {
                        infoWindowMapa.setContent(construirInfoPunto(punto, metrica));
                        infoWindowMapa.open({ map: mapa, anchor: marcador });
                    });

                    marcadoresMapa[metrica].push(marcador);
                    bounds.extend(marcador.getPosition());
                    totalPuntos++;
                });
            });

            if (totalPuntos === 0) {
                mapa.setCenter(MAPA_CENTRO_COAHUILA);
                mapa.setZoom(MAPA_ZOOM_INICIAL);
            } else if (totalPuntos === 1) {
                mapa.setCenter(bounds.getCenter());
                mapa.setZoom(14);
            } else {
                mapa.fitBounds(bounds, { padding: 40 });
            }

        }

        function actualizarContadoresCapas(datos) {
            ['denuncias', 'detenidos', 'cateos'].forEach(function (metrica) {
                const info = datos && datos[metrica] ? datos[metrica] : null;
                const total = info ? info.total : '--';
                const registros = info ? info.registros : '--';
                const texto = info
                    ? (total === registros ? String(total) : total + '/' + registros)
                    : '--';

                $('#count-' + metrica)
                    .text(texto)
                    .attr('title', info
                        ? total + ' puntos con coordenadas de ' + registros + ' registros'
                        : 'Sin datos');
            });
        }

        function crearIconoPin(color) {
            const svg = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="40" viewBox="0 0 32 40">' +
                '<path d="M16 1C7.7 1 1 7.7 1 16c0 11.2 15 23 15 23s15-11.8 15-23C31 7.7 24.3 1 16 1z" ' +
                'fill="' + color + '" stroke="#ffffff" stroke-width="2"/>' +
                '<circle cx="16" cy="16" r="5" fill="#ffffff"/>' +
                '</svg>';

            return {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
                scaledSize: new google.maps.Size(32, 40),
                anchor: new google.maps.Point(16, 40)
            };
        }

        function construirTituloPunto(punto, metrica) {
            const etiqueta = CAPAS_MAPA[metrica] ? CAPAS_MAPA[metrica].etiqueta : metrica;
            const partes = [etiqueta, punto.folio, punto.delito].filter(function (valor) {
                return valor !== null && valor !== undefined && String(valor).trim() !== '';
            });

            return partes.join(' · ');
        }

        function construirInfoPunto(punto, metrica) {
            const definicion = CAPAS_MAPA[metrica] || { etiqueta: metrica, color: '#0B2341' };
            const filas = [
                ['Folio', punto.folio],
                ['Delito', punto.delito],
                ['Fecha', formatearFechaMapa(punto.fecha)],
                ['Región', punto.region],
                ['Municipio', punto.municipio],
                ['Dirección', [punto.calle, punto.colonia].filter(Boolean).join(', ')]
            ].filter(function (fila) {
                return fila[1] !== null && fila[1] !== undefined && String(fila[1]).trim() !== '';
            });

            let html = '<div style="min-width:220px;max-width:320px;font-size:13px;">';
            html += '<div style="font-weight:700;color:' + definicion.color + ';margin-bottom:6px;">' +
                escapeHtml(definicion.etiqueta) + '</div>';

            filas.forEach(function (fila) {
                html += '<div style="margin-bottom:3px;"><strong>' + escapeHtml(fila[0]) + ':</strong> ' +
                    escapeHtml(fila[1]) + '</div>';
            });

            html += '</div>';

            return html;
        }

        function formatearFechaMapa(fecha) {
            if (!fecha) {
                return '';
            }

            const texto = String(fecha).substring(0, 10);
            const partes = texto.split('-');

            if (partes.length === 3) {
                return partes[2] + '/' + partes[1] + '/' + partes[0];
            }

            return texto;
        }

        function escapeHtml(valor) {
            return $('<div>').text(valor === null || valor === undefined ? '' : String(valor)).html();
        }

        function mostrarCargandoMapa() {
            mostrarEstadoMapa(
                '<div class="text-center text-muted px-3">' +
                '<div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>' +
                '<div class="small">Cargando puntos...</div>' +
                '</div>'
            );
        }

        function mostrarEstadoMapa(html) {
            $('#geographic-status').removeClass('d-none').html(html);
        }

        function ocultarEstadoMapa() {
            $('#geographic-status').addClass('d-none').empty();
        }
    </script>

    <?php if (!empty($googleMapsApiKey)): ?>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=<?= rawurlencode($googleMapsApiKey) ?>&libraries=visualization&loading=async&callback=initMap"
        async defer></script>
    <?php endif; ?>
</body>

</html>
