<?php

namespace App\Models;

use Config\Database;

class AreasModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

     function obtenerTodos() {
        return $this->db->table('areas')
            ->where('activo', 1)
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResult();
    }

    function obtenerPorId($id) {
        return $this->db->table('users')
            ->where('id', $id)
            ->get()
            ->getRow();   
    }

    public function guardar($data){
        $builder = $this->db->table('areas');
        $builder->insert($data);

        return $this->db->insertID();
    }

    function actualizar($id, $data) {
        $builder = $this->db->table('areas');
        $builder->where('id',$id);
        return $builder->update($data);
    }

    function eliminar($id) {
        $builder = $this->db->table('areas');
        $builder->where('id',$id);
        return $builder->update(array('activo'=>'0'));
    }
    
}