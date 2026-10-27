<?php

namespace App\Models;

use Config\Database;

class UnidadesInvestigacionModel
{
    protected $mpdb;

    public function __construct()
    {
        $this->mpdb = env('app.mpdigital_database_split') ? Database::connect('mpdigital') : Database::connect();
    }

    function get_all_activos() {
        return $this->mpdb->table('denuncias_unidades_investigacion')
            ->where('activo', 1)
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResultArray();
    }

    // ponytail: WHERE id_region IN (…) — used by the Usuarios form to filter unidades
    // by the regiones the admin just picked. Empty input → empty result (no DB hit).
    function get_activos_by_regiones(array $ids) {
        if (empty($ids)) return [];
        return $this->mpdb->table('denuncias_unidades_investigacion')
            ->select('id, nombre, clave, id_region')
            ->where('activo', 1)
            ->whereIn('id_region', array_map('intval', $ids))
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResultArray();
    }

    function guardar($data){
        $builder = $this->mpdb->table('denuncias_unidades_investigacion');
        $builder->insert($data);

        return $this->mpdb->insertID();
    }

    function actualizar($id, $data) {
        $builder = $this->mpdb->table('denuncias_unidades_investigacion');
        $builder->where('id',$id);
        return $builder->update($data);
    }

    function guardar_con_sedes($data, array $sedes) {
        $this->mpdb->transStart();
        $this->mpdb->table('denuncias_unidades_investigacion')
            ->insert($data);
        $id = (int) $this->mpdb->insertID();
        $this->guardar_sedes($id, $sedes);
        $this->mpdb->transComplete();
        return $this->mpdb->transStatus() ? $id : 0;
    }

    function actualizar_con_sedes($id, $data, ?array $sedes = null) {
        $this->mpdb->transStart();
        $this->mpdb->table('denuncias_unidades_investigacion')
                    ->where('id', $id)
                    ->update($data);
        if ($sedes !== null) {
            $this->mpdb->table('denuncias_unidades_investigacion_sedes')
                    ->where('id_unidad_investigacion', $id)
                    ->delete();
            $this->guardar_sedes($id, $sedes);
        }
        $this->mpdb->transComplete();
        return $this->mpdb->transStatus();
    }

    function get_sedes($id) {
        return $this->mpdb->table('denuncias_unidades_investigacion_sedes')
            ->where('id_unidad_investigacion', $id)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }
    private function guardar_sedes($id, array $sedes) {
        foreach ($sedes as &$sede) $sede['id_unidad_investigacion'] = $id;
        unset($sede);
        if (!empty($sedes)){
            $this->mpdb->table('denuncias_unidades_investigacion_sedes')->insertBatch($sedes);
        } 
    }
    function eliminar($id) {
        return $this->actualizar($id, ['activo' => 0]); 
    }
}