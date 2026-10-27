<?php

namespace App\Models;

use Config\Database;
use App\Libraries\MultasAccessScope;

class UsuariosModel
{
    protected $db;

    protected $mpdb;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->mpdb = env('app.mpdigital_database_split') ? Database::connect('mpdigital') : Database::connect();
    }

    public function obtenerperfiles(){
        return $this->db->table('profiles')
            ->orderBy('profile', 'ASC')
            ->get()
            ->getResult();
    }
    public function obtenerregiones(){
        return $this->db->table('delegaciones')
            ->where('activo', 1)
            ->get()
            ->getResult();
    }
    function obtenerusuarios(){
        return $this->db->table('users')
            ->select('Id, name')
            ->where('active', 1)
            ->orderBy('name')
            ->get()
            ->getResult();
    }
    public function guardar($data){
        $builder = $this->db->table('users');
        $builder->set('creationdate', 'now()', FALSE);
        $builder->set('active', '1', FALSE);
        $builder->insert($data);

        return $this->db->insertID();
    }
    function comprobarusuario($email, $id){
        $builder = $this->db->table('users')
            ->select('Id')
            ->where('active', 1)
            ->where('email', $email);

        if ($id > 0) {
            $builder->where('Id <>', $id);
        }

        return $builder->get()->getRow();
    }
    public function obtenerusuario($id){
        return $this->db->table('users')
            ->where('Id', $id)
            ->get()
            ->getRow();   
    }
    public function actualizar($id, $data){
        $builder = $this->db->table('users');
        $builder->where('Id',$id);
        $builder->update($data);
    }
    public function eliminar($id){
        $id = (int)$id;
        $this->db->transStart();

        $this->db->table('users')
                ->where('Id', $id)
                ->update(['active' => 0]);

        $this->db->table('usuarios_unidades_investigacion')
                ->where('id_usuario', $id)
                ->delete();

        $this->db->table('user_special_permissions')
                    ->where('id_usuario', $id)
                    ->where('permission', MultasAccessScope::PERMISSION_SEND_EXPEDIENTES)
                    ->delete();


        $this->db->transComplete();

        return $this->db->transStatus() !== false;    
    }

    public function guardarregiones($id, $data){
        $this->db->table('usuarios_delegaciones')
                ->where('id_usuario', $id)
                ->delete();

        if (sizeof($data) > 0) {
            $this->db->table('usuarios_delegaciones')->insertBatch($data);
        }
    }

    public function obtenerregionesusuario($id){
        return $this->db->table('usuarios_delegaciones')
            ->where('id_usuario', $id)
            ->get()
            ->getResult();
    }
    
    public function obteneroficinas(){
        return $this->db->table('oficinas')
            ->get()
            ->getResult();
    }

    public function guardaroficinas($id, $data){
        $this->db->table('usuarios_oficinas')
                ->where('id_usuario', $id)
                ->delete();

        if (sizeof($data) > 0) {
            $this->db->table('usuarios_oficinas')->insertBatch($data);
        }
    }

    public function obteneroficinasusuario($id){
        return $this->db->table('usuarios_oficinas')
            ->where('id_usuario', $id)
            ->get()
            ->getResult();
    }

    // Multa Regiones methods — both fields in the user form read the same
    // `detenidos_regiones` catalog (managed by the Regiones CRUD). ponytail:
    // one catalog, one source of truth.
    public function obtenerregionesmultas(){
        return $this->mpdb->table('detenidos_regiones')
            ->select('id, nombre')
            ->where('activo', 1)
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResult();
    }

    // Dashboard regiones — same catalog as Detenidos + the Multas field above.
    public function obtener_regiones_dashboard(){
        return $this->mpdb->table('detenidos_regiones')
            ->select('id, nombre')
            ->where('activo', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();
    }

    public function guardar_regiones_dashboard($id, $data){
        $this->db->table('usuarios_dashboard_regiones')
                ->where('id_usuario', (int)$id)
                ->delete();

        if (sizeof($data) > 0) {
            $this->db->table('usuarios_dashboard_regiones')->insertBatch($data);
        }
    }

    public function obtener_dashboard_regiones_usuario($id){
        return $this->db->table('usuarios_dashboard_regiones')
            ->select('id_region')
            ->where('id_usuario', (int)$id)
            ->get()
            ->getResult();
    }

    public function guardarregionesmultas($id, $data){
        // Delete existing multa regiones for this user
        $this->db->table('usuarios_delegaciones')
                ->where('id_usuario', $id)
                ->where('id_region IS NOT NULL')
                ->delete();

        // Insert new ones
        if (sizeof($data) > 0) {
            $this->db->table('usuarios_delegaciones')->insertBatch($data);
        }
    }

    public function obtenerregionesmultasusuario($id){
        return $this->db->table('usuarios_delegaciones')
            ->where('id_usuario', $id)
            ->where('id_region IS NOT NULL')
            ->get()
            ->getResult();
    }

    public function obtener_permisos_especiales($id){
        return array_column($this->db->table('user_special_permissions')
            ->select('permission')
            ->where('id_usuario', $id)
            ->get()
            ->getResultArray(), 'permission');
    }

    public function guardar_permisos_especiales($id, $permisos){
        $this->db->table('user_special_permissions')
                ->where('id_usuario', $id)
                ->delete();

        if(!empty($permisos)){
            $data = array();
            foreach($permisos as $permission){
                $data[] = array('id_usuario' => $id, 'permission' => $permission);
            }
            $this->db->table('user_special_permissions')->insertBatch($data);
        }
    }

    public function obtenermodulos(){
        return $this->db->table('modules')
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResult();
    }

    public function obtenermodulosusuario($id){
        return array_column(
            $this->db->table('user_modules um')
                ->select('m.name')
                ->join('modules m', 'm.Id = um.id_module')
                ->where('um.id_usuario', (int)$id)
                ->orderBy('m.sort_order', 'ASC')
                ->get()
                ->getResultArray(), 'name');
    }

    public function guardarmodulos($id, $modulos){
        $this->db->table('user_modules')
                ->where('id_usuario', (int)$id)
                ->delete();

        if(!empty($modulos)){
            $data = array();

            foreach($modulos as $nombre){
                $row = $this->db->table('modules')
                                ->select('Id')
                                ->where('name', $nombre)
                                ->get()
                                ->getRow();

                if($row){
                    $data[] = array('id_usuario' => (int)$id, 'id_module' => $row->Id);
                }
            }

            if(!empty($data)){
                $this->db->table('user_modules')->insertBatch($data);
            }
        }
    }

    // Unidades de investigación — scoped under region_multa.
    // Same delete-then-insert_batch pattern as guardar_regiones_dashboard.
    public function guardar_unidades_investigacion($id, $data){
        $this->db->table('usuarios_unidades_investigacion')
                ->where('id_usuario', (int)$id)
                ->delete();

        if (sizeof($data) > 0) {
            $this->db->table('usuarios_unidades_investigacion')->insertBatch($data);
        }
    }

    public function obtener_unidades_investigacion_usuario($id){
        return $this->db->table('usuarios_unidades_investigacion')
            ->select('id_unidad_investigacion AS id, tipo_adscripcion')
            ->where('id_usuario', (int)$id)
            ->get()
            ->getResult();
    }

    public function obtener_resumen_adscripcion_mpdigital($id){
        $id = (int)$id;
        $user = $id > 0
            ? $this->db->table('users')
                ->select('multas_mini_profile')
                ->where('Id', $id)
                ->get()
                ->getRowArray()
            : null;
        $profile = $user['multas_mini_profile'] ?? null;
        $regions = [];
        $units = [];



        if ($profile === MultasAccessScope::FISCAL_GENERAL_DELEGADO) {
            $regions = array_column($this->mpdb->table('fiscalia.usuarios_delegaciones d')
                                            ->select('r.nombre')
                                            ->join('detenidos_regiones r', 'r.id = d.id_region AND r.activo = 1')
                                            ->where('d.id_usuario', $id)
                                            ->orderBy('r.nombre', 'ASC')
                                            ->get()
                                            ->getResultArray(), 'nombre');
        } elseif ($profile === MultasAccessScope::FISCAL_ESPECIALIZADO_COORDINADOR
            || $profile === MultasAccessScope::MINISTERIO_PUBLICO || $profile === MultasAccessScope::DIRECTOR) {
            $assignment = $profile === MultasAccessScope::FISCAL_ESPECIALIZADO_COORDINADOR
                ? 'coordinador' : ($profile === MultasAccessScope::DIRECTOR ? 'director' : 'ministerio_publico');
            $assignments = $this->mpdb->table('fiscalia.usuarios_unidades_investigacion a')
                                            ->select('u.nombre AS unidad, r.nombre AS region')
                                            ->join('denuncias_unidades_investigacion u', 'u.id=a.id_unidad_investigacion AND u.activo=1')
                                            ->join('detenidos_regiones r', 'r.id=u.id_region AND r.activo=1')
                                            ->where('a.id_usuario', $id)
                                            ->where('a.tipo_adscripcion', $assignment)
                                            ->orderBy('r.nombre, u.nombre', 'ASC')
                                            ->get()
                                            ->getResultArray();
            $regions = array_values(array_unique(array_column($assignments, 'region')));
            $units = array_column($assignments, 'unidad');
        }

        return ['profile' => $profile, 'regions' => $regions, 'units' => $units];
    }

    public function obtener_organigrama_mpdigital(){
        $profiles = ['director', 'fiscal_general_delegado', 'fiscal_especializado_coordinador', 'ministerio_publico'];
        $users = $this->db->table('users')
                            ->select('Id, name, email, multas_mini_profile')
                            ->where('active', 1)
                            ->whereIn('multas_mini_profile', $profiles)
                            ->orderBy('name', 'ASC')
                            ->get()
                            ->getResultArray();
        $people = [];
        foreach ($users as $user) $people[(int)$user['Id']] = $user;

        $regions = [];
        $visible = [];
        $delegates = $this->mpdb->table('fiscalia.usuarios_delegaciones d')
                            ->select('d.id_usuario, r.id AS region_id, r.nombre AS region_nombre')
                            ->join('detenidos_regiones r', 'r.id=d.id_region AND r.activo=1')
                            ->get()
                            ->getResultArray();


        foreach ($delegates as $row) {
            $id = (int)$row['id_usuario'];
            if (!isset($people[$id]) || $people[$id]['multas_mini_profile'] !== 'fiscal_general_delegado') continue;
            $regionId = (int)$row['region_id'];
            if (!isset($regions[$regionId])) $regions[$regionId] = ['id' => $regionId, 'nombre' => $row['region_nombre'], 'directores' => [], 'delegados' => [], 'coordinadores' => []];
            $regions[$regionId]['delegados'][$id] = $people[$id];
            $visible[$id] = true;
        }
        $assignments = $this->mpdb->table('fiscalia.usuarios_unidades_investigacion a')
                                        ->select('a.id_usuario, a.id_unidad_investigacion, a.tipo_adscripcion, u.id_region, u.nombre AS unidad_nombre, r.nombre AS region_nombre')
                                        ->join('denuncias_unidades_investigacion u', 'u.id=a.id_unidad_investigacion AND u.activo=1')
                                        ->join('detenidos_regiones r', 'r.id=u.id_region AND r.activo=1')
                                        ->get()
                                        ->getResultArray();

        $mpsByUnit = [];
        foreach ($assignments as $row) {
            $id = (int)$row['id_usuario'];
            if (!isset($people[$id])) continue;
            $profile = $people[$id]['multas_mini_profile'];
            $unitId = (int)$row['id_unidad_investigacion'];
            $regionId = (int)$row['id_region'];
            if ($profile === 'director' && $row['tipo_adscripcion'] === 'director') {
                if (!isset($regions[$regionId])) {
                    $regions[$regionId] = ['id' => $regionId, 'nombre' => $row['region_nombre'], 'directores' => [], 'delegados' => [], 'coordinadores' => []];
                }
                if (!isset($regions[$regionId]['directores'][$id])) $regions[$regionId]['directores'][$id] = $people[$id] + ['unidades' => []];
                $regions[$regionId]['directores'][$id]['unidades'][$unitId] = ['id' => $unitId, 'nombre' => $row['unidad_nombre']];
                $visible[$id] = true;
                continue;
            }
            if ($profile === 'ministerio_publico' && $row['tipo_adscripcion'] === 'ministerio_publico') {
                $mpsByUnit[$unitId][$id] = $people[$id];
                continue;
            }
            if ($profile !== 'fiscal_especializado_coordinador' || $row['tipo_adscripcion'] !== 'coordinador') continue;
            if (!isset($regions[$regionId])) {
                $regions[$regionId] = ['id' => $regionId, 'nombre' => $row['region_nombre'], 'directores' => [], 'delegados' => [], 'coordinadores' => []];
            }
            if (!isset($regions[$regionId]['coordinadores'][$id])) $regions[$regionId]['coordinadores'][$id] = $people[$id] + ['unidades' => []];
            $regions[$regionId]['coordinadores'][$id]['unidades'][$unitId] = ['id' => $unitId, 'nombre' => $row['unidad_nombre'], 'mps' => []];
            $visible[$id] = true;
        }
        foreach ($regions as &$region) {
            foreach ($region['directores'] as &$director) {
                uasort($director['unidades'], function($a, $b) { return strcasecmp($a['nombre'], $b['nombre']); });
            }
            unset($director);
            foreach ($region['coordinadores'] as &$coordinador) {
                foreach ($coordinador['unidades'] as &$unidad) {
                    $unidad['mps'] = $mpsByUnit[$unidad['id']] ?? [];
                    foreach ($unidad['mps'] as $mp) $visible[(int)$mp['Id']] = true;
                    uasort($unidad['mps'], function($a, $b) { return strcasecmp($a['name'], $b['name']); });
                }
                unset($unidad);
                uasort($coordinador['unidades'], function($a, $b) { return strcasecmp($a['nombre'], $b['nombre']); });
            }
            unset($coordinador);
            uasort($region['directores'], function($a, $b) { return strcasecmp($a['name'], $b['name']); });
            uasort($region['delegados'], function($a, $b) { return strcasecmp($a['name'], $b['name']); });
            uasort($region['coordinadores'], function($a, $b) { return strcasecmp($a['name'], $b['name']); });
        }
        unset($region);
        uasort($regions, function($a, $b) { return strcasecmp($a['nombre'], $b['nombre']); });
        $unassigned = array_filter($people, function($user) use ($visible) { return empty($visible[(int)$user['Id']]); });
        uasort($unassigned, function($a, $b) { return strcasecmp($a['name'], $b['name']); });
        return ['regiones_organigrama' => $regions, 'sin_adscripcion_organigrama' => $unassigned];
    }

    public function coordinador_unidad_ocupada($id_unidad, $excluir_usuario = 0) {
        $builder = $this->db->table('usuarios_unidades_investigacion a')
            ->select('u.Id, u.name')
            ->join('users u', 'u.Id=a.id_usuario')
            ->where('a.id_unidad_investigacion', (int)$id_unidad)
            ->where('a.tipo_adscripcion', 'coordinador')
            ->where('u.active', 1)
            ->where('u.multas_mini_profile', 'fiscal_especializado_coordinador');

        if ((int)$excluir_usuario > 0) {
            $builder->where('u.Id <>', (int)$excluir_usuario);
        }

        return $builder->limit(1)->get()->getRowArray();
    }

    public function alertas_adscripcion_expedientes() {
        $sin_unidad = (int) $this->db->table('users u')
                                ->where('u.active', 1)
                                ->where('u.multas_mini_profile', 'ministerio_publico')
                                ->where(
                                    'NOT EXISTS (SELECT 1 FROM usuarios_unidades_investigacion a WHERE a.id_usuario = u.Id)',
                                    null,
                                    false
                                )
                                ->countAllResults();
        // ponytail: outer `denuncias_unidades_investigacion` vive en fiscalia_mpdigital
        // pero los JOINs internos (`usuarios_unidades_investigacion` + `users`) viven
        // en fiscalia. CI3 no resuelve cross-schema automáticamente — MySQL usa el
        // current_schema del connection. Calificar explícitamente las tablas fiscales.
        // Mismo patrón que MultasAccessScope::get() línea 79.
        $sin_coordinador = (int) $this->mpdb->table('denuncias_unidades_investigacion ui')
            ->where('ui.activo', 1)
            ->where(
                "NOT EXISTS (
                    SELECT 1
                    FROM fiscalia.usuarios_unidades_investigacion a
                    JOIN fiscalia.users u
                        ON u.Id = a.id_usuario
                        AND u.active = 1
                        AND u.multas_mini_profile = 'fiscal_especializado_coordinador'
                    WHERE a.id_unidad_investigacion = ui.id
                        AND a.tipo_adscripcion = 'coordinador'
                )",
                null,
                false
            )
            ->countAllResults();

        return [
            'mp_sin_unidad' => $sin_unidad,
            'unidades_sin_coordinador' => $sin_coordinador,
        ];
    }

    // Defensive: drop submitted unidad ids whose region is not in the just-saved region_multa set.
    // ponytail: server-side enforcement of "only matching unidades" — JS may submit stale ids.
    public function filtrar_unidades_multa_porregiones(array $unidad_ids, array $region_ids) {
        if (empty($unidad_ids) || empty($region_ids)) return [];
        $rows = $this->mpdb->table('denuncias_unidades_investigacion')
                                        ->select('id')
                                        ->where('activo', 1)
                                        ->whereIn('id', array_map('intval', $unidad_ids))
                                        ->whereIn('id_region', array_map('intval', $region_ids))
                                        ->get()
                                        ->getResultArray();
        return array_map(fn($r) => (int)$r['id'], $rows);
    }

    public function filtrar_regiones_multa_activas(array $region_ids) {
        $region_ids = array_values(array_filter(array_unique(array_map('intval', $region_ids))));
        if (empty($region_ids)) return [];
        $rows = $this->mpdb->table('detenidos_regiones')
                                        ->select('id')
                                        ->where('activo', 1)
                                        ->whereIn('id', $region_ids)
                                        ->get()
                                        ->getResultArray();
        return array_map(fn($r) => (int)$r['id'], $rows);
    }

    
}