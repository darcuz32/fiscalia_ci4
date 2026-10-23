<?php

namespace App\Controllers;

use App\Models\AreasModel;

class Areas extends MyController
{
    private AreasModel $areasModel;

    public function __construct()
    {
        $this->areasModel = new AreasModel();
    }

    public function index()
    {
        return $this->render('areas_view');
    }

	function loaddata(){
		$datatable = new \App\Libraries\Datatable();

		$query='select id, nombre, activo
				from areas a
				where a.activo=1 ';

		$primaryKey = 'id';

		$columns = array(
		    array( 'db' => 'id', 'dt' => 0 ),
		    array( 'db' => 'nombre',  'dt' => 1),
		    array( 'db' => 'id', 'dt' => 2, 'formatter' => function( $d, $row ) {
					$html = "";
					
					$html.='<button class="btn btn-sm btn-warning btn-edit me-1" data-id="' . $d . '" data-nombre="' . $row["nombre"] . '"><i class="mdi mdi-pencil"></i> Editar</button>';

					$html.='<button class="btn btn-sm btn-danger btn-delete" data-id="' . $d . '"><i class="mdi mdi-delete"></i> Eliminar</button>';
					
		            return $html;
		        })
		);
		
		$data=$datatable->simple($this->request->getGet(), $query, $primaryKey, $columns);
		$data=$this->utf8_encode_deep($data);
		return $this->response->setJSON($data);
	}

	public function guardar() {
        $nombre = trim($this->request->getPost('nombre'));
        if (empty($nombre)) {
            return $this->response->setJSON(['success' => false, 'message' => 'El nombre es requerido']);
        }
        $id = $this->areasModel->guardar(['nombre' => $nombre]);
        return $this->response->setJSON(['success' => $id > 0]);
    }

	public function editar() {
        $id     = intval($this->request->getPost('id'));
        $nombre = trim($this->request->getPost('nombre'));
        if ($id <= 0 || empty($nombre)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Datos inválidos']);
        }
        $ok = $this->areasModel->actualizar($id, ['nombre' => $nombre]);
        return $this->response->setJSON(['success' => $ok]);
    }

    public function eliminar() {
        $id = intval($this->request->getPost('id'));
        if ($id <= 0) {
            return $this->response->setJSON(['success' => false]);
        }
        $ok = $this->areasModel->eliminar($id);
        return $this->response->setJSON(['success' => $ok]);
    }

    public function obtener() {
        $id  = intval($this->request->getGet('id'));
        $row = $this->areasModel->obtenerPorId($id);
        return $this->response->setJSON($row);
    }
}
