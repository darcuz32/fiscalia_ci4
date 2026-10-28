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

    public function getCateosPorCorporacion()
    {
        return $this->response->setJSON(
            $this->informeEjecutivoModel->getCateosPorCorporacion($this->getFiltrosComunes())
        );
    }

    public function getCateosPorRegion()
    {
        return $this->response->setJSON(
            $this->informeEjecutivoModel->getCateosPorRegion($this->getFiltrosComunes())
        );
    }

    public function getExpedientesPorUnidad()
    {
        return $this->response->setJSON(
            $this->informeEjecutivoModel->getExpedientesPorUnidad($this->getFiltrosComunes())
        );
    }

    public function getMedidasProteccionPorDelito()
    {
        return $this->response->setJSON(
            $this->informeEjecutivoModel->getMedidasProteccionPorDelito($this->getFiltrosComunes())
        );
    }

    public function getUsuariosMpDigital()
    {
        return $this->response->setJSON(
            $this->informeEjecutivoModel->getUsuariosMpDigital()
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
            'estado'      => trim((string) $this->request->getGet('estado')),
            'usuario'     => trim((string) $this->request->getGet('usuario')),
        ];
    }
}
