<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>
    <?php echo $head; ?>
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
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
                                    <h5 class="card-title mb-0">Motivos de Visita</h5>
                                    <button class="btn btn-primary btn-sm" id="btnNuevo">
                                        <i class="mdi mdi-plus"></i> Nuevo motivo
                                    </button>
                                </div>
                                <div class="card-body">
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nombre</th>
                                                <th>Opciones</th>
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

    <!-- Modal -->
    <div class="modal fade" id="modalMotivo" tabindex="-1" aria-labelledby="modalMotivoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMotivoLabel">Motivo de visita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="motivoId">
                    <div class="mb-3">
                        <label for="motivoNombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="motivoNombre" maxlength="200" placeholder="Ej. Denuncia, Consulta...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarMotivo">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm delete modal -->
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ¿Deseas eliminar este motivo de visita?
                    <input type="hidden" id="eliminarId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</button>
                </div>
            </div>
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

    <script type="text/javascript">
        var table;
        var baseUrl = '<?= base_url(); ?>';

        $(document).ready(function () {
            table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { url: baseUrl + 'motivos_visita/loaddata', type: 'GET' },
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-MX.json' }
            });

            // Nuevo
            $('#btnNuevo').click(function () {
                $('#motivoId').val('');
                $('#motivoNombre').val('');
                $('#modalMotivoLabel').text('Nuevo motivo de visita');
                $('#modalMotivo').modal('show');
            });

            // Editar
            $('#datatable tbody').on('click', '.btn-edit', function () {
                $('#motivoId').val($(this).data('id'));
                $('#motivoNombre').val($(this).data('nombre'));
                $('#modalMotivoLabel').text('Editar motivo de visita');
                $('#modalMotivo').modal('show');
            });

            // Eliminar
            $('#datatable tbody').on('click', '.btn-delete', function () {
                $('#eliminarId').val($(this).data('id'));
                $('#modalEliminar').modal('show');
            });

            // Guardar (crear / editar)
            $('#btnGuardarMotivo').click(function () {
                var id = $('#motivoId').val();
                var nombre = $('#motivoNombre').val().trim();
                if (!nombre) { toastr.error('El nombre es requerido'); return; }

                var url = id ? baseUrl + 'motivos_visita/editar' : baseUrl + 'motivos_visita/guardar';
                var payload = { nombre: nombre };
                if (id) payload.id = id;

                $.post(url, payload, function (res) {
                    var r = (res);
                    if (r.success) {
                        $('#modalMotivo').modal('hide');
                        table.ajax.reload();
                        toastr.success('Guardado correctamente');
                    } else {
                        toastr.error(r.message || 'Ocurrió un error al guardar');
                    }
                });
            });

            // Confirmar eliminar
            $('#btnConfirmarEliminar').click(function () {
                var id = $('#eliminarId').val();
                $.post(baseUrl + 'motivos_visita/eliminar', { id: id }, function (res) {
                    var r = (res);
                    if (r.success) {
                        $('#modalEliminar').modal('hide');
                        table.ajax.reload();
                        toastr.success('Eliminado correctamente');
                    } else {
                        toastr.error('Ocurrió un error al eliminar');
                    }
                });
            });
        });
    </script>
</body>
</html>
