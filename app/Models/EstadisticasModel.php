<?php

namespace App\Models;

use Config\Database;

class EstadisticasModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getOficinasGestiones()
    {
        return $this->db->table('oficinas o')
            ->select('o.Id, o.oficina')
            ->distinct()
            ->join('gestiones g', 'g.oficina = o.Id', 'inner')
            ->orderBy('o.oficina', 'ASC')
            ->get()
            ->getResult();
    }

    public function getMunicipios()
    {
        return $this->db->table('constancias_hechos')
            ->distinct()
            ->select('municipio')
            ->where('municipio IS NOT NULL', null, false)
            ->where('municipio !=', '')
            ->orderBy('municipio', 'ASC')
            ->get()
            ->getResult();
    }
    
}