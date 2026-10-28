<?php

namespace App\Controllers;

use App\Models\InformeEjecutivoModel;

class InformeEjecutivo extends MyController
{
    private InformeEjecutivoModel $informeEjecutivoModel;

    public function __construct()
    {
        $this->informeEjecutivoModel = new InformeEjecutivoModel();
    }

    public function index()
    {
        return $this->render('informe_ejecutivo_view');
    }

    public function getDenunciasPorInstrumento()
    {
        return $this->response->setJSON(
            $this->informeEjecutivoModel->getDenunciasPorInstrumento($this->getFiltrosComunes())
        );
    }

    public function getDetenidosPorRangoEdad()
    {
        return $this->response->setJSON(
            $this->informeEjecutivoModel->getDetenidosPorRangoEdad($this->getFiltrosComunes())
        );
    }

    public function getDetenidosPorDelito()
    {
        return $this->response->setJSON(
            $this->informeEjecutivoModel->getDetenidosPorDelito($this->getFiltrosComunes())
        );
    }

    private function getFiltrosComunes(): array
    {
        return [
            'fechaInicio' => trim((string) $this->request->getGet('fechaInicio')),
            'fechaFin'    => trim((string) $this->request->getGet('fechaFin')),
            'region'      => trim((string) $this->request->getGet('region')),
            'municipio'   => trim((string) $this->request->getGet('municipio')),
            'unidad'      => trim((string) $this->request->getGet('unidad')),
        ];
    }
}
