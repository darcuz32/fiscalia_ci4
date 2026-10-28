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
            .form-control-sm,
            .form-select-sm {
                min-height: 38px;
            }
            .topic-card .topic-total {
                font-size: 1.35rem;
            }
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
                                    <div class="row g-3">
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
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3 mb-2" id="topic-cards">
                                        <div class="col-12 col-sm-6 col-md-4 col-xl-3">
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
                                        <div class="col-12 col-sm-6 col-md-4 col-xl-3">
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
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            loadDenunciasRegiones();
            loadDenunciasMunicipios();
            loadDenunciasUnidades();

            $('#fecha-inicio, #fecha-fin, #region-filter, #municipio-filter, #unidades-investigacion-filter').on('change', function () {
                loadDenunciasChart();
                loadDetenidosChart();
                loadDetenidosDelitosChart();
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
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar oficinas:', error);
                    $('#region-filter').html('<option value="">Error al cargar</option>');
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
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar municipios:', error);
                    $('#municipio-filter').html('<option value="">Error al cargar</option>');
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
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar unidades de investigación:', error);
                    $('#unidades-investigacion-filter').html('<option value="">Error al cargar</option>');
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

            if (inicio)    params.push('fechaInicio=' + encodeURIComponent(inicio));
            if (fin)       params.push('fechaFin=' + encodeURIComponent(fin));
            if (region)    params.push('region=' + encodeURIComponent(region));
            if (municipio) params.push('municipio=' + encodeURIComponent(municipio));
            if (unidad)    params.push('unidad=' + encodeURIComponent(unidad));
            return params;
        }

        function loadDenunciasChart() {
            let url = '<?php echo base_url('InformeEjecutivo/getDenunciasPorInstrumento'); ?>';
            const params = getFiltrosParams();
            if (params.length > 0) url += '?' + params.join('&');

            $('#denuncias-table-container').html('<div class="text-center text-muted small py-3">Cargando...</div>');

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    createDenunciasChart(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar denuncias por instrumento:', error);
                    $('#denuncias-table-container').html('<div class="text-center text-danger small py-3">No fue posible cargar la información.</div>');
                }
            });
        }

        function loadDetenidosChart() {
            let url = '<?php echo base_url('InformeEjecutivo/getDetenidosPorRangoEdad'); ?>';
            const params = getFiltrosParams();
            if (params.length > 0) url += '?' + params.join('&');

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    createDetenidosChart(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar detenidos por rango de edad:', error);
                }
            });
        }

        function loadDetenidosDelitosChart() {
            let url = '<?php echo base_url('InformeEjecutivo/getDetenidosPorDelito'); ?>';
            const params = getFiltrosParams();
            if (params.length > 0) url += '?' + params.join('&');

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    createDetenidosDelitosChart(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar detenidos por delito:', error);
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

        function createDenunciasTable(containerId, data) {
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
                            <th>Instrumento</th>
                            <th class="text-right">Denuncias</th>
                            <th class="text-right">%</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach((item) => {
                const percentage = total > 0 ? ((item.y / total) * 100).toFixed(1) : '0.0';
                const isMax = item.y === maxValue;
                const rowStyle = isMax ? 'background-color: #d4edda; font-weight: bold;' : '';
                tableHTML += `
                    <tr style="${rowStyle}">
                        <td style="padding: 6px 4px;">
                            <div style="width: 12px; height: 12px; background-color: ${item.color}; border-radius: 2px; display: inline-block;"></div>
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
    </script>
</body>
</html>
