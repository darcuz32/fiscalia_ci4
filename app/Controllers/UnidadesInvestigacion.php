<?php

namespace App\Controllers;

use App\Models\UnidadesInvestigacionModel;

class UnidadesInvestigacion extends MyController
{
    private UnidadesInvestigacionModel $unidadesInvestigacionModel;

    public function __construct()
    {
        $this->unidadesInvestigacionModel = new UnidadesInvestigacionModel();
    }

    public function get_activos_by_regiones(){
        $raw = $this->request->getGet('ids');
        $arr = is_array($raw) ? $raw : array_filter(explode(',', (string)$raw), 'strlen');
        return $this->response->setJSON($this->unidadesInvestigacionModel->get_activos_by_regiones($arr));
    }

    
    public function getUnidadesInvestigacion(){
		$data = $this->unidadesInvestigacionModel->get_all_activos();
		return $this->response->setJSON($data);
	}
}
