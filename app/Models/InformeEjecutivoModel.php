<?php

namespace App\Models;

use Config\Database;

class InformeEjecutivoModel
{
    protected $db;
    protected $mpdb;

    public function __construct()
    {
        $this->db   = Database::connect();
        $this->mpdb = env('app.mpdigital_database_split') ? Database::connect('mpdigital') : Database::connect();
    }

    /**
     * Denuncias agrupadas por instrumento del delito.
     *
     * Se toma el primer instrumento capturado (slots 1 a 3) para que cada
     * denuncia cuente una sola vez en la gráfica.
     */
    public function getDenunciasPorInstrumento(array $filters = [])
    {
        $builder = $this->mpdb->table('denuncias d')
            ->select("COALESCE(i.nombre, 'Sin instrumento') AS instrumento, COUNT(d.Id) AS total", false)
            ->join(
                'denuncias_instrumentos_delito i',
                'i.id = COALESCE(d.id_instrumento_delito1, d.id_instrumento_delito2, d.id_instrumento_delito3)',
                'left',
                false
            )
            ->where('d.status !=', 'cancelada');

        if (!empty($filters['fechaInicio'])) {
            $builder->where('d.fecha_denuncia >=', $filters['fechaInicio']);
        }
        if (!empty($filters['fechaFin'])) {
            $builder->where('d.fecha_denuncia <=', $filters['fechaFin']);
        }
        if (!empty($filters['region'])) {
            $builder->where('d.id_region', (int) $filters['region']);
        }
        if (!empty($filters['municipio'])) {
            $builder->where('d.ciudad', $filters['municipio']);
        }
        if (!empty($filters['unidad'])) {
            $builder->where('d.id_unidad_investigacion', (int) $filters['unidad']);
        }

        return $builder->groupBy('instrumento')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Detenidos agrupados por rango de edad.
     *
     * Se usa `edad` cuando es válida; si no, se calcula con la fecha de
     * nacimiento. Los registros sin edad válida se agrupan en "Sin dato".
     * El filtro de unidad de investigación no aplica a detenidos.
     */
    public function getDetenidosPorRangoEdad(array $filters = [])
    {
        $where  = ['d.archivado = 0'];
        $params = [];

        if (!empty($filters['fechaInicio'])) {
            $where[]  = 'd.fecha_puesta_disposicion >= ?';
            $params[] = $filters['fechaInicio'] . ' 00:00:00';
        }
        if (!empty($filters['fechaFin'])) {
            $where[]  = 'd.fecha_puesta_disposicion <= ?';
            $params[] = $filters['fechaFin'] . ' 23:59:59';
        }
        if (!empty($filters['region'])) {
            $where[]  = 'd.id_region = ?';
            $params[] = (int) $filters['region'];
        }
        if (!empty($filters['municipio'])) {
            $where[]  = 'cm.nom_mun = ?';
            $params[] = $filters['municipio'];
        }

        $sql = "SELECT
                    CASE
                        WHEN t.edad_calculada IS NULL OR t.edad_calculada < 0 OR t.edad_calculada > 120 THEN 'Sin dato'
                        WHEN t.edad_calculada < 18 THEN '0-17'
                        WHEN t.edad_calculada < 26 THEN '18-25'
                        WHEN t.edad_calculada < 36 THEN '26-35'
                        WHEN t.edad_calculada < 46 THEN '36-45'
                        WHEN t.edad_calculada < 61 THEN '46-60'
                        ELSE '61+'
                    END AS rango,
                    COUNT(*) AS total
                FROM (
                    SELECT CASE
                        WHEN d.edad BETWEEN 0 AND 120 THEN d.edad
                        WHEN d.fecha_nacimiento IS NOT NULL AND d.fecha_nacimiento > '1900-01-01'
                            THEN TIMESTAMPDIFF(YEAR, d.fecha_nacimiento, CURDATE())
                        ELSE NULL
                    END AS edad_calculada
                    FROM detenidos d
                    LEFT JOIN catalogos.cat_municipios cm ON cm.id = d.id_municipio
                    WHERE " . implode(' AND ', $where) . "
                ) t
                GROUP BY rango
                ORDER BY FIELD(rango, '0-17', '18-25', '26-35', '36-45', '46-60', '61+', 'Sin dato')";

        return $this->mpdb->query($sql, $params)->getResultArray();
    }

    /**
     * Detenidos por delito: los 5 delitos con más registros y el resto
     * agrupado en una sola categoría.
     */
    public function getDetenidosPorDelito(array $filters = [])
    {
        $builder = $this->mpdb->table('detenidos_delitos dd')
            ->select('dd.id_delito, COUNT(*) AS total', false)
            ->join('detenidos d', 'd.id = dd.id_detenido', 'inner')
            ->join('catalogos.cat_municipios cm', 'cm.id = d.id_municipio', 'left')
            ->where('d.archivado', 0);

        if (!empty($filters['fechaInicio'])) {
            $builder->where('d.fecha_puesta_disposicion >=', $filters['fechaInicio'] . ' 00:00:00');
        }
        if (!empty($filters['fechaFin'])) {
            $builder->where('d.fecha_puesta_disposicion <=', $filters['fechaFin'] . ' 23:59:59');
        }
        if (!empty($filters['region'])) {
            $builder->where('d.id_region', (int) $filters['region']);
        }
        if (!empty($filters['municipio'])) {
            $builder->where('cm.nom_mun', $filters['municipio']);
        }

        $rows = $builder->groupBy('dd.id_delito')
            ->orderBy('total', 'DESC')
            ->orderBy('dd.id_delito', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($rows)) {
            return [];
        }

        $top  = array_slice($rows, 0, 5);
        $rest = array_sum(array_map('intval', array_column(array_slice($rows, 5), 'total')));

        $names = $this->db->table('mj_delitos')
            ->select('id, nombre')
            ->whereIn('id', array_column($top, 'id_delito'))
            ->get()
            ->getResultArray();

        $namesById = array_column($names, 'nombre', 'id');

        $result = [];
        foreach ($top as $row) {
            $result[] = [
                'delito' => $namesById[$row['id_delito']] ?? ('Delito ' . $row['id_delito']),
                'total'  => $row['total'],
            ];
        }

        if ($rest > 0) {
            $result[] = [
                'delito' => 'Resto',
                'total'  => (string) $rest,
            ];
        }

        return $result;
    }
}
