<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Config\Mpdigital;

/**
 * Alcance operativo para Detenciones, Expedientes y Denuncias.
 */
class MultasAccessScope
{
    public const DIRECTOR                       = 'director';
    public const FISCAL_GENERAL_DELEGADO        = 'fiscal_general_delegado';
    public const FISCAL_ESPECIALIZADO_COORDINADOR = 'fiscal_especializado_coordinador';
    public const MINISTERIO_PUBLICO             = 'ministerio_publico';
    public const PERMISSION_SEND_EXPEDIENTES    = 'enviar_expedientes';
    public const PERMISSION_AUDIT_EXPEDIENTES   = 'consultar_auditoria_expedientes';
    public const PERMISSION_VIEW_ORGANIGRAMA    = 'ver_organigrama_mpdigital';

    private BaseConnection $coredb;
    private BaseConnection $mpdb;

    /**
     * @param BaseConnection|null $coredb Conexión a la base fiscalia (por defecto la del grupo `default`).
     * @param BaseConnection|null $mpdb   Conexión a la base de mpdigital (por defecto el grupo `mpdigital`).
     */
    public function __construct(?BaseConnection $coredb = null, ?BaseConnection $mpdb = null)
    {
        $this->coredb = $coredb ?? Database::connect();

        if ($mpdb !== null) {
            $this->mpdb = $mpdb;
        } else {
            $split      = config(Mpdigital::class)->split;
            $this->mpdb = $split ? Database::connect('mpdigital') : $this->coredb;
        }
    }

    public static function profiles(): array
    {
        return [
            self::DIRECTOR => [
                'label'       => 'Director',
                'description' => 'Puede consultar los expedientes, incluidos los enviados a MASC, bajo custodia actual de sus unidades asignadas.',
            ],
            self::FISCAL_GENERAL_DELEGADO => [
                'label'       => 'Fiscal - Delegado',
                'description' => 'Puede consultar en modo de solo lectura los registros de Detenciones, Expedientes y Denuncias de las regiones seleccionadas.',
            ],
            self::FISCAL_ESPECIALIZADO_COORDINADOR => [
                'label'       => 'Coordinador',
                'description' => 'Puede gestionar los expedientes bajo custodia de su unidad y consultar aquellos en los que participó.',
            ],
            self::MINISTERIO_PUBLICO => [
                'label'       => 'Ministerio Público',
                'description' => 'Puede crear en su unidad y gestionar expedientes mientras sea su custodio actual.',
            ],
        ];
    }

    public static function scoped_modules(): array
    {
        return ['detenciones', 'expedientes', 'denuncias', 'multas', 'bienes_asegurados', 'cateos', 'actuaciones_ministeriales'];
    }

    public static function is_valid_profile($profile): bool
    {
        return isset(self::profiles()[$profile]);
    }

    public static function label($profile): string
    {
        $profiles = self::profiles();

        return $profiles[$profile]['label'] ?? 'Sin mini-perfil';
    }

    public function get($user_id): array
    {
        $user_id = (int) $user_id;
        $user    = $user_id > 0
            ? $this->coredb->table('users')
                ->select('multas_mini_profile')
                ->where('Id', $user_id)
                ->get()->getRowArray()
            : null;

        $profile = $user['multas_mini_profile'] ?? null;

        if ($profile === self::FISCAL_GENERAL_DELEGADO) {
            // El delegado no tiene adscripción a unidades: captura en cualquiera de sus regiones.
            $region_ids = $user_id > 0
                ? array_values(array_unique(array_map('intval', array_column(
                    $this->coredb->table('usuarios_delegaciones')
                        ->select('id_region')
                        ->where('id_usuario', $user_id)
                        ->get()->getResultArray(),
                    'id_region'
                ))))
                : [];
            $units = empty($region_ids) ? [] : $this->mpdb
                ->table('denuncias_unidades_investigacion')
                ->select('id AS id_unidad_investigacion, id_region')
                ->where('activo', 1)
                ->whereIn('id_region', $region_ids)
                ->get()->getResultArray();
        } else {
            $units = $user_id > 0
                ? $this->mpdb
                    ->table('fiscalia.usuarios_unidades_investigacion aui')
                    ->select('aui.id_unidad_investigacion, aui.tipo_adscripcion, u.id_region, u.es_masc')
                    ->join('denuncias_unidades_investigacion u', 'u.id = aui.id_unidad_investigacion')
                    ->where('aui.id_usuario', $user_id)
                    ->where('u.activo', 1)
                    ->get()->getResultArray()
                : [];
            $expected_assignment = $profile === self::FISCAL_ESPECIALIZADO_COORDINADOR ? 'coordinador'
                : ($profile === self::MINISTERIO_PUBLICO ? 'ministerio_publico'
                : ($profile === self::DIRECTOR ? 'director' : null));
            $units = $expected_assignment === null ? [] : array_values(array_filter($units, static function ($row) use ($expected_assignment) {
                return ($row['tipo_adscripcion'] ?? null) === $expected_assignment;
            }));
            $region_ids = array_values(array_unique(array_map('intval', array_column($units, 'id_region'))));
        }

        $permissions = $user_id > 0
            ? array_column(
                $this->coredb->table('user_special_permissions')
                    ->select('permission')
                    ->where('id_usuario', $user_id)
                    ->get()->getResultArray(),
                'permission'
            )
            : [];

        $modules = $user_id > 0
            ? array_column(
                $this->coredb->table('user_modules um')
                    ->select('m.name')
                    ->join('modules m', 'm.Id = um.id_module')
                    ->where('um.id_usuario', $user_id)
                    ->get()->getResultArray(),
                'name'
            )
            : [];

        $masc_unit_ids = [];
        if ($profile === self::FISCAL_ESPECIALIZADO_COORDINADOR && ! empty($units)) {
            $masc_unit_ids = array_values(array_unique(array_map('intval', array_column(
                array_filter($units, static fn ($unit) => (int) ($unit['es_masc'] ?? 0) === 1),
                'id_unidad_investigacion'
            ))));
        }

        return [
            'user_id'       => $user_id,
            'profile'       => $profile,
            'region_ids'    => $region_ids,
            'unit_ids'      => array_values(array_unique(array_map('intval', array_column($units, 'id_unidad_investigacion')))),
            'masc_unit_ids' => $masc_unit_ids,
            'permissions'   => $permissions,
            'modules'       => $modules,
        ];
    }

    /**
     * Returns ['locked' => true, 'unidad' => row] only when the user is bound to exactly
     * one unidad and that unidad is not the delegado (who has no row in
     * usuarios_unidades_investigacion but may pick any active unit in their regiones).
     * Otherwise returns ['locked' => false, 'unidad' => null] so the view keeps the
     * editable <select>. Caller passes the already-loaded $unidades_investigacion cache
     * so we don't issue an extra query in the common case.
     */
    public static function lock_unit_or_null(array $scope, array $unidades_cache = []): array
    {
        $profile  = $scope['profile'] ?? null;
        $unit_ids = array_values(array_filter(array_map('intval', $scope['unit_ids'] ?? [])));
        if ($profile === self::FISCAL_GENERAL_DELEGADO || count($unit_ids) !== 1) {
            return ['locked' => false, 'unidad' => null];
        }
        $uid = (int) $unit_ids[0];
        foreach ($unidades_cache as $u) {
            if ((int) ($u['id'] ?? 0) === $uid) {
                return ['locked' => true, 'unidad' => $u];
            }
        }

        // ponytail: cache miss — caller may fall back to a DB query; we keep the lock
        // signal so the view can render the readonly field as soon as the row arrives.
        return ['locked' => true, 'unidad' => null];
    }

    /** Pure policy check, shared by controllers and the runnable matrix test. */
    public static function allows(array $scope, $created_by, $region_id, $unit_id): bool
    {
        $profile = $scope['profile'] ?? null;
        $user_id = (int) ($scope['user_id'] ?? 0);
        if (! self::is_valid_profile($profile) || $user_id <= 0) {
            return false;
        }

        $region_ids = array_values(array_filter(array_map('intval', $scope['region_ids'] ?? [])));
        $unit_ids   = array_values(array_filter(array_map('intval', $scope['unit_ids'] ?? [])));
        if ($profile === self::FISCAL_GENERAL_DELEGADO) {
            return (int) $region_id > 0 && in_array((int) $region_id, $region_ids, true);
        }
        if ($profile === self::FISCAL_ESPECIALIZADO_COORDINADOR || $profile === self::DIRECTOR) {
            return ! empty($region_ids) && (int) $unit_id > 0 && in_array((int) $unit_id, $unit_ids, true);
        }

        return (int) $created_by === $user_id;
    }

    public static function can_create(array $scope): bool
    {
        if (($scope['profile'] ?? null) === self::DIRECTOR) {
            return false;
        }

        return self::is_valid_profile($scope['profile'] ?? null)
            && count(array_filter(array_map('intval', $scope['unit_ids'] ?? []))) >= 1;
    }

    public static function can_edit(array $scope, $created_by, $current_unit_id, $current_mp_id, $concluido_at = null, $masc_estado = 'interno'): bool
    {
        if (in_array($masc_estado, ['enviado', 'regresado'], true)) {
            return false;
        }
        $profile = $scope['profile'] ?? null;
        if ($profile === self::DIRECTOR) {
            return false;
        }
        $uid = (int) ($scope['user_id'] ?? 0);
        if ($profile === self::FISCAL_ESPECIALIZADO_COORDINADOR) {
            if ($masc_estado === 'asignado_mp') {
                return false;
            }

            return in_array((int) $current_unit_id, array_map('intval', $scope['unit_ids'] ?? []), true);
        }
        // El delegado solo edita lo que capturó él (al crear, id_mp_actual = created_by).
        if ($profile === self::MINISTERIO_PUBLICO || $profile === self::FISCAL_GENERAL_DELEGADO) {
            return (int) $current_mp_id === $uid;
        }

        return false;
    }

    public static function can_send(array $scope, $created_by, $current_unit_id, $current_mp_id, $concluido_at = null): bool
    {
        return ($scope['profile'] ?? null) === self::FISCAL_ESPECIALIZADO_COORDINADOR
            && in_array(self::PERMISSION_SEND_EXPEDIENTES, $scope['permissions'] ?? [], true)
            && self::can_edit($scope, $created_by, $current_unit_id, $current_mp_id, $concluido_at);
    }

    /** MASC Digital is available only to the coordinator holding direct custody in a MASC unit. */
    public static function can_send_masc(array $scope, $created_by, $current_unit_id, $current_mp_id, $concluido_at = null, $masc_estado = 'interno', $habilitado = 0): bool
    {
        return ($scope['profile'] ?? null) === self::FISCAL_ESPECIALIZADO_COORDINADOR
            && in_array($masc_estado, ['interno', 'asignado_mp'], true)
            && ((int) $current_mp_id === 0 || ($masc_estado === 'asignado_mp' && (int) $habilitado === 1))
            && in_array((int) $current_unit_id, array_map('intval', $scope['masc_unit_ids'] ?? []), true);
    }

    public static function masc_block_reasons(array $scope, $created_by, $current_unit_id, $current_mp_id, $has_victim, $has_imputado, $already_sent, $concluido_at = null, $masc_estado = 'interno', $habilitado = 0): array
    {
        $reasons = [];
        if ($masc_estado === 'enviado' || ((int) $already_sent === 1 && $masc_estado === 'interno')) {
            $reasons[] = 'El expediente ya fue enviado a MASC.';
        }
        if ($masc_estado === 'regresado') {
            $reasons[] = 'El expediente regresó de MASC y debe asignarse a un MP.';
        }
        if (! (int) $has_victim) {
            $reasons[] = 'Requiere al menos una víctima.';
        }
        if (! (int) $has_imputado) {
            $reasons[] = 'Requiere al menos un imputado.';
        }
        if (! self::can_send_masc($scope, $created_by, $current_unit_id, $current_mp_id, $concluido_at, $masc_estado, $habilitado)) {
            $reasons[] = $masc_estado === 'asignado_mp'
                ? 'Debe estar en una unidad MASC y marcado como listo para enviar.'
                : 'Debe recibirse en una unidad MASC, bajo custodia directa de su Coordinadora.';
        }

        return $reasons;
    }

    public static function can_global_audit(array $scope): bool
    {
        if (($scope['profile'] ?? null) === self::DIRECTOR) {
            return false;
        }

        return in_array(self::PERMISSION_AUDIT_EXPEDIENTES, $scope['permissions'] ?? [], true)
            && (($scope['profile'] ?? null) === self::FISCAL_ESPECIALIZADO_COORDINADOR
                || in_array('usuarios', $scope['modules'] ?? [], true));
    }

    public static function can_scoped_audit(array $scope): bool
    {
        return ($scope['profile'] ?? null) === self::FISCAL_ESPECIALIZADO_COORDINADOR && ! empty($scope['unit_ids']);
    }

    /** SQL predicate for trusted column expressions supplied by application code. */
    public static function sql_condition(array $scope, $created_expr, $region_expr, $unit_expr, $exp_id_expr = null, $current_unit_expr = null, $current_mp_expr = null): string
    {
        $profile = $scope['profile'] ?? null;
        $user_id = (int) ($scope['user_id'] ?? 0);
        if (! self::is_valid_profile($profile) || $user_id <= 0) {
            return '1=0';
        }

        if ($profile === self::FISCAL_GENERAL_DELEGADO) {
            $ids = array_values(array_filter(array_map('intval', $scope['region_ids'] ?? [])));

            return empty($ids) ? '1=0' : $region_expr . ' IN (' . implode(',', $ids) . ')';
        }
        if ($exp_id_expr !== null && $current_unit_expr !== null && $current_mp_expr !== null) {
            $participant = self::participant_sql($exp_id_expr, $user_id);
            if ($profile === self::DIRECTOR) {
                $ids = array_values(array_filter(array_map('intval', $scope['unit_ids'] ?? [])));

                return empty($ids) ? '1=0' : $current_unit_expr . ' IN (' . implode(',', $ids) . ')';
            }
            if ($profile === self::FISCAL_ESPECIALIZADO_COORDINADOR) {
                $ids     = array_values(array_filter(array_map('intval', $scope['unit_ids'] ?? [])));
                $current = empty($ids) ? '1=0' : $current_unit_expr . ' IN (' . implode(',', $ids) . ')';

                return '(' . $current . ' OR ' . $participant . ')';
            }

            return '(' . $created_expr . ' = ' . $user_id . ' OR ' . $current_mp_expr . ' = ' . $user_id . ' OR ' . $participant . ')';
        }
        if ($profile === self::FISCAL_ESPECIALIZADO_COORDINADOR || $profile === self::DIRECTOR) {
            $ids = array_values(array_filter(array_map('intval', $scope['unit_ids'] ?? [])));

            return empty($ids) ? '1=0' : $unit_expr . ' IN (' . implode(',', $ids) . ')';
        }

        return $created_expr . ' = ' . $user_id;
    }

    private static function participant_sql($exp_id_expr, $user_id): string
    {
        return 'EXISTS (SELECT 1 FROM expedientes_envios ae WHERE ae.id_expediente = ' . $exp_id_expr
            . ' AND (' . (int) $user_id . ' IN (ae.enviado_por, COALESCE(ae.id_mp_origen,0), COALESCE(ae.id_mp_destino,0), COALESCE(ae.id_coordinador_destino,0))))';
    }

    public function can_view_expediente(array $scope, array $expediente): bool
    {
        if (($scope['profile'] ?? null) === self::FISCAL_GENERAL_DELEGADO) {
            $rid = $this->_expediente_region_id($expediente);

            return $rid !== null && in_array($rid, array_map('intval', $scope['region_ids'] ?? []), true);
        }
        if (($scope['profile'] ?? null) === self::DIRECTOR) {
            return in_array((int) ($expediente['id_unidad_actual'] ?? 0), array_map('intval', $scope['unit_ids'] ?? []), true);
        }
        if (self::can_edit($scope, $expediente['created_by'] ?? 0, $expediente['id_unidad_actual'] ?? 0, $expediente['id_mp_actual'] ?? 0, $expediente['concluido_at'] ?? null)) {
            return true;
        }
        if (($scope['profile'] ?? null) === self::MINISTERIO_PUBLICO
            && (int) ($expediente['created_by'] ?? 0) === (int) ($scope['user_id'] ?? 0)) {
            return true;
        }

        return $this->mpdb->table('expedientes_envios')
            ->where('id_expediente', (int) ($expediente['id'] ?? 0))
            ->groupStart()
            ->where('enviado_por', (int) $scope['user_id'])
            ->orWhere('id_mp_origen', (int) $scope['user_id'])
            ->orWhere('id_mp_destino', (int) $scope['user_id'])
            ->orWhere('id_coordinador_destino', (int) $scope['user_id'])
            ->groupEnd()
            ->countAllResults() > 0;
    }

    private function _expediente_region_id(array $expediente): ?int
    {
        if (! empty($expediente['id_unidad_actual'])) {
            $row = $this->mpdb->table('denuncias_unidades_investigacion')
                ->select('id_region')
                ->where('id', (int) $expediente['id_unidad_actual'])
                ->get()->getRowArray();
            if ($row) {
                return (int) $row['id_region'];
            }
        }
        if (! empty($expediente['id_region'])) {
            return (int) $expediente['id_region'];
        }

        return null;
    }

    public function allowed_units_for_capture(array $scope): array
    {
        if (! self::can_create($scope)) {
            return [];
        }
        $ids = array_values(array_filter(array_map('intval', $scope['unit_ids'] ?? [])));

        return $this->mpdb->table('denuncias_unidades_investigacion')
            ->select('id, nombre, clave, id_region')
            ->where('activo', 1)
            ->whereIn('id', $ids)
            ->orderBy('nombre', 'ASC')
            ->get()->getResultArray();
    }

    public function unit_allowed_for_capture(array $scope, $unit_id, $region_id = 0): bool
    {
        $unit_id   = (int) $unit_id;
        $region_id = (int) $region_id;
        if ($unit_id <= 0) {
            return false;
        }

        $unit = $this->mpdb->table('denuncias_unidades_investigacion')
            ->select('id, id_region')
            ->where('id', $unit_id)
            ->where('activo', 1)
            ->get()->getRowArray();
        if (! $unit) {
            return false;
        }
        if ($region_id > 0 && (int) $unit['id_region'] !== $region_id) {
            return false;
        }
        if (! self::can_create($scope)) {
            return false;
        }

        return in_array($unit_id, array_map('intval', $scope['unit_ids'] ?? []), true)
            && (! $region_id || (int) $unit['id_region'] === $region_id);
    }
}
