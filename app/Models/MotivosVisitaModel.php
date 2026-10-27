<?php

namespace App\Models;

use Config\Database;

class MotivosVisitaModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    function obtenerTodos() {
        return $this->db->table('motivos_visitas')
            ->where('activo', 1)
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResult();
    }

    function obtenerPorId($id) {
        return $this->db->table('motivos_visita')
            ->where('id', $id)
            ->get()
            ->getRow();   
    }

    public function guardar($data){
        $builder = $this->db->table('motivos_visita');
        $builder->insert($data);

        return $this->db->insertID();
    }

    function actualizar($id, $data) {
        $builder = $this->db->table('motivos_visita');
        $builder->where('id',$id);
        return $builder->update($data);
    }

    function eliminar($id) {
        $builder = $this->db->table('motivos_visita');
        $builder->where('id',$id);
        return $builder->update(array('activo'=>'0'));
    }
    
}