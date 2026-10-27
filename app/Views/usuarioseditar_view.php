<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>
    <?php echo $head; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <style>
        .user-editor .card-header { padding: 1rem 1.25rem; }
        .user-editor .card-body { padding: 1.25rem; }
        .user-editor .section-card {
            height: 100%;
            margin-bottom: 0;
            border: 1px solid var(--vz-border-color);
            box-shadow: none;
        }
        .user-editor .section-card .card-body { padding: 1rem; }
        .user-editor .section-title {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1rem;
            color: var(--vz-heading-color);
            font-size: .925rem;
            font-weight: 600;
        }
        .user-editor .section-title i { color: var(--vz-primary); font-size: 1.15rem; }
        .user-editor .form-label { margin-bottom: .4rem; font-weight: 500; }
        .user-editor .bootstrap-select { width: 100% !important; }
        .user-editor .module-group,
        .user-editor .permission-group {
            height: 100%;
            padding: .8rem;
            border: 1px solid var(--vz-border-color);
            border-radius: var(--vz-border-radius);
            background: var(--vz-light-bg-subtle);
        }
        .user-editor .module-group > .form-check:first-child .form-check-label { font-weight: 600; }
        .user-editor .module-child { margin-left: 1rem; }
        .user-editor .module-separator { margin: .65rem 0 .35rem 1rem; }
        .user-editor .form-check-input:disabled + .form-check-label { opacity: .65; }
        .user-editor .signature-preview img { max-width: 220px; max-height: 120px; object-fit: contain; }
        .user-editor .mini-profile-option { padding: .75rem; border: 1px solid var(--vz-border-color); border-radius: var(--vz-border-radius); }
        .user-editor .mini-profile-option:focus-within { border-color: var(--vz-primary); box-shadow: 0 0 0 .15rem rgba(var(--vz-primary-rgb), .15); }
        .user-editor #mini-profile-section legend { font-size: .92rem; }
        .user-editor #mini_profile_fiscal_general_delegado + label { color: #2f9e44; }
        .user-editor #mini_profile_fiscal_especializado_coordinador + label { color: #6f42c1; }
        .user-editor .form-actions { gap: .5rem; }
        @media (max-width: 767.98px) {
            .user-editor .card-header { align-items: flex-start !important; }
            .user-editor .card-header .btn { width: 100%; margin-top: .75rem; }
            .user-editor .form-actions { width: 100%; }
            .user-editor .form-actions .btn { flex: 1; }
        }
    </style>
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
                        <div class="col-12">
                            <form method="POST" action="<?php echo base_url(); ?>usuarios/actualizar/<?php echo $usuario->Id; ?>" id="form1" autocomplete="off">
                                <div class="card user-editor">
                                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                                        <div>
                                            <h5 class="card-title mb-1"><i class="mdi mdi-account-edit-outline me-1"></i>Editar usuario</h5>
                                            <p class="text-muted mb-0">Actualice los datos, accesos y alcances operativos del usuario.</p>
                                        </div>
                                        <a href="<?= base_url(); ?>usuarios" class="btn btn-light btn-sm">
                                            <i class="mdi mdi-arrow-left me-1"></i>Volver al listado
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <section class="card section-card" aria-labelledby="datos-usuario-title">
                                                    <div class="card-body">
                                                        <h6 class="section-title" id="datos-usuario-title"><i class="mdi mdi-account-outline"></i>Datos del usuario</h6>
                                                        <div class="row g-3">
                                                            <div class="col-md-6 col-xl-4">
                                                                <label for="name" class="form-label">Nombre</label>
                                                                <input type="text" id="name" name="name" class="form-control" required="" value="<?php echo $usuario->name; ?>">
                                                            </div>
                                                            <div class="col-md-6 col-xl-4">
                                                                <label for="email" class="form-label">Correo electrónico</label>
                                                                <input type="email" id="email" name="email" class="form-control" autocomplete="off" required="" value="<?php echo $usuario->email; ?>">
                                                            </div>
                                                            <div class="col-md-6 col-xl-4">
                                                                <label for="telefono" class="form-label">Teléfono</label>
                                                                <input type="text" id="telefono" name="telefono" class="form-control" maxlength="50" value="<?php echo isset($usuario->telefono) ? $usuario->telefono : ''; ?>" placeholder="Opcional">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="password" class="form-label">Contraseña</label>
                                                                <input type="password" id="password" name="password" class="form-control" autocomplete="new-password" placeholder="Dejar en blanco para no cambiar" value="">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="rfc" class="form-label">RFC</label>
                                                                <input type="text" id="rfc" name="rfc" class="form-control" value="<?= $usuario->rfc; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </section>
                                            </div>

                                            <div class="col-12 firma">
                                                <section class="card section-card" aria-labelledby="firma-title">
                                                    <div class="card-body">
                                                        <h6 class="section-title" id="firma-title"><i class="mdi mdi-draw"></i>Firma</h6>
                                                        <div class="row g-3 align-items-center">
                                                            <div class="col-md-6">
                                                                <label for="firma" class="form-label">Archivo PNG</label>
                                                                <input class="form-control" type="file" id="firma" accept="image/png">
                                                                <input class="form-control" hidden name="firma">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div id="filePreview" class="signature-preview">
                                                                    <?= (isset($usuario->firma) && $usuario->firma != "") ? '<img src="'.$usuario->firma. '" alt="Firma actual" class="img-fluid">' : ""; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </section>
                                            </div>

                                            <div class="col-12">
                                                <section class="card section-card" aria-labelledby="datos-laborales-title">
                                                    <div class="card-body">
                                                        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
                                                            <h6 class="section-title mb-0" id="datos-laborales-title"><i class="mdi mdi-badge-account-outline"></i>Datos laborales</h6>
                                                            <div class="form-check form-switch mb-0">
                                                                <input class="form-check-input" type="checkbox" name="is_empleado" value="1" id="is_empleado" <?= ($usuario->idprofile == 3) ? 'checked' : ''; ?>>
                                                                <label class="form-check-label fw-semibold" for="is_empleado">Es Empleado (acceso a Comisiones)</label>
                                                            </div>
                                                        </div>
                                                        <div class="row g-3">
                                                            <div class="col-md-6 col-xl-3 campos-empleado">
                                                                <label for="numero" class="form-label">Número de empleado</label>
                                                                <input type="number" id="numero" name="numero" class="form-control" required="" value="<?= $usuario->numero; ?>">
                                                            </div>
                                                            <div class="col-md-6 col-xl-3 campos-empleado">
                                                                <label for="correo" class="form-label">Correo institucional</label>
                                                                <input type="email" id="correo" name="correoinstitucional" class="form-control" required="" value="<?= $usuario->correoinstitucional; ?>">
                                                            </div>
                                                            <div class="col-md-6 col-xl-3 campos-empleado">
                                                                <label for="passwordcorreo" class="form-label">Contraseña correo</label>
                                                                <div class="input-group">
                                                                    <input type="password" id="passwordcorreo" name="passwordcorreo" class="form-control" autocomplete="new-password" placeholder="Dejar en blanco para no cambiar">
                                                                    <button type="button" id="mostrarPassword" class="btn btn-outline-secondary" title="Ver contraseña guardada">
                                                                        <i class="mdi mdi-eye-outline"></i><span class="visually-hidden">Ver contraseña guardada</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-xl-3 campos-empleado">
                                                                <label for="idprofile" class="form-label">Nivel</label>
                                                                <select class="form-control" name="nivel" id="idprofile">
                                                                    <?php
                                                                    $niveles = [1,2,3];
                                                                    foreach ($niveles as $key => $value) { ?>
                                                                        <option <?= ($value == $usuario->nivel) ? "selected" : ""; ?> value="<?php echo $value; ?>"><?= $value; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </section>
                                            </div>

                                            <div class="col-12" id="modules-section">
                                                <section class="card section-card" aria-labelledby="modulos-title">
                                                    <div class="card-body">
                                                        <h6 class="section-title" id="modulos-title"><i class="mdi mdi-view-grid-plus-outline"></i>Módulos de acceso</h6>
                                                        <div class="row g-2" id="modulos-checks">
                                                            <?php
                                                            $_parent_subs = [
                                                                'gestiones'    => ['gestiones_administracion'],
                                                                'comisiones'   => ['comisiones_reporte', 'comisiones_solicitudes'],
                                                                'estadisticas' => ['estadisticas_gestiones', 'estadisticas_constancias', 'estadisticas_registro'],
                                                                'mpdigital'    => [
                                                                    'multas',
                                                                    'multas_dashboard',
                                                                    'detenciones',
                                                                    'expedientes',
                                                                    'denuncias',
                                                                    'denuncias_medidas',
                                                                    'bienes_asegurados',
                                                                    'cateos',
                                                                    'audiencias',
                                                                    'actuaciones_ministeriales',
                                                                    'exportacion_datos',
                                                                    '__sep:Cat&aacute;logos',
                                                                    'regiones',
                                                                    'detenidos_corporaciones',
                                                                    'unidades_investigacion',
                                                                    'multa_delitos',
                                                                    'multa_motivos_salida',
                                                                    'ofendidos',
                                                                ],
                                                            ];
                                                            $_all_subs = array_merge(...array_values($_parent_subs));
                                                            $_modulos_map = [];
                                                            foreach ($modulos as $_m) { $_modulos_map[$_m->name] = $_m; }
                                                            foreach ($modulos as $modulo):
                                                                if (in_array($modulo->name, $_all_subs)) continue;
                                                                $_subs = isset($_parent_subs[$modulo->name]) ? $_parent_subs[$modulo->name] : [];
                                                            ?>
                                                            <div class="<?= $modulo->name === 'mpdigital' ? 'col-12' : 'col-md-6 col-xl-4'; ?>">
                                                                <div class="module-group">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input module-check" type="checkbox"
                                                                               name="modulos[]" value="<?= $modulo->name; ?>"
                                                                               id="mod_<?= $modulo->name; ?>"
                                                                               <?= in_array($modulo->name, $usuario->modulos) ? 'checked' : ''; ?>>
                                                                        <label class="form-check-label" for="mod_<?= $modulo->name; ?>"><?= $modulo->label; ?></label>
                                                                    </div>
                                                                    <?php if ($modulo->name === 'mpdigital'): ?>
                                                                    <div class="row g-3 mt-1">
                                                                        <div class="col-md-6">
                                                                            <div class="small text-uppercase text-muted fw-semibold mb-2 ms-3">Operación</div>
                                                                    <?php endif; ?>
                                                                    <?php foreach ($_subs as $_sub_name):
                                                                        if (strncmp($_sub_name, '__sep:', 6) === 0):
                                                                            $_sep_label = html_entity_decode(substr($_sub_name, 6), ENT_QUOTES, 'UTF-8');
                                                                            if ($modulo->name === 'mpdigital'): ?>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="small text-uppercase text-muted fw-semibold mb-2 ms-3"><?= htmlspecialchars($_sep_label); ?></div>
                                                                            <?php else: ?>
                                                                    <div class="module-separator">
                                                                        <small class="text-muted fw-semibold"><?= htmlspecialchars($_sep_label); ?></small>
                                                                    </div>
                                                                            <?php endif;
                                                                            continue;
                                                                        endif;
                                                                        if (!isset($_modulos_map[$_sub_name])) continue;
                                                                        $_sub = $_modulos_map[$_sub_name];
                                                                    ?>
                                                                    <div class="form-check module-child mt-1">
                                                                        <input class="form-check-input module-check" type="checkbox"
                                                                               name="modulos[]" value="<?= $_sub->name; ?>"
                                                                               id="mod_<?= $_sub->name; ?>"
                                                                               <?= in_array($_sub->name, $usuario->modulos) ? 'checked' : ''; ?>>
                                                                        <label class="form-check-label" for="mod_<?= $_sub->name; ?>">&#8627; <?= $_sub->label; ?></label>
                                                                    </div>
                                                                    <?php endforeach; ?>
                                                                    <?php if ($modulo->name === 'mpdigital'): ?>
                                                                        </div>
                                                                    </div>
                                                                    <?php endif; ?>
                                                                    <?php if ($modulo->name === 'mpdigital'): ?>
                                                                    <div id="multas-section" class="mt-3 pt-3 border-top">
                                                                        <div class="fw-semibold mb-3">Configuración de MP Digital</div>
                                                                        <fieldset id="mini-profile-section" class="mb-3" aria-describedby="multas-mini-profile-help">
                                                                            <legend class="form-label mb-2">Mini-perfil de MP Digital <span class="text-danger" aria-hidden="true">*</span></legend>
                                                                            <p id="multas-mini-profile-help" class="text-muted small mb-2">Este alcance se aplica únicamente a Detenciones, Expedientes y Denuncias.</p>
                                                                            <div class="row g-2">
                                                                                <?php foreach ($multas_mini_profiles as $code => $profile): ?>
                                                                                <div class="col-lg-4">
                                                                                    <div class="form-check mini-profile-option h-100">
                                                                                        <input class="form-check-input mini-profile-radio" type="radio" name="multas_mini_profile" value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>" id="mini_profile_<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>" <?= $usuario->multas_mini_profile === $code ? 'checked' : '' ?> aria-describedby="mini_profile_desc_<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>">
                                                                                        <label class="form-check-label fw-semibold" for="mini_profile_<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($profile['label'], ENT_QUOTES, 'UTF-8') ?></label>
                                                                                        <div id="mini_profile_desc_<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>" class="text-muted small mt-1"><?= htmlspecialchars($profile['description'], ENT_QUOTES, 'UTF-8') ?></div>
                                                                                    </div>
                                                                                </div>
                                                                                <?php endforeach; ?>
                                                                            </div>
                                                                        </fieldset>
                                                                        <div class="row g-3">
                                                                            <div class="col-lg-4 select-multa-delegacion">
                                                                                <label for="region_multa" class="form-label">Región de Multas</label>
                                                                                <select class="form-control" name="region_multa[]" id="region_multa" data-live-search="true" multiple data-actions-box="true" data-selected-text-format="count > 3">
                                                                                    <?php foreach ($multa_regiones as $key => $value) { ?>
                                                                                        <option <?= (in_array($value->id, $usuario->multa_regiones)) ? "selected" : ""; ?> value="<?= $value->id; ?>"><?= $value->nombre; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-lg-4 select-unidades-multa" style="display:none">
                                                                                <label for="unidades_investigacion_multa" class="form-label">Unidad de investigación</label>
	                                                                                <select class="form-control" name="unidades_investigacion_multa[]" id="unidades_investigacion_multa" data-live-search="true" <?= in_array($usuario->multas_mini_profile, ['fiscal_especializado_coordinador', 'director'], true) ? 'multiple data-actions-box="true" data-selected-text-format="count > 3"' : '' ?>>
                                                                                    <?php foreach ($unidades_investigacion_multa_catalogo as $u): ?>
                                                                                        <option value="<?= (int)$u['id'] ?>" <?= in_array((int)$u['id'], $unidades_investigacion_multa_seleccionadas) ? 'selected' : '' ?>><?= htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                                <small class="text-muted">Unidades dentro de las regiones seleccionadas arriba.</small>
                                                                            </div>
                                                                            <div class="col-lg-4 select-dashboard-regiones">
                                                                                <label for="dashboard_regiones" class="form-label">Región de Estadística</label>
                                                                                <select class="form-control" name="dashboard_regiones[]" id="dashboard_regiones" data-live-search="true" multiple data-actions-box="true" data-selected-text-format="count > 3">
                                                                                    <?php foreach ($dashboard_regiones as $key => $value) { ?>
                                                                                        <option <?= (in_array($value->id, $usuario->dashboard_regiones)) ? "selected" : ""; ?> value="<?= $value->id; ?>"><?= $value->nombre; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                                <small class="text-muted">Regiones visibles en la Estadística de Multas (las mismas que Detenidos).</small>
                                                                            </div>
                                                                            <div class="col-12 permissions-multas">
                                                                                <div class="permission-group">
                                                                                    <div class="fw-semibold mb-2">Permisos especiales</div>
                                                                                    <div class="row g-2">
                                                                                        <div class="col-md-6">
                                                                                            <div class="form-check">
                                                                                                <input class="form-check-input" type="checkbox" name="special_permissions[]" value="mostrar_total_multas" id="perm_mostrar_total_multas" <?= in_array('mostrar_total_multas', $usuario->special_permissions) ? 'checked' : ''; ?> disabled>
                                                                                                <label class="form-check-label" for="perm_mostrar_total_multas">Mostrar total de multas en estadística</label>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-6 perm-enviar-expedientes" style="display:none">
                                                                                            <div class="form-check">
                                                                                                <input class="form-check-input" type="checkbox" name="special_permissions[]" value="enviar_expedientes" id="perm_enviar_expedientes" <?= in_array('enviar_expedientes', $usuario->special_permissions) ? 'checked' : ''; ?>>
                                                                                                <label class="form-check-label" for="perm_enviar_expedientes">Puede enviar expedientes</label>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <div class="form-check">
                                                                                                <input class="form-check-input" type="checkbox" name="special_permissions[]" value="marcar_multas_pagadas" id="perm_marcar_multas_pagadas" <?= in_array('marcar_multas_pagadas', $usuario->special_permissions) ? 'checked' : ''; ?> disabled>
                                                                                                <label class="form-check-label" for="perm_marcar_multas_pagadas">Marcar como pagado</label>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </section>
                                            </div>

                                            <div class="col-xl-6" id="adscripcion-section">
                                                <section class="card section-card" aria-labelledby="adscripcion-title">
                                                    <div class="card-body">
                                                        <h6 class="section-title" id="adscripcion-title"><i class="mdi mdi-office-building-marker-outline"></i>Adscripción</h6>
                                                        <div class="row g-3">
                                                            <div class="col-lg-6 select-oficina">
                                                                <label for="oficina" class="form-label">Oficina</label>
                                                                <select class="form-control" name="oficina[]" id="oficina" data-live-search="true" multiple data-actions-box="true" data-selected-text-format="count > 3">
                                                                    <?php foreach ($oficinas as $key => $value) { ?>
                                                                        <option <?= (in_array($value->Id, $usuario->oficinas)) ? "selected" : ""; ?> value="<?= $value->Id; ?>"><?= $value->oficina; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-6 select-region">
                                                                <label for="region" class="form-label">Delegacion(es)</label>
                                                                <select class="form-control" name="region[]" id="region" data-live-search="true" multiple data-actions-box="true" data-selected-text-format="count > 3">
                                                                    <?php foreach ($regiones as $key => $value) { ?>
                                                                        <option <?= (in_array($value->region, $usuario->regiones)) ? "selected" : ""; ?> value="<?= $value->region; ?>"><?= $value->region; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </section>
                                            </div>

                                            <div class="col-xl-6" id="special-permissions-section">
                                                <section class="card section-card" aria-labelledby="permisos-title">
                                                    <div class="card-body">
                                                        <h6 class="section-title" id="permisos-title"><i class="mdi mdi-shield-key-outline"></i>Permisos especiales</h6>
                                                        <div class="permisos-constancias" style="display:none">
                                                            <div class="permission-group">
                                                                <div class="fw-semibold mb-2">Constancias</div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" name="special_permissions[]" value="marcar_pago_manual" id="perm_marcar_pago_manual" <?= in_array('marcar_pago_manual', $usuario->special_permissions) ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="perm_marcar_pago_manual">Puede marcar pagos de constancias de hechos manualmente</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="permisos-auditoria-expedientes" style="display:none">
                                                            <div class="permission-group">
                                                                <div class="fw-semibold mb-2">Expedientes</div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" name="special_permissions[]" value="consultar_auditoria_expedientes" id="perm_consultar_auditoria_expedientes" <?= in_array('consultar_auditoria_expedientes', $usuario->special_permissions) ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="perm_consultar_auditoria_expedientes">Puede consultar la auditoría global de expedientes</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="permisos-organigrama-mpdigital" style="display:none">
                                                            <div class="permission-group">
                                                                <div class="fw-semibold mb-2">MP Digital</div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" name="special_permissions[]" value="ver_organigrama_mpdigital" id="perm_ver_organigrama_mpdigital" <?= in_array('ver_organigrama_mpdigital', $usuario->special_permissions) ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="perm_ver_organigrama_mpdigital">Puede consultar el organigrama de MP Digital</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </section>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex flex-column flex-sm-row justify-content-end form-actions">
                                        <a href="<?= base_url(); ?>usuarios" class="btn btn-light">Cancelar</a>
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                                            <i class="mdi mdi-content-save-outline me-1"></i>Guardar cambios
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
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

    <div class="modal fade" id="modalPassword" tabindex="-1" role="dialog" aria-labelledby="modalPasswordLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPasswordLabel">Desencriptar contraseña</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-10">
                            
                            <div class="mb-3">
                                <label for="comprobante" class="form-label">Contraseña para desencriptar</label>
                                <input class="form-control" type="password" id="passwordDesencriptacion">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <br/>
                            <button type="button" class="btn btn-primary" id="desencriptarPassword">Mostrar</button>
                        </div>
                        <div class="col-md-12">
                            <p id="textPassword"></p>
                        </div>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div> -->
            </div>
        </div>
    </div>

    <?php echo $js; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/i18n/defaults-es_ES.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('select[name="region[]"], select[name="oficina[]"], select[name="region_multa[]"], select[name="dashboard_regiones[]"], select[name="unidades_investigacion_multa[]"]').selectpicker();
            $('#form1').submit(function(event) {
                event.preventDefault();
                if (!validateMiniProfile()) return;
                var $btn = $('button[type="submit"]');
                if ($btn.prop('disabled')) return;
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i>Guardando...');
                // ponytail: safety timeout — if the server never responds, re-enable after 15s
                var safety = setTimeout(function() {
                    if ($btn.prop('disabled')) {
                        $btn.prop('disabled', false).html(originalHtml);
                        toastr.error('La solicitud tardó demasiado. Intente de nuevo.');
                    }
                }, 15000);
                $.post($('#form1').prop('action'), {data: $('#form1').serialize()}, function(data, textStatus, xhr) {
                    clearTimeout(safety);
                    if(data=='repetido'){
                        toastr.error('El usuario ya fué registrado');
                        $btn.prop('disabled', false).html(originalHtml);
                    }else if(data=='ok'){
                        toastr.success('Usuario registrado correctamente');
                        setTimeout(function(){
                            window.location.href='<?= base_url(); ?>usuarios';
                        },2000);
                    } else if (String(data).indexOf('error|') === 0) {
                        toastr.error(String(data).substring(6));
                        $btn.prop('disabled', false).html(originalHtml);
                    } else {
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                }).fail(function(){
                    clearTimeout(safety);
                    $btn.prop('disabled', false).html(originalHtml);
                    toastr.error('Algo anda mal');
                });
            });

            $('select[name=idprofile]').change(function(){
                // legacy handler removed — replaced by module checkboxes
            });

            function syncMpDigitalHierarchy(changed) {
                var children = ['multas', 'multas_dashboard', 'detenciones', 'expedientes', 'denuncias', 'denuncias_medidas', 'bienes_asegurados', 'cateos', 'audiencias', 'actuaciones_ministeriales', 'regiones', 'detenidos_corporaciones', 'unidades_investigacion', 'multa_delitos', 'multa_motivos_salida', 'ofendidos'];
                if (changed.value === 'mpdigital' && !changed.checked) {
                    children.forEach(function(name) { $('#mod_' + name).prop('checked', false); });
                } else if (children.includes(changed.value) && changed.checked) {
                    $('#mod_mpdigital').prop('checked', true);
                }
            }

            function updateModuleVisibility() {
                var mods = [];
                $('.module-check:checked').each(function(){ mods.push($(this).val()); });
                var isEmpleado = $('#is_empleado').is(':checked');
                var usesMiniProfile = !isEmpleado && ['detenciones', 'expedientes', 'denuncias', 'multas', 'bienes_asegurados', 'cateos', 'actuaciones_ministeriales'].some(function(m){ return mods.includes(m); });
                var miniProfile = $('input[name="multas_mini_profile"]:checked').val() || '';

                if(isEmpleado) {
                    $('#modules-section').hide();
                    $('.campos-empleado').show();
                    $('.campos-empleado').find('input').prop('required', true);
                } else {
                    $('#modules-section').show();
                    $('.campos-empleado').hide();
                    $('.campos-empleado').find('input').prop('required', false);
                }

                if(!isEmpleado && (mods.includes('registro') || mods.includes('constancias') || mods.includes('registro_mp'))) {
                    $('.select-region').show();
                    $('.select-region select').prop('required', true);
                } else {
                    $('.select-region').hide();
                    $('.select-region select').prop('required', false);
                }

                if(!isEmpleado && mods.includes('gestiones')) {
                    $('.select-oficina').show();
                } else {
                    $('.select-oficina').hide();
                }

                if(!isEmpleado && (mods.includes('multas') || usesMiniProfile)) {
                    $('.select-multa-delegacion').show();
                } else {
                    $('.select-multa-delegacion').hide();
                }
	                var requiresUnit = usesMiniProfile && ['fiscal_especializado_coordinador','ministerio_publico','director'].includes(miniProfile);
	                var multipleUnits = ['fiscal_especializado_coordinador','director'].includes(miniProfile);
	                var $unidades = $('#unidades_investigacion_multa');
	                if ($unidades.prop('multiple') !== multipleUnits) {
	                    var selectedUnits = $unidades.val() || [];
	                    selectedUnits = Array.isArray(selectedUnits) ? selectedUnits : [selectedUnits];
	                    $unidades.prop('multiple', multipleUnits).val(multipleUnits ? selectedUnits : selectedUnits.slice(0, 1));
	                    $unidades.selectpicker('destroy').selectpicker();
	                }
	                $('.select-unidades-multa label').text(multipleUnits ? 'Unidades de investigación' : 'Unidad de investigación');
                $('.select-unidades-multa').toggle(requiresUnit);
                $('#mini-profile-section').toggle(usesMiniProfile);
                $('.mini-profile-radio').prop('required', usesMiniProfile);
                $('.perm-enviar-expedientes').toggle(usesMiniProfile && miniProfile === 'fiscal_especializado_coordinador');
                if (miniProfile !== 'fiscal_especializado_coordinador') $('#perm_enviar_expedientes').prop('checked', false);
                var canGlobalAudit = (!isEmpleado && mods.includes('usuarios')) || (usesMiniProfile && miniProfile === 'fiscal_especializado_coordinador');
                $('.permisos-auditoria-expedientes').toggle(canGlobalAudit);
                if (!canGlobalAudit) $('#perm_consultar_auditoria_expedientes').prop('checked', false);
                var canViewOrganigrama = !isEmpleado && mods.includes('mpdigital');
                $('.permisos-organigrama-mpdigital').toggle(canViewOrganigrama);
                if (!canViewOrganigrama) $('#perm_ver_organigrama_mpdigital').prop('checked', false);

                if(!isEmpleado && mods.includes('multas_dashboard')) {
                    $('.select-dashboard-regiones').show();
                } else {
                    $('.select-dashboard-regiones').hide();
                }

                if(!isEmpleado && mods.includes('gestiones')) {
                    $('.firma').show();
                    var hasExisting = $('#filePreview').html().trim() !== '';
                    $('.firma').find('input[type=file]').prop('required', !hasExisting);
                } else {
                    $('.firma').hide();
                    $('.firma').find('input').prop('required', false);
                }

                if(!isEmpleado && mods.includes('constancias')) {
                    $('.permisos-constancias').show();
                } else {
                    $('.permisos-constancias').hide();
                    $('.permisos-constancias input[type=checkbox]').prop('checked', false);
                }

                $('#perm_marcar_multas_pagadas').prop('disabled', isEmpleado || !mods.includes('multas'));
                if(isEmpleado || !mods.includes('multas')) $('#perm_marcar_multas_pagadas').prop('checked', false);

                $('#perm_mostrar_total_multas').prop('disabled', isEmpleado || !mods.includes('multas_dashboard'));
                if(isEmpleado || !mods.includes('multas_dashboard')) $('#perm_mostrar_total_multas').prop('checked', false);

                $('#adscripcion-section').toggle(!isEmpleado && (
                    mods.includes('gestiones') || mods.includes('registro') ||
                    mods.includes('constancias') || mods.includes('registro_mp')
                ));
                $('#multas-section').toggle(!isEmpleado && (mods.includes('multas') || mods.includes('multas_dashboard') || usesMiniProfile));
	                $('#special-permissions-section').toggle(!isEmpleado && (mods.includes('constancias') || canGlobalAudit || canViewOrganigrama));
            }

            $('.module-check').on('change', function() {
                syncMpDigitalHierarchy(this);
                updateModuleVisibility();
            });
            $('input[name="multas_mini_profile"]').on('change', updateModuleVisibility);
            $('#is_empleado').on('change', updateModuleVisibility);
            updateModuleVisibility();

            function validateMiniProfile() {
                var mods = $('.module-check:checked').map(function(){ return this.value; }).get();
                var scoped = ['detenciones', 'expedientes', 'denuncias', 'multas', 'bienes_asegurados', 'cateos', 'actuaciones_ministeriales'].some(function(m){ return mods.includes(m); });
                if ($('#is_empleado').is(':checked') || !scoped) return true;
                var profile = $('input[name="multas_mini_profile"]:checked').val();
                if (!profile) { toastr.error('Seleccione un mini-perfil de Multas.'); return false; }

                var regionMulta = ($('#region_multa').val() || []).filter(Boolean);
                var dashRegions = ($('select[name="dashboard_regiones[]"]').val() || []).filter(Boolean);

                if (profile === 'fiscal_general_delegado') {
                    if (regionMulta.length < 1) { toastr.error('Asigne al menos una región de Multas.'); return false; }
                    if (dashRegions.length < 1) { toastr.error('Asigne al menos una región del Tablero.'); return false; }
	                } else if (['fiscal_especializado_coordinador', 'director'].includes(profile)) {
	                    if (!(($('#unidades_investigacion_multa').val() || []).length)) {
	                        toastr.error('Asigne al menos una unidad de investigación.'); return false;
	                    }
	                } else {
                    if (regionMulta.length !== 1) { toastr.error('Asigne exactamente una región.'); return false; }
	                    if (profile === 'ministerio_publico'
	                        && !(($('#unidades_investigacion_multa').val() || '').length)) {
                        toastr.error('Asigne una unidad de investigación.'); return false;
                    }
                }
                return true;
            }

            // ponytail: refresh unidades_investigacion_multa options whenever region_multa changes.
// Preserves any units still in the new region set; the server-side orphan filter
// (Usuarios::guardar/actualizar) drops the rest on the next save.
            $('select[name="region_multa[]"]').on('changed.bs.select change', function(){
                var ids = ($(this).val() || []).filter(Boolean);
                var $unidades = $('select[name="unidades_investigacion_multa[]"]');
	                var previousValue = $unidades.val();
	                var previouslySelected = Array.isArray(previousValue) ? previousValue.map(String) : (previousValue ? [String(previousValue)] : []);
                // bootstrap-select 1.14-beta leaves stale <li> items in the dropdown menu after
                // .empty() + .refresh(). destroy() + re-init rebuilds both the button label and
                // the menu <li> list from the current <option> set.
                var rebuild = function(rows) {
                    $unidades.empty();
                    rows.forEach(function(u){
                        var keep = previouslySelected.indexOf(String(u.id)) !== -1;
                        $unidades.append($('<option>', {value: u.id, text: u.nombre}).prop('selected', keep));
                    });
                    $unidades.selectpicker('destroy').selectpicker();
                };
                if (ids.length === 0) { rebuild([]); return; }
                $.get('<?= base_url(); ?>UnidadesInvestigacion/get_activos_by_regiones', {ids: ids}, rebuild, 'json').fail(function(){
                    toastr.error('No se pudieron cargar las unidades de investigación');
                });
            });

            $('#mostrarPassword').click(function (e) { 
               $("#modalPassword").modal("show");
            });

            $("#desencriptarPassword").click(function (e) { 
                $.post('<?= base_url(); ?>usuarios/mostrarpassword', {passwordDesencriptacion: $('#passwordDesencriptacion').val(), password: "<?php echo $usuario->passwordcorreo; ?>"}, function(data, textStatus, xhr) {
                    let obj = (data);
                    if(obj.success){
                        $('#textPassword').text("Contraseña: "+obj.password);
                        toastr.success('Contraseña desencriptada');
                    }else{
                        toastr.error('Revise la contraseña para desencriptar');
                    }
                }).fail(function(){
                    toastr.error('Algo anda mal');
                });
            });

            $('#firma').change(function () {
                let fileName = $(this).val().split('\\');
                $('#firma').next().text(fileName[fileName.length - 1]);
                const file = this.files[0];
                $('#filePreview').html(''); 

                const fileReader = new FileReader();

                fileReader.onload = function(e) {

                    $('input[name=firma]').val((e.target.result));
                    $('#filePreview').html('<img src="' + e.target.result + '" alt="Foto" style="width: 100%; height: auto; max-width: 300px;"> <br/> ');
                };
            
                fileReader.readAsDataURL(file);
            });

            $('select[name=idprofile]').trigger("change");
        });
    </script>
</body>

</html>
