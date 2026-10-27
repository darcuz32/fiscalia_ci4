<?php

namespace App\Controllers;

use App\Models\ConstanciasTitularesModel;

class ConstanciasTitulares extends MyController
{
    private ConstanciasTitularesModel $constanciasTitularesModel;

    public function __construct()
    {
        $this->constanciasTitularesModel = new ConstanciasTitularesModel();
    }

    public function index()
    {
        return $this->render('constancias_titulares_view');
    }

	function loaddata(){
		$datatable = new \App\Libraries\Datatable();

		$query='select Id, region, agente, firma, domicilio, telefono
        from delegaciones where activo = 1';
		$primaryKey = 'Id';

		$columns = array(
		    array( 'db' => 'region', 'dt' => 0 ),
            array( 'db' => 'Id', 'dt' => 1, 'formatter' => function( $d, $row ) {
					$html = "";
					$html.='<a type="button" class="btn btn-primary actualizar" style="color: white;" title="Actualizar" data-id="'.$d.'" href="#">Actualizar<i class="bx bxs-edit-alt"></i></a>&nbsp;';

		            return $html;
		        }),
		    array( 'db' => 'agente',  'dt' => 2),
		    array( 'db' => 'firma',  'dt' => 3, 'formatter' => function( $d, $row ) {
					$html = "<img src='".base_url()."sources/firmas_constancias/".$row['firma']."' style='width: 100px; height: 50px;' alt='Firma' />";
		            return $html;
		        }),
		    array( 'db' => 'domicilio',  'dt' => 4),
		    array( 'db' => 'telefono', 'dt' => 5)
		);
		
		$data=$datatable->simple($this->request->getGet(), $query, $primaryKey, $columns);
		$data=$this->utf8_encode_deep($data);
		return $this->response->setJSON($data);
	}

	public function actualizar() {
        $id     = intval($this->request->getPost('id'));
        $data = array();
        $data['delegacion'] = $this->constanciasTitularesModel->obtenerdelegacion($id);
        return $this->render('constancias_titularesnuevo_view', $data);
    }

    function guardar(){
		$data=array();
		parse_str($this->request->getPost('data'), $data);

        // Obtener la delegación de origen
        $delegacion = $this->constanciasTitularesModel->obtenerdelegacion($data['id']);
        // Desactivar la delegación de origen
        $this->constanciasTitularesModel->desactivardelegacion($data['id']);
        // Guardar la nueva delegación
        $firma = $data['firma'];
		unset($data['firma']);
        unset($data['id']);
        $data['activo'] = 1;
        $data['creationdate'] = date('Y-m-d H:i:s');
        $data['createdby'] = session()->get('userid');
        $data['region'] = $delegacion->region;
        $id = $this->constanciasTitularesModel->guardar($data);

        $firma_name = $id.'_Firma.png';
        $this->constanciasTitularesModel->actualizar($id, array('firma' => $firma_name));

        if($firma != ""){
            $folder = getcwd().'/sources/firmas_constancias/';
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }
            $base64 = str_replace('data:image/png;base64,', '', $firma);
            $base64 = str_replace(' ', '+', $base64);
            $base64 = base64_decode($base64);
            $file = $folder.$firma_name;
            file_put_contents($file, $base64);
        }

        return $this->response->setBody('ok');
	}
}
