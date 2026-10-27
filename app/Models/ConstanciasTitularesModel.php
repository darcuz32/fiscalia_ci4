<?php

namespace App\Models;

use Config\Database;

class ConstanciasTitularesModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }
    
    public function guardar($data){
        $builder = $this->db->table('delegaciones');
        $builder->insert($data);

        return $this->db->insertID();
    }

    public function actualizar($id, $data){
        $builder = $this->db->table('delegaciones');
        $builder->where('Id',$id);
        return $builder->update($data);
    }

    public function obtenerdelegacion($id){
        return $this->db->table('delegaciones')
            ->where('Id', $id)
            ->get()
            ->getRow();   
    }

    public function desactivardelegacion($id){
        $builder = $this->db->table('delegaciones');
        $builder->set('activo', 0);
        $builder->where('Id',$id);
        return $builder->update();
    }
}