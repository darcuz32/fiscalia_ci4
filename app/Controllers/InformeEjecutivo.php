<?php

namespace App\Controllers;

use App\Models\InformeEjecutivoModel;
use DateTimeImmutable;

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

    public function getDenuncias()
    {
        return $this->responderMetrica('denuncias');
    }

    public function getDetenidos()
    {
        return $this->responderMetrica('detenidos');
    }

    public function getCateos()
    {
        return $this->responderMetrica('cateos');
    }

    public function getExpedientes()
    {
        return $this->responderMetrica('expedientes');
    }

    public function getMedidas()
    {
        return $this->responderMetrica('medidas');
    }

    public function getBienes()
    {
        return $this->responderMetrica('bienes');
    }

    public function getDroga()
    {
        return $this->responderMetrica('droga');
    }

    public function getUsuariosMpDigital()
    {
        return $this->response->setJSON(
            $this->informeEjecutivoModel->getUsuariosMpDigital()
        );
    }

    public function getDelitosMpDigital()
    {
        return $this->response->setJSON(
            $this->informeEjecutivoModel->getDelitosMpDigital()
        );
    }

    public function getPuntosMapa()
    {
        return $this->response->setJSON(
            $this->informeEjecutivoModel->getPuntosMapa($this->getFiltrosComunes())
        );
    }

    private function responderMetrica(string $metrica)
    {
        return $this->response->setJSON(
            $this->informeEjecutivoModel->getMetrica($metrica, $this->getFiltrosComunes())
        );
    }

    /**
     * Construye los filtros comunes y las ventanas de fecha.
     *
     * Para la gráfica comparativa se calcula el periodo inmediatamente
     * anterior con la misma duración que el periodo seleccionado.
     */
    private function getFiltrosComunes(): array
    {
        $periodo     = trim((string) $this->request->getGet('periodo'));
        $fechaInicio = $this->normalizarFecha((string) $this->request->getGet('fechaInicio'));
        $fechaFin    = $this->normalizarFecha((string) $this->request->getGet('fechaFin'));
        $esHistorico = $periodo === 'historico';

        $filtros = [
            'periodo'             => $periodo,
            'historico'           => $esHistorico,
            'region'              => trim((string) $this->request->getGet('region')),
            'municipio'           => trim((string) $this->request->getGet('municipio')),
            'delito'              => trim((string) $this->request->getGet('delito')),
            'unidad'              => trim((string) $this->request->getGet('unidad')),
            'usuario'             => trim((string) $this->request->getGet('usuario')),
            'fechaInicio'         => '',
            'fechaFin'            => '',
            'fechaInicioAnterior' => '',
            'fechaFinAnterior'    => '',
            'etiquetaActual'      => 'Histórico completo',
            'etiquetaAnterior'    => '',
        ];

        if ($esHistorico) {
            return $filtros;
        }

        $hoy = new DateTimeImmutable('today');

        if ($periodo === 'custom' && $fechaInicio !== null && $fechaFin !== null) {
            $inicio = new DateTimeImmutable($fechaInicio);
            $fin    = new DateTimeImmutable($fechaFin);
        } else {
            $fin = $hoy;

            switch ($periodo) {
                case 'lastweek':
                    $inicio = $hoy->modify('-7 days');
                    break;
                case 'yeartodate':
                    $inicio = $hoy->setDate((int) $hoy->format('Y'), 1, 1);
                    break;
                case 'lastmonth':
                default:
                    $inicio = $hoy->modify('-30 days');
                    break;
            }
        }

        if ($fin < $inicio) {
            [$inicio, $fin] = [$fin, $inicio];
        }

        $duracion    = $inicio->diff($fin)->days + 1;
        $finAnterior = $inicio->modify('-1 day');
        $inicioAnterior = $finAnterior->modify('-' . ($duracion - 1) . ' days');

        $filtros['fechaInicio']         = $inicio->format('Y-m-d');
        $filtros['fechaFin']            = $fin->format('Y-m-d');
        $filtros['fechaInicioAnterior'] = $inicioAnterior->format('Y-m-d');
        $filtros['fechaFinAnterior']    = $finAnterior->format('Y-m-d');
        $filtros['etiquetaActual']      = $inicio->format('d/m/Y') . ' - ' . $fin->format('d/m/Y');
        $filtros['etiquetaAnterior']    = $inicioAnterior->format('d/m/Y') . ' - ' . $finAnterior->format('d/m/Y');

        return $filtros;
    }

    private function normalizarFecha(string $fecha): ?string
    {
        $fecha = trim($fecha);

        if ($fecha === '') {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);

        if ($date === false || $date->format('Y-m-d') !== $fecha) {
            return null;
        }

        return $date->format('Y-m-d');
    }



    //Vista geografica

    public function vistaGeografica()
    {
        return $this->render('informe_ejecutivo_vista_geografica_view', [
            'googleMapsApiKey' => trim((string) env('google.maps.api_key', '')),
        ]);
    }
}
