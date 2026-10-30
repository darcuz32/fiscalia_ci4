<?php

namespace App\Models;

use Config\Database;

class InformeEjecutivoModel
{
    protected $mpdb;
    protected $coredb;

    public function __construct()
    {
        $this->coredb = Database::connect();
        $this->mpdb   = env('app.mpdigital_database_split') ? Database::connect('mpdigital') : Database::connect();
    }

    /**
     * Usuarios activos que tienen un perfil de MP Digital asignado.
     */
    public function getUsuariosMpDigital()
    {
        return $this->coredb->table('users')
            ->select('Id, name, email, multas_mini_profile')
            ->where('multas_mini_profile IS NOT NULL', null, false)
            ->where('multas_mini_profile !=', '')
            ->where('active', 1)
            ->orderBy('multas_mini_profile', 'ASC')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Delitos activos del catálogo de MP Digital.
     */
    public function getDelitosMpDigital()
    {
        return $this->mpdb->table('multa_delitos')
            ->distinct()
            ->select('nombre')
            ->where('activo', 1)
            ->where('nombre IS NOT NULL', null, false)
            ->where('nombre !=', '')
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Consolidado de una métrica del informe ejecutivo.
     *
     * Toda la información (totales por estado, distribución por delito,
     * región y municipio, y comparativa contra el periodo inmediato anterior)
     * se obtiene con una sola consulta agrupada.
     */
    public function getMetrica(string $metrica, array $filters = []): ?array
    {
        $config = $this->getMetricaConfig($metrica);

        if ($config === null) {
            return null;
        }

        $esHistorico = ! empty($filters['historico']);
        $partesWhere = $this->buildMetricaWhere($config, $filters);
        $where       = $partesWhere['where'];
        $params      = $partesWhere['params'];
        $periodoExpr = $partesWhere['periodoExpr'];

        $sql = "SELECT
                    {$config['delito']} AS dim_delito,
                    {$config['region']} AS dim_region,
                    {$config['municipio']} AS dim_municipio,
                    {$config['estado']} AS dim_estado,
                    {$periodoExpr} AS dim_periodo,
                    {$config['valor']} AS dim_total
                FROM {$config['from']}
                WHERE " . implode(' AND ', $where) . "
                GROUP BY dim_delito, dim_region, dim_municipio, dim_estado, dim_periodo";

        $rows = $this->mpdb->query($sql, $params)->getResultArray();

        $esPeso      = ($config['unidad'] ?? '') === 'kg';
        $total       = 0.0;
        $activos     = 0.0;
        $concluidos  = 0.0;
        $actual      = 0.0;
        $anterior    = 0.0;
        $dimensiones = [
            'delito'    => [],
            'region'    => [],
            'municipio' => [],
        ];

        foreach ($rows as $row) {
            $valor = (float) ($row['dim_total'] ?? 0);

            if ($row['dim_periodo'] === 'anterior') {
                $anterior += $valor;
                continue;
            }

            $total  += $valor;
            $actual += $valor;

            if ($row['dim_estado'] === 'activo') {
                $activos += $valor;
            } else {
                $concluidos += $valor;
            }

            foreach (array_keys($dimensiones) as $dimension) {
                $nombre = trim((string) ($row['dim_' . $dimension] ?? '')) ?: 'Sin dato';
                $dimensiones[$dimension][$nombre] = ($dimensiones[$dimension][$nombre] ?? 0) + $valor;
            }
        }

        return [
            'metrica'      => $metrica,
            'unidad'       => $config['unidad'],
            'total'        => $this->normalizarValor($total, $esPeso),
            'activos'      => $this->normalizarValor($activos, $esPeso),
            'concluidos'   => $this->normalizarValor($concluidos, $esPeso),
            'porDelito'    => $this->dimensionToArray($dimensiones['delito'], $esPeso),
            'porRegion'    => $this->dimensionToArray($dimensiones['region'], $esPeso),
            'porMunicipio' => $this->dimensionToArray($dimensiones['municipio'], $esPeso),
            'comparativa'  => [
                'actual'           => $this->normalizarValor($actual, $esPeso),
                'anterior'         => $esHistorico ? null : $this->normalizarValor($anterior, $esPeso),
                'etiquetaActual'   => $filters['etiquetaActual'] ?? '',
                'etiquetaAnterior' => $esHistorico ? null : ($filters['etiquetaAnterior'] ?? ''),
            ],
        ];
    }

    /**
     * Construye el WHERE y los parámetros de una métrica.
     *
     * Se reutiliza tanto en el consolidado como en los puntos del mapa.
     * Cuando $soloPeriodoActual es true se excluye el periodo anterior,
     * que se usa únicamente para la comparativa.
     */
    private function buildMetricaWhere(array $config, array $filters, bool $soloPeriodoActual = false): array
    {
        $esHistorico = ! empty($filters['historico']);
        $fechaCol    = $config['fecha'];
        $where       = $config['where'];
        $params      = [];

        if ($esHistorico) {
            $where[]     = $fechaCol . ' IS NOT NULL';
            $periodoExpr = "'historico'";
        } else {
            $fechaDesde = $soloPeriodoActual
                ? $filters['fechaInicio']
                : $filters['fechaInicioAnterior'];

            $where[]  = $fechaCol . ' >= ?';
            $params[] = $fechaDesde . ' 00:00:00';
            $where[]  = $fechaCol . ' <= ?';
            $params[] = $filters['fechaFin'] . ' 23:59:59';

            $periodoExpr = "CASE WHEN {$fechaCol} >= '" . $filters['fechaInicio'] . " 00:00:00'"
                . " AND {$fechaCol} <= '" . $filters['fechaFin'] . " 23:59:59'"
                . " THEN 'actual' ELSE 'anterior' END";
        }

        if (!empty($filters['delito'])) {
            $where[]  = $config['delito'] . ' = ?';
            $params[] = $filters['delito'];
        }

        foreach ($config['filters'] as $key => $column) {
            if (empty($filters[$key])) {
                continue;
            }

            $where[]  = $column . ' = ?';
            $params[] = in_array($key, ['region', 'unidad', 'usuario'], true)
                ? (int) $filters[$key]
                : $filters[$key];
        }

        return [
            'where'       => $where,
            'params'      => $params,
            'periodoExpr' => $periodoExpr,
        ];
    }

    /**
     * Configuración SQL de cada métrica.
     *
     * `delito`, `region`, `municipio` y `estado` son expresiones que se usan
     * como dimensiones de agrupación. `fecha` define el campo temporal de la
     * métrica y `filters` los campos sobre los que aplican los filtros.
     */
    private function getMetricaConfig(string $metrica): ?array
    {
        $denunciasExpediente = "LEFT JOIN (
                    SELECT id_expediente, MIN(Id) AS id_denuncia
                    FROM denuncias
                    WHERE id_expediente IS NOT NULL
                    GROUP BY id_expediente
                ) dx ON dx.id_expediente = %s
                LEFT JOIN denuncias d ON d.Id = dx.id_denuncia";

        switch ($metrica) {
            case 'denuncias':
                return [
                    'from'      => "denuncias d
                        LEFT JOIN detenidos_regiones r ON r.id = d.id_region
                        LEFT JOIN denuncias_instrumentos_delito i ON i.id = COALESCE(d.id_instrumento_delito1, d.id_instrumento_delito2, d.id_instrumento_delito3)",
                    'delito'    => "COALESCE(NULLIF(d.delito1, ''), i.nombre, 'Sin delito')",
                    'region'    => "COALESCE(NULLIF(r.nombre, ''), 'Sin región')",
                    'municipio' => "COALESCE(NULLIF(d.ciudad, ''), 'Sin municipio')",
                    'estado'    => "CASE WHEN d.status = 'cancelada' THEN 'concluido' ELSE 'activo' END",
                    'fecha'     => 'd.fecha_denuncia',
                    'where'     => [],
                    'valor'     => 'COUNT(*)',
                    'id'        => 'd.Id',
                    'folio'     => 'd.folio',
                    'filters'   => [
                        'region'    => 'd.id_region',
                        'municipio' => 'd.ciudad',
                        'unidad'    => 'd.id_unidad_investigacion',
                        'usuario'   => 'd.created_by',
                    ],
                    'unidad'    => '',
                ];

            case 'detenidos':
                return [
                    'from'      => "detenidos d
                        LEFT JOIN detenidos_regiones r ON r.id = d.id_region
                        LEFT JOIN detenidos_municipios mu ON mu.id = d.id_municipio
                        LEFT JOIN detenidos_delitos dd ON dd.id_detenido = d.id
                            AND dd.orden = (SELECT MIN(dd2.orden) FROM detenidos_delitos dd2 WHERE dd2.id_detenido = d.id)
                        LEFT JOIN multa_delitos md ON md.id = dd.id_delito",
                    'delito'    => "COALESCE(NULLIF(md.nombre, ''), 'Sin delito')",
                    'region'    => "COALESCE(NULLIF(r.nombre, ''), 'Sin región')",
                    'municipio' => "COALESCE(NULLIF(mu.nombre, ''), 'Sin municipio')",
                    'estado'    => "CASE WHEN d.status = 'detenido' THEN 'activo' ELSE 'concluido' END",
                    'fecha'     => 'd.fecha_puesta_disposicion',
                    'where'     => ['d.archivado = 0'],
                    'valor'     => 'COUNT(*)',
                    'id'        => 'd.id',
                    'folio'     => 'd.folio',
                    'filters'   => [
                        'region'    => 'd.id_region',
                        'municipio' => 'mu.nombre',
                        'usuario'   => 'd.created_by',
                    ],
                    'unidad'    => '',
                ];

            case 'cateos':
                return [
                    'from'      => "cateos c
                        LEFT JOIN detenidos_regiones r ON r.id = c.id_region
                        LEFT JOIN detenidos_municipios mu ON mu.id = c.id_municipio
                        LEFT JOIN expedientes e ON e.id = c.id_expediente
                        " . sprintf($denunciasExpediente, 'c.id_expediente'),
                    'delito'    => "COALESCE(NULLIF(d.delito1, ''), 'Sin delito')",
                    'region'    => "COALESCE(NULLIF(r.nombre, ''), 'Sin región')",
                    'municipio' => "COALESCE(NULLIF(mu.nombre, ''), 'Sin municipio')",
                    'estado'    => "CASE WHEN c.activo = 1 THEN 'activo' ELSE 'concluido' END",
                    'fecha'     => 'c.fecha',
                    'where'     => [],
                    'valor'     => 'COUNT(*)',
                    'id'        => 'c.id',
                    'folio'     => 'c.nuc',
                    'filters'   => [
                        'region'    => 'c.id_region',
                        'municipio' => 'mu.nombre',
                        'unidad'    => 'e.id_unidad_investigacion',
                        'usuario'   => 'c.created_by',
                    ],
                    'unidad'    => '',
                ];

            case 'expedientes':
                return [
                    'from'      => "expedientes e
                        LEFT JOIN denuncias_unidades_investigacion u ON u.id = e.id_unidad_investigacion
                        LEFT JOIN detenidos_regiones r ON r.id = u.id_region
                        " . sprintf($denunciasExpediente, 'e.id'),
                    'delito'    => "COALESCE(NULLIF(d.delito1, ''), 'Sin delito')",
                    'region'    => "COALESCE(NULLIF(r.nombre, ''), 'Sin región')",
                    'municipio' => "COALESCE(NULLIF(d.ciudad, ''), 'Sin municipio')",
                    'estado'    => "CASE WHEN e.concluido_at IS NULL THEN 'activo' ELSE 'concluido' END",
                    'fecha'     => 'e.created_at',
                    'where'     => [],
                    'valor'     => 'COUNT(*)',
                    'filters'   => [
                        'region'    => 'u.id_region',
                        'municipio' => 'd.ciudad',
                        'unidad'    => 'e.id_unidad_investigacion',
                        'usuario'   => 'e.created_by',
                    ],
                    'unidad'    => '',
                ];

            case 'medidas':
                return [
                    'from'      => "denuncias_medidas m
                        LEFT JOIN denuncias d ON d.Id = m.id_denuncia
                        LEFT JOIN detenidos_regiones r ON r.id = d.id_region",
                    'delito'    => "COALESCE(NULLIF(m.delito, ''), 'Sin delito')",
                    'region'    => "COALESCE(NULLIF(r.nombre, ''), 'Sin región')",
                    'municipio' => "COALESCE(NULLIF(d.ciudad, ''), 'Sin municipio')",
                    'estado'    => "CASE WHEN m.deleted_at IS NULL THEN 'activo' ELSE 'concluido' END",
                    'fecha'     => 'm.fecha_emision',
                    'where'     => [],
                    'valor'     => 'COUNT(*)',
                    'filters'   => [
                        'region'    => 'd.id_region',
                        'municipio' => 'd.ciudad',
                        'unidad'    => 'd.id_unidad_investigacion',
                        'usuario'   => 'm.id_usuario_emitio',
                    ],
                    'unidad'    => '',
                ];

            case 'bienes':
                return [
                    'from'      => "bienes_asegurados_objetos o
                        INNER JOIN bienes_asegurados b ON b.id = o.id_bien_asegurado
                        LEFT JOIN expedientes e ON e.id = b.id_expediente
                        " . sprintf($denunciasExpediente, 'b.id_expediente') . "
                        LEFT JOIN detenidos_regiones r ON r.id = b.id_region
                        LEFT JOIN cateos cat ON cat.id = b.id_cateo
                        LEFT JOIN detenidos_municipios mu ON mu.id = cat.id_municipio",
                    'delito'    => "COALESCE(NULLIF(d.delito1, ''), 'Sin delito')",
                    'region'    => "COALESCE(NULLIF(r.nombre, ''), 'Sin región')",
                    'municipio' => "COALESCE(NULLIF(mu.nombre, ''), 'Sin municipio')",
                    'estado'    => "CASE WHEN o.activo = 1 AND b.activo = 1 THEN 'activo' ELSE 'concluido' END",
                    'fecha'     => 'b.fecha_aseguramiento',
                    'where'     => [],
                    'valor'     => 'COUNT(*)',
                    'filters'   => [
                        'region'    => 'b.id_region',
                        'municipio' => 'mu.nombre',
                        'unidad'    => 'e.id_unidad_investigacion',
                        'usuario'   => 'b.created_by',
                    ],
                    'unidad'    => '',
                ];

            case 'droga':
                return [
                    'from'      => "bienes_asegurados_objetos o
                        INNER JOIN bienes_asegurados b ON b.id = o.id_bien_asegurado
                        LEFT JOIN expedientes e ON e.id = b.id_expediente
                        " . sprintf($denunciasExpediente, 'b.id_expediente') . "
                        LEFT JOIN detenidos_regiones r ON r.id = b.id_region
                        LEFT JOIN cateos cat ON cat.id = b.id_cateo
                        LEFT JOIN detenidos_municipios mu ON mu.id = cat.id_municipio",
                    'delito'    => "COALESCE(NULLIF(d.delito1, ''), 'Sin delito')",
                    'region'    => "COALESCE(NULLIF(r.nombre, ''), 'Sin región')",
                    'municipio' => "COALESCE(NULLIF(mu.nombre, ''), 'Sin municipio')",
                    'estado'    => "CASE WHEN o.activo = 1 AND b.activo = 1 THEN 'activo' ELSE 'concluido' END",
                    'fecha'     => 'b.fecha_aseguramiento',
                    'where'     => [
                        "o.tipo_objeto = 'narcotico'",
                        'o.narcotico_peso IS NOT NULL',
                    ],
                    'valor'     => 'SUM(o.narcotico_peso)',
                    'filters'   => [
                        'region'    => 'b.id_region',
                        'municipio' => 'mu.nombre',
                        'unidad'    => 'e.id_unidad_investigacion',
                        'usuario'   => 'b.created_by',
                    ],
                    'unidad'    => 'kg',
                ];
        }

        return null;
    }

    /**
     * Puntos georreferenciados de las métricas que se muestran en el mapa.
     *
     * Reutiliza la configuración y los filtros de getMetrica()/getMetricaConfig().
     */
    public function getPuntosMapa(array $filters = []): array
    {
        $resultado = [];

        foreach (['denuncias', 'detenidos', 'cateos'] as $metrica) {
            $resultado[$metrica] = $this->getPuntosMetrica($metrica, $filters);
        }

        return $resultado;
    }

    private function getPuntosMetrica(string $metrica, array $filters): array
    {
        $config = $this->getMetricaConfig($metrica);

        if ($config === null) {
            return ['total' => 0, 'puntos' => []];
        }

        $partes = $this->buildMetricaWhere($config, $filters, true);

        $select = [
            $config['id'] . ' AS id',
            $config['folio'] . ' AS folio',
            $config['delito'] . ' AS delito',
            $config['region'] . ' AS region',
            $config['municipio'] . ' AS municipio',
            $config['estado'] . ' AS estado',
            $config['fecha'] . ' AS fecha',
        ];

        $from = $config['from'];

        switch ($metrica) {
            case 'denuncias':
                // La tabla de denuncias no almacena latitud/longitud.
                $select[] = 'NULL AS lat';
                $select[] = 'NULL AS lng';
                $select[] = 'd.calle_hechos AS calle';
                $select[] = 'd.colonia_hechos AS colonia';
                break;

            case 'detenidos':
                $select[] = 'det.lugar_coordenadas_lat AS lat';
                $select[] = 'det.lugar_coordenadas_lng AS lng';
                $select[] = 'det.lugar_calle AS calle';
                $select[] = 'det.lugar_colonia AS colonia';
                $from    .= ' LEFT JOIN detenciones det ON det.id = d.id_detencion';
                break;

            case 'cateos':
                $select[] = 'c.latitud AS lat';
                $select[] = 'c.longitud AS lng';
                $select[] = 'c.calle AS calle';
                $select[] = 'c.colonia AS colonia';
                break;
        }

        $sql = 'SELECT ' . implode(', ', $select)
            . ' FROM ' . $from
            . ' WHERE ' . implode(' AND ', $partes['where']);

        $rows      = $this->mpdb->query($sql, $partes['params'])->getResultArray();
        $registros = count($rows);

        $puntos = [];

        foreach ($rows as $row) {
            if (!$this->esCoordenadaValida($row['lat'] ?? null, $row['lng'] ?? null)) {
                continue;
            }

            $puntos[] = [
                'id'        => (int) $row['id'],
                'folio'     => $row['folio'],
                'delito'    => $row['delito'],
                'region'    => $row['region'],
                'municipio' => $row['municipio'],
                'estado'    => $row['estado'],
                'fecha'     => $row['fecha'],
                'calle'     => $row['calle'],
                'colonia'   => $row['colonia'],
                'lat'       => (float) $row['lat'],
                'lng'       => (float) $row['lng'],
            ];
        }

        return [
            'total'     => count($puntos),
            'registros' => $registros,
            'puntos'    => $puntos,
        ];
    }

    private function esCoordenadaValida($lat, $lng): bool
    {
        if (!is_numeric($lat) || !is_numeric($lng)) {
            return false;
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        // Límites aproximados de México. Descarta ceros y valores de prueba.
        return $lat >= 14.0 && $lat <= 33.0 && $lng >= -118.0 && $lng <= -86.0;
    }

    private function normalizarValor(float $valor, bool $esPeso): float|int
    {
        return $esPeso ? round($valor, 3) : (int) round($valor);
    }

    /**
     * Convierte un acumulado [nombre => valor] en una lista ordenada.
     * Se conservan los 12 valores principales y el resto se agrupa en "Otros".
     */
    private function dimensionToArray(array $values, bool $esPeso, int $limit = 12): array
    {
        arsort($values);

        $total     = array_sum($values);
        $items     = [];
        $acumulado = 0.0;

        foreach ($values as $name => $value) {
            if (count($items) >= $limit) {
                break;
            }

            $items[]    = ['name' => $name, 'total' => $this->normalizarValor($value, $esPeso)];
            $acumulado += $value;
        }

        if (count($values) > $limit) {
            $resto = $total - $acumulado;

            if ($resto > 0) {
                $items[] = ['name' => 'Otros', 'total' => $this->normalizarValor($resto, $esPeso)];
            }
        }

        return $items;
    }
}
