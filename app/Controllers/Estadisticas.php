<?php

namespace App\Controllers;

use App\Models\EstadisticasModel;

class Estadisticas extends MyController
{
    private EstadisticasModel $estadisticasModel;

    public function __construct()
    {
        $this->estadisticasModel = new EstadisticasModel();
    }
    

    public function getOficinasGestiones(){
		$data = $this->estadisticasModel->getOficinasGestiones();
		return $this->response->setJSON($data);
	}

    public function getMunicipios(){
		$data = $this->estadisticasModel->getMunicipios();
		return $this->response->setJSON($data);
	}
}
