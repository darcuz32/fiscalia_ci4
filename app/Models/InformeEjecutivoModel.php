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
            );

        $estado = $filters['estado'] ?? 'todos';
        if ($estado === 'activo') {
            $builder->where('d.status !=', 'cancelada');
        } elseif ($estado === 'inactivo') {
            $builder->where('d.status', 'cancelada');
        }

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
        if (!empty($filters['usuario'])) {
            $builder->where('d.created_by', (int) $filters['usuario']);
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

        $estado = $filters['estado'] ?? 'todos';
        if ($estado === 'activo') {
            $where[] = "d.status = 'detenido'";
        } elseif ($estado === 'inactivo') {
            $where[] = "d.status != 'detenido'";
        }

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
        if (!empty($filters['usuario'])) {
            $where[]  = 'd.created_by = ?';
            $params[] = (int) $filters['usuario'];
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
            ->select("COALESCE(md.nombre, CONCAT('Delito ', dd.id_delito)) AS delito, COUNT(*) AS total", false)
            ->join('detenidos d', 'd.id = dd.id_detenido', 'inner')
            ->join('multa_delitos md', 'md.id = dd.id_delito', 'left')
            ->join('catalogos.cat_municipios cm', 'cm.id = d.id_municipio', 'left')
            ->where('d.archivado', 0);

        $estado = $filters['estado'] ?? 'todos';
        if ($estado === 'activo') {
            $builder->where('d.status', 'detenido');
        } elseif ($estado === 'inactivo') {
            $builder->where('d.status !=', 'detenido');
        }

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
        if (!empty($filters['usuario'])) {
            $builder->where('d.created_by', (int) $filters['usuario']);
        }

        $rows = $builder->groupBy('dd.id_delito, md.nombre')
            ->orderBy('total', 'DESC')
            ->orderBy('dd.id_delito', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($rows)) {
            return [];
        }

        $top  = array_slice($rows, 0, 5);
        $rest = array_sum(array_map('intval', array_column(array_slice($rows, 5), 'total')));

        if ($rest > 0) {
            $top[] = [
                'delito' => 'Resto',
                'total'  => (string) $rest,
            ];
        }

        return $top;
    }

    /**
     * Expedientes agrupados por unidad de investigación.
     */
    public function getExpedientesPorUnidad(array $filters = [])
    {
        $builder = $this->mpdb->table('expedientes e')
            ->select("COALESCE(u.nombre, CONCAT('Unidad ', e.id_unidad_investigacion)) AS unidad, COUNT(*) AS total", false)
            ->join('denuncias_unidades_investigacion u', 'u.id = e.id_unidad_investigacion', 'left');

        $estado = $filters['estado'] ?? 'todos';
        if ($estado === 'activo') {
            $builder->where('e.concluido_at IS NULL', null, false);
        } elseif ($estado === 'inactivo') {
            $builder->where('e.concluido_at IS NOT NULL', null, false);
        }

        if (!empty($filters['fechaInicio'])) {
            $builder->where('e.created_at >=', $filters['fechaInicio'] . ' 00:00:00');
        }
        if (!empty($filters['fechaFin'])) {
            $builder->where('e.created_at <=', $filters['fechaFin'] . ' 23:59:59');
        }
        if (!empty($filters['region'])) {
            $builder->where('u.id_region', (int) $filters['region']);
        }
        if (!empty($filters['unidad'])) {
            $builder->where('e.id_unidad_investigacion', (int) $filters['unidad']);
        }
        if (!empty($filters['usuario'])) {
            $builder->where('e.created_by', (int) $filters['usuario']);
        }

        return $builder->groupBy('e.id_unidad_investigacion, u.nombre')
            ->orderBy('total', 'DESC')
            ->orderBy('e.id_unidad_investigacion', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Medidas de protección agrupadas por delito.
     *
     * Cada medida se cuenta una sola vez, usando el delito principal.
     */
    public function getMedidasProteccionPorDelito(array $filters = [])
    {
        $builder = $this->mpdb->table('denuncias_medidas m')
            ->select("COALESCE(NULLIF(m.delito, ''), 'Sin delito') AS delito, COUNT(*) AS total", false)
            ->join('denuncias d', 'd.Id = m.id_denuncia', 'left');

        $estado = $filters['estado'] ?? 'todos';
        if ($estado === 'activo') {
            $builder->where('m.deleted_at IS NULL', null, false);
        } elseif ($estado === 'inactivo') {
            $builder->where('m.deleted_at IS NOT NULL', null, false);
        }

        if (!empty($filters['fechaInicio'])) {
            $builder->where('m.fecha_emision >=', $filters['fechaInicio'] . ' 00:00:00');
        }
        if (!empty($filters['fechaFin'])) {
            $builder->where('m.fecha_emision <=', $filters['fechaFin'] . ' 23:59:59');
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
        if (!empty($filters['usuario'])) {
            $builder->where('m.id_usuario_emitio', (int) $filters['usuario']);
        }

        return $builder->groupBy('delito')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Cateos agrupados por corporación.
     *
     * La tabla cateos_corporaciones es la fuente de verdad para las
     * corporaciones que participan en cada cateo.
     */
    public function getCateosPorCorporacion(array $filters = [])
    {
        $builder = $this->mpdb->table('cateos c')
            ->select("COALESCE(corp.nombre, CONCAT('Corporación ', cc.id_corporacion)) AS corporacion, COUNT(DISTINCT c.id) AS total", false)
            ->join('cateos_corporaciones cc', 'cc.id_cateo = c.id', 'inner')
            ->join('detenidos_corporaciones corp', 'corp.id = cc.id_corporacion', 'left')
            ->join('detenidos_municipios mu', 'mu.id = c.id_municipio', 'left');

        $estado = $filters['estado'] ?? 'todos';
        if ($estado === 'activo') {
            $builder->where('c.activo', 1);
        } elseif ($estado === 'inactivo') {
            $builder->where('c.activo', 0);
        }

        if (!empty($filters['fechaInicio'])) {
            $builder->where('c.fecha >=', $filters['fechaInicio'] . ' 00:00:00');
        }
        if (!empty($filters['fechaFin'])) {
            $builder->where('c.fecha <=', $filters['fechaFin'] . ' 23:59:59');
        }
        if (!empty($filters['region'])) {
            $builder->where('c.id_region', (int) $filters['region']);
        }
        if (!empty($filters['municipio'])) {
            $builder->where('mu.nombre', $filters['municipio']);
        }
        if (!empty($filters['usuario'])) {
            $builder->where('c.created_by', (int) $filters['usuario']);
        }

        return $builder->groupBy('cc.id_corporacion, corp.nombre')
            ->orderBy('total', 'DESC')
            ->orderBy('cc.id_corporacion', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Cateos agrupados por región.
     *
     * Cada cateo pertenece a una sola región, por lo que la suma de los
     * totales coincide con el total de cateos del filtro.
     */
    public function getCateosPorRegion(array $filters = [])
    {
        $builder = $this->mpdb->table('cateos c')
            ->select("COALESCE(r.nombre, 'Sin región') AS region, COUNT(*) AS total", false)
            ->join('detenidos_regiones r', 'r.id = c.id_region', 'left')
            ->join('detenidos_municipios mu', 'mu.id = c.id_municipio', 'left');

        $estado = $filters['estado'] ?? 'todos';
        if ($estado === 'activo') {
            $builder->where('c.activo', 1);
        } elseif ($estado === 'inactivo') {
            $builder->where('c.activo', 0);
        }

        if (!empty($filters['fechaInicio'])) {
            $builder->where('c.fecha >=', $filters['fechaInicio'] . ' 00:00:00');
        }
        if (!empty($filters['fechaFin'])) {
            $builder->where('c.fecha <=', $filters['fechaFin'] . ' 23:59:59');
        }
        if (!empty($filters['region'])) {
            $builder->where('c.id_region', (int) $filters['region']);
        }
        if (!empty($filters['municipio'])) {
            $builder->where('mu.nombre', $filters['municipio']);
        }
        if (!empty($filters['usuario'])) {
            $builder->where('c.created_by', (int) $filters['usuario']);
        }

        return $builder->groupBy('c.id_region, r.nombre')
            ->orderBy('total', 'DESC')
            ->orderBy('c.id_region', 'ASC')
            ->get()
            ->getResultArray();
    }
}
