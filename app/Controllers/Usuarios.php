<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use App\Models\UnidadesInvestigacionModel;
use App\Libraries\MultasAccessScope;
use App\Libraries\Ssoauth;
use Config\Database;
use Throwable;

class Usuarios extends MyController
{
    private UsuariosModel $usuariosModel;
    private UnidadesInvestigacionModel $unidadesInvestigacionModel;
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->usuariosModel = new UsuariosModel();
		$this->unidadesInvestigacionModel = new UnidadesInvestigacionModel();
    }

    public function index()
    {
        return $this->render('usuarios_view', [
			'alertas_adscripcion' => $this->usuariosModel->alertas_adscripcion_expedientes(),
			'puede_auditar_expedientes' => in_array(
				MultasAccessScope::PERMISSION_AUDIT_EXPEDIENTES,
				(array) session()->get('special_permissions'), true
			),
		]);
    }

	function loaddata(){
		$datatable = new \App\Libraries\Datatable();

		if (!$this->_hasModule('usuarios')) { 
            return $this->response
                        ->setStatusCode(403)
                        ->setJSON(['data' => []]);
        }

		$modules       = session()->get('modules') ?: [];
		$is_empleado   = session()->get('is_empleado');
        
		$content_modules = ['buss','registro','registro_mp','constancias','constancias_titulares',
		                    'citas','gestiones','multas','mandamientos','instituto',
		                    'instituto_personal','instituto_seguimiento','estadisticas',
		                    'colaboracionforanea','motivos_visita','areas'];

		$comisiones_only = in_array('comisiones', $modules)
		                   && empty(array_intersect($modules, $content_modules));

		$where = $comisiones_only ? "a.active=1 AND a.idprofile=3" : "a.active=1";

		$query = 'SELECT a.Id, a.name, a.email, a.password,
				COALESCE(
					GROUP_CONCAT(DISTINCT mo.label ORDER BY mo.sort_order SEPARATOR ", "),
					IF(a.idprofile=3,"Empleado","Sin m\u00f3dulos")
				) as profile,
				a.active,
				DATE_FORMAT(a.creationdate,"%d/%m/%Y %h:%i %p") as creationdate,
				a.Id as orden,
				a.region,
					CASE a.multas_mini_profile
						WHEN "director" THEN "Director"
						WHEN "fiscal_general_delegado" THEN "Fiscal - Delegado"
						WHEN "fiscal_especializado_coordinador" THEN "Coordinador"
					WHEN "ministerio_publico" THEN "Ministerio Público"
					ELSE "-"
				END AS mini_profile,
					GROUP_CONCAT(DISTINCT COALESCE(c.region, dn.nombre)) as nombre_region,
					GROUP_CONCAT(DISTINCT ui.nombre ORDER BY ui.nombre SEPARATOR ", ") as unidades_investigacion
			FROM users a
			LEFT JOIN user_modules um ON um.id_usuario = a.Id
			LEFT JOIN modules mo ON mo.Id = um.id_module
			LEFT JOIN usuarios_delegaciones d ON d.id_usuario = a.Id
			LEFT JOIN delegaciones c ON (d.region = c.region OR c.Id = d.id_region) AND c.activo = 1
			LEFT JOIN fiscalia_mpdigital.detenidos_regiones dn ON dn.id = d.id_region
				LEFT JOIN usuarios_unidades_investigacion aui ON aui.id_usuario = a.Id
				LEFT JOIN fiscalia_mpdigital.denuncias_unidades_investigacion ui ON ui.id = aui.id_unidad_investigacion AND ui.activo = 1
			WHERE ' . $where . '
			GROUP BY a.Id';
		$primaryKey = 'Id';

		$columns = array(
		    array( 'db' => 'orden', 'dt' => 0 ),
		    array( 'db' => 'name',  'dt' => 1),
		    array( 'db' => 'email',  'dt' => 2),
		    array( 'db' => 'region', 'dt' => 4, 'formatter' => function( $d, $row ) {
				$region = $row['nombre_region'];
				return $region;
			}),
		    array( 'db' => 'creationdate',  'dt' => 5),
		    array( 'db' => 'Id', 'dt' => 6, 'formatter' => function( $d, $row ) {
					$html = "";
					
					$html.='<a  type="button" class="btn btn-primary" style="color: white;"   title="Editar" href="'.base_url().'usuarios/editar/'.$d.'">Editar<i class="bx bxs-edit-alt"></i></a>&nbsp;';

					$html.='<a  type="button" class="btn btn-danger eliminar" style="color: white;"   title="Eliminar" href="#" data-id="'.$d.'">Eliminar<i class="bx bxs-trash"></i></a>';
					
		            return $html;
		        }),
			    array( 'db' => 'mini_profile', 'dt' => 7, 'formatter' => function( $d, $row ) {
					$profile = htmlspecialchars($d, ENT_QUOTES, 'UTF-8');
					$unidades = trim($row['unidades_investigacion'] ?? '');
					return $unidades === '' ? $profile : $profile . '<br><small class="text-muted">' . htmlspecialchars($unidades, ENT_QUOTES, 'UTF-8') . '</small>';
				})
		);
		
		$data=$datatable->simple($this->request->getGet(), $query, $primaryKey, $columns);
		$data=$this->utf8_encode_deep($data);
		return $this->response->setJSON($data);
	}

	function nuevo(){
		$data['modulos']=$this->usuariosModel->obtenermodulos();
		$data['regiones']=$this->usuariosModel->obtenerregiones();
		$data['oficinas']=$this->usuariosModel->obteneroficinas();
		$data['multa_regiones']=$this->usuariosModel->obtenerregionesmultas();
		$data['dashboard_regiones']=$this->usuariosModel->obtener_regiones_dashboard();
		// Unidades investigación de Multas — populated by JS as the admin picks regiones.
		$data['unidades_investigacion_multa_catalogo'] = [];
		$data['unidades_investigacion_multa_seleccionadas'] = [];
		$data['multas_mini_profiles'] = MultasAccessScope::profiles();
		return $this->render('usuariosnuevo_view', $data);
	}
	function guardar(){
		if (!$this->_hasModule('usuarios')) { 
            return $this->response
                        ->setStatusCode(403)
                        ->setBody('error|No autorizado');
        }
		$data=array();
		parse_str($this->request->getPost('data'), $data);
		$usuario=$this->usuariosModel->comprobarusuario($data['email'], 0);
		if(isset($usuario->Id)){
			return $this->response->setBody('repetido');
		}else{
			$this->db->transBegin();
			$firma = $data['firma'];
			unset($data['firma']);

			$is_empleado = isset($data['is_empleado']) && $data['is_empleado'] == '1';
			unset($data['is_empleado']);
			$modulos = isset($data['modulos']) ? (array)$data['modulos'] : [];
			$modulos = $this->_normalizeModuleHierarchy($modulos);
			unset($data['modulos']);

			if(isset($data['passwordcorreo']) && $data['passwordcorreo'] !== ''){
				$data['passwordcorreo'] = $this->encrypt_password($data['passwordcorreo']);
			}else{
				unset($data['passwordcorreo']);
			}
			$regiones = isset($data['region']) ? $data['region'] : null;
			unset($data['region']);
			$oficinas = isset($data['oficina']) ? $data['oficina'] : null;
			unset($data['oficina']);
			$multa_delegaciones = isset($data['region_multa']) ? $data['region_multa'] : null;
			unset($data['region_multa']);
			$uids_multa = isset($data['unidades_investigacion_multa']) ? (array)$data['unidades_investigacion_multa'] : [];
			unset($data['unidades_investigacion_multa']);
			$mini_profile = isset($data['multas_mini_profile']) ? trim($data['multas_mini_profile']) : null;
			unset($data['multas_mini_profile']);
			$dashboard_regiones = isset($data['dashboard_regiones']) ? $data['dashboard_regiones'] : null;
			unset($data['dashboard_regiones']);
			$permisos_especiales_raw = isset($data['special_permissions']) ? (array)$data['special_permissions'] : [];
			unset($data['special_permissions']);
				[$profile_error, $multa_delegaciones, $valid_uids, $assignment_type] = $this->_normalizeMultasAssignment(
					$is_empleado, $modulos, $mini_profile, (array)$multa_delegaciones, $uids_multa, 0
				);
				if ($profile_error) { 
					$this->db->transRollback();
    				return $this->response->setBody('error|' . $profile_error);
				}
				$effective_profile = $this->_usesScopedMultasModules($is_empleado, $modulos) ? $mini_profile : null;
				$permisos_especiales_raw = $this->_sanitizeSpecialPermissions($permisos_especiales_raw, $effective_profile, $modulos);

			$data['idprofile'] = $is_empleado ? 3 : null;
			$data['multas_mini_profile'] = $effective_profile;
			$data['changedpassword']='0';
			$data['password']=password_hash(hash("sha512", $data['password']), PASSWORD_DEFAULT);
			$id = $this->usuariosModel->guardar($data);

			if(!$is_empleado){
				$this->usuariosModel->guardarmodulos($id, $modulos);
			}

			$regionesData = [];
			
			if($regiones !== null){
				foreach($regiones as $key => $value){
					$regionesData[] = array(
						"id_usuario" => $id,
						"region" => $value
					);
				}
			}
			$this->usuariosModel->guardarregiones($id, $regionesData);

			$multaDelegacionesData = [];
			if($multa_delegaciones !== null){
				foreach($multa_delegaciones as $key => $value){
					$multaDelegacionesData[] = array(
						"id_usuario" => $id,
						"id_region" => $value
					);
				}
			}
			$this->usuariosModel->guardarregionesmultas($id, $multaDelegacionesData);

			// Unidades de investigación de Multas — filter against the just-saved regiones
			// so any submitted unidad whose region was deselected is dropped on save.
				$uidsData = array_map(fn($uid) => ['id_usuario' => $id, 'id_unidad_investigacion' => (int)$uid, 'tipo_adscripcion' => $assignment_type], $valid_uids);
			$this->usuariosModel->guardar_unidades_investigacion($id, $uidsData);

			$dashboardRegionesData = [];
			if($dashboard_regiones !== null){
				foreach($dashboard_regiones as $value){
					$dashboardRegionesData[] = array(
						"id_usuario" => $id,
						"id_region" => $value
					);
				}
			}
			$this->usuariosModel->guardar_regiones_dashboard($id, $dashboardRegionesData);

			$oficinasData = [];
			
			if($oficinas !== null){
				foreach($oficinas as $key => $value){
					$oficinasData[] = array(
						"id_usuario" => $id,
						"id_oficina" => $value
					);
				}
			}
			$this->usuariosModel->guardaroficinas($id, $oficinasData);

			$this->usuariosModel->guardar_permisos_especiales($id, $permisos_especiales_raw);

			if ($this->db->transStatus() === FALSE) {
				$this->db->transRollback();
				return $this->response->setBody(
					'error|No se pudo guardar la configuración del usuario.'
				);
			}
			$this->db->transCommit();

			if($firma != ""){
				$folder = getcwd().'/uploads_general/firmas/'.$id;
				if (!file_exists($folder)) {
					mkdir($folder, 0777, true);
				}
				$base64 = str_replace('data:image/png;base64,', '', $firma);
				$base64 = str_replace(' ', '+', $base64);
				$base64 = base64_decode($base64);
				$file = $folder.'/Firma.png';
				file_put_contents($file, $base64);
			}

			return $this->response->setBody('ok');
			//redirect(base_url().'usuarios');
		}
	}
	function editar($id){
		$data['modulos']=$this->usuariosModel->obtenermodulos();
		$data['usuario']=$this->usuariosModel->obtenerusuario($id);
		$data['regiones']=$this->usuariosModel->obtenerregiones();
		$data['oficinas']=$this->usuariosModel->obteneroficinas();
		$data['multa_regiones']=$this->usuariosModel->obtenerregionesmultas();
		$data['dashboard_regiones']=$this->usuariosModel->obtener_regiones_dashboard();
		$data['usuario']->modulos = $this->usuariosModel->obtenermodulosusuario($id);
		$data['usuario']->regiones = $this->usuariosModel->obtenerregionesusuario($id);
		$data['usuario']->regiones = array_map(function($delegacion) {
			return $delegacion->region;
		}, $data['usuario']->regiones);
		$data['usuario']->oficinas = $this->usuariosModel->obteneroficinasusuario($id);
		$data['usuario']->oficinas = array_map(function($oficina) {
			return $oficina->id_oficina;
		}, $data['usuario']->oficinas);
		$data['usuario']->multa_regiones = $this->usuariosModel->obtenerregionesmultasusuario($id);
		$data['usuario']->multa_regiones = array_map(function($region) {
			return $region->id_region;
		}, $data['usuario']->multa_regiones);
		$data['usuario']->dashboard_regiones = $this->usuariosModel->obtener_dashboard_regiones_usuario($id);
		$data['usuario']->dashboard_regiones = array_map(function($region) {
			return $region->id_region;
		}, $data['usuario']->dashboard_regiones);

		// Unidades investigación de Multas — seed the dropdown from the user's saved regiones
		// so the field is populated on page load (no client AJAX needed for initial state).
		$uids_multa = $this->usuariosModel->obtener_unidades_investigacion_usuario($id);
		$data['unidades_investigacion_multa_seleccionadas'] = array_map(fn($r) => (int)$r->id, $uids_multa);
		$data['unidades_investigacion_multa_catalogo'] = $this->unidadesInvestigacionModel->get_activos_by_regiones($data['usuario']->multa_regiones);

		$data['usuario']->special_permissions = $this->usuariosModel->obtener_permisos_especiales($id);
		$data['multas_mini_profiles'] = MultasAccessScope::profiles();

		if(file_exists(getcwd().'/uploads_general/firmas/'.$id.'/Firma.png')){
			$data['usuario']->firma = base_url().'/uploads_general/firmas/'.$id.'/Firma.png';
		}
		return $this->render('usuarioseditar_view', $data);
	}
	function actualizar($id){
		if (!$this->_hasModule('usuarios')) { 
            return $this->response
                        ->setStatusCode(403)
                        ->setBody('error|No autorizado');
        }
		
		$data=array();
		parse_str($this->request->getPost('data'), $data);
		$passwordChanged = isset($data['password']) && $data['password'] !== '';
		$usuario=$this->usuariosModel->comprobarusuario($data['email'], $id);
		if(isset($usuario->Id)){
			return $this->response->setBody('repetido');
		}else{
			$this->db->transBegin();
			$firma = $data['firma'] ?? '';
			unset($data['firma']);

			$is_empleado = isset($data['is_empleado']) && $data['is_empleado'] == '1';
			unset($data['is_empleado']);
			$modulos = isset($data['modulos']) ? (array)$data['modulos'] : [];
			$modulos = $this->_normalizeModuleHierarchy($modulos);
			unset($data['modulos']);

			$regiones = isset($data['region']) ? $data['region'] : null;
			unset($data['region']);
			$oficinas = isset($data['oficina']) ? $data['oficina'] : null;
			unset($data['oficina']);
			$multa_delegaciones = isset($data['region_multa']) ? $data['region_multa'] : null;
			unset($data['region_multa']);
			$uids_multa = isset($data['unidades_investigacion_multa']) ? (array)$data['unidades_investigacion_multa'] : [];
			unset($data['unidades_investigacion_multa']);
			$mini_profile = isset($data['multas_mini_profile']) ? trim($data['multas_mini_profile']) : null;
			unset($data['multas_mini_profile']);
			$dashboard_regiones = isset($data['dashboard_regiones']) ? $data['dashboard_regiones'] : null;
			unset($data['dashboard_regiones']);
			$permisos_especiales_raw = isset($data['special_permissions']) ? (array)$data['special_permissions'] : [];
			unset($data['special_permissions']);
			if(!isset($data['password']) || $data['password']==""){
				unset($data['password']);
			}else{
				$data['password']=password_hash(hash("sha512", $data['password']), PASSWORD_DEFAULT);
			}

			if(!isset($data['passwordcorreo']) || $data['passwordcorreo'] == ""){
				unset($data['passwordcorreo']);
			}else{
				$data['passwordcorreo'] = $this->encrypt_password($data['passwordcorreo']);
			}

				[$profile_error, $multa_delegaciones, $valid_uids, $assignment_type] = $this->_normalizeMultasAssignment(
					$is_empleado, $modulos, $mini_profile, (array)$multa_delegaciones, $uids_multa, (int)$id
				);
				
				if ($profile_error) {
					$this->db->transRollback();
					return $this->response->setBody('error|' . $profile_error);
				}
				$effective_profile = $this->_usesScopedMultasModules($is_empleado, $modulos) ? $mini_profile : null;
				$permisos_especiales_raw = $this->_sanitizeSpecialPermissions($permisos_especiales_raw, $effective_profile, $modulos);

			$data['idprofile'] = $is_empleado ? 3 : null;
			$data['multas_mini_profile'] = $effective_profile;
			$this->usuariosModel->actualizar($id, $data);

			if(!$is_empleado){
				$this->usuariosModel->guardarmodulos($id, $modulos);
			}

			$regionesData = [];
			if($regiones !== null){
				foreach($regiones as $key => $value){
					$regionesData[] = array(
						"id_usuario" => $id,
						"region" => $value
					);
				}
		
			}
			$this->usuariosModel->guardarregiones($id, $regionesData);

			$multaDelegacionesData = [];
			if($multa_delegaciones !== null){
				foreach($multa_delegaciones as $key => $value){
					$multaDelegacionesData[] = array(
						"id_usuario" => $id,
						"id_region" => $value
					);
				}
			}
			$this->usuariosModel->guardarregionesmultas($id, $multaDelegacionesData);

			// Unidades de investigación de Multas — filter against the just-saved regiones
			// so any submitted unidad whose region was deselected is dropped on save.
				$uidsData = array_map(fn($uid) => ['id_usuario' => $id, 'id_unidad_investigacion' => (int)$uid, 'tipo_adscripcion' => $assignment_type], $valid_uids);
			$this->usuariosModel->guardar_unidades_investigacion($id, $uidsData);

			$dashboardRegionesData = [];
			if($dashboard_regiones !== null){
				foreach($dashboard_regiones as $value){
					$dashboardRegionesData[] = array(
						"id_usuario" => $id,
						"id_region" => $value
					);
				}
			}
			$this->usuariosModel->guardar_regiones_dashboard($id, $dashboardRegionesData);

			$oficinasData = [];
			
			if($oficinas !== null){
				foreach($oficinas as $key => $value){
					$oficinasData[] = array(
						"id_usuario" => $id,
						"id_oficina" => $value
					);
				}
			}
			$this->usuariosModel->guardaroficinas($id, $oficinasData);

			$this->usuariosModel->guardar_permisos_especiales($id, $permisos_especiales_raw);

			if ($this->db->transStatus() === false) {
				$this->db->transRollback();
				return $this->response->setBody(
					'error|No se pudo guardar la configuración del usuario.'
				);
			}

			$this->db->transCommit();
			$this->_refreshSessionIfSelf($id, $passwordChanged);

			if ($passwordChanged) {
				$this->_revokeSsoSessions($id);
			}

			if($firma != ""){
				$folder = getcwd().'/uploads_general/firmas/'.$id;
				if (!file_exists($folder)) {
					mkdir($folder, 0777, true);
				}
				$base64 = str_replace('data:image/png;base64,', '', $firma);
				$base64 = str_replace(' ', '+', $base64);
				$base64 = base64_decode($base64);
				$file = $folder.'/Firma.png';
				file_put_contents($file, $base64);
			}

			return $this->response->setBody('ok');
		}
	}
	function eliminar($id){
		if (!$this->_hasModule('usuarios')) { 
            return $this->response
                        ->setStatusCode(403)
                        ->setBody('No autorizado');
        }
		$this->_revokeSsoSessions($id);
		$this->usuariosModel->eliminar($id);
		return redirect()->to(module_url('usuarios'));
	}

	function mostrarpassword(){
		if (!$this->_hasModule('usuarios')) { 
            return $this->response
                        ->setStatusCode(403)
                        ->setJSON(['success' => false]);
        }
		$key = env('app.encryption_passwords_key');
		$keySent = $this->request->getPost('passwordDesencriptacion');
		if($keySent != null && $keySent == $key){
			return $this->response->setJSON(array("success" => true, "password" => $this->decrypt_password($this->request->getPost('password'), $key)));
		}else{
			return $this->response->setJSON(array("success" => false));
		}
	}

	function registrarcorreo(){
		return $this->render('usuariosregistrarcorreo_view');
	}

	/**
	 * Wrapper SSO de CI4. Config\Services::ssoauth() devuelve una instancia
	 * compartida por petición, la misma que usa SsoAuthFilter.
	 */
	private function _ssoauth(): Ssoauth {
		return service('ssoauth');
	}

	/**
	 * Revoca todas las sesiones SSO del usuario. Si el SSO falla se registra
	 * el error y la operación sobre el usuario continúa.
	 */
	private function _revokeSsoSessions($id) {
		try {
			$this->_ssoauth()->revokeAllForUser((int) $id);
		} catch (Throwable $e) {
			log_message('error', 'No se pudieron revocar las sesiones SSO del usuario ' . (int) $id . ': ' . $e->getMessage());
		}
	}

	/**
	 * SSO es la fuente de verdad de la identidad: si el admin se editó a sí
	 * mismo, rehidratamos la sesión nativa desde el bridge (módulos, permisos,
	 * nombre, correo, changedpassword) en lugar de parchearla con el modelo.
	 *
	 * Si cambió su propia contraseña no se refresca nada: el llamador revoca
	 * sus sesiones SSO y deberá volver a iniciar sesión.
	 */
	private function _refreshSessionIfSelf($id, $passwordChanged = false) {
		if ((int) $id !== (int) session()->get('userid')) {
			return;
		}

		if ($passwordChanged) {
			return;
		}

		$identity = $this->_ssoauth()->identity();

		if ($identity === null) {
			// La cookie SSO ya no es válida: limpiar solo la identidad.
			session()->remove([
				'is_logued_in',
				'userid',
				'is_empleado',
				'modules',
				'username',
				'rfc',
				'name',
				'changedpassword',
				'special_permissions',
			]);
			return;
		}

		session()->set([
			'is_logued_in'        => true,
			'userid'              => $identity['user_id'],
			'is_empleado'         => $identity['is_empleado'],
			'modules'             => $identity['modules'],
			'username'            => $identity['email'],
			'rfc'                 => $identity['rfc'],
			'name'                => $identity['name'],
			'changedpassword'     => $identity['changedpassword'] ?? '1',
			'special_permissions' => $identity['special_permissions'],
		]);
	}

	private function _usesScopedMultasModules($is_empleado, array $modulos) {
		return !$is_empleado && !empty(array_intersect($modulos, MultasAccessScope::scoped_modules()));
	}

	private function _normalizeModuleHierarchy(array $modulos) {
		$children = ['multas', 'multas_dashboard', 'detenciones', 'expedientes', 'denuncias', 'denuncias_medidas', 'bienes_asegurados', 'cateos', 'audiencias', 'exportacion_datos', 'regiones', 'detenidos_corporaciones', 'unidades_investigacion', 'multa_delitos', 'multa_motivos_salida', 'ofendidos', 'actuaciones_ministeriales'];
		if (array_intersect($modulos, $children)) $modulos[] = 'mpdigital';
		return array_values(array_unique($modulos));
	}

	private function _normalizeMultasAssignment($is_empleado, array $modulos, $profile, array $regiones, array $unidades, $usuario_id) {
		if (!$this->_usesScopedMultasModules($is_empleado, $modulos)) return [null, [], [], null];
		if (!MultasAccessScope::is_valid_profile($profile)) return ['Seleccione un mini-perfil de Multas válido.', [], [], null];
		if ($profile === MultasAccessScope::FISCAL_GENERAL_DELEGADO) {
			$regiones = array_values(array_filter(array_unique(array_map('intval', $regiones))));
			if (empty($regiones)) return ['Asigne al menos una región para el Delegado.', [], [], null];
			return [null, $regiones, [], null];
		}

		$unidades = array_values(array_filter(array_unique(array_map('intval', $unidades))));
		$is_multiple_unidades = in_array($profile, [MultasAccessScope::FISCAL_ESPECIALIZADO_COORDINADOR, MultasAccessScope::DIRECTOR], true);
		if (($is_multiple_unidades && empty($unidades)) || (!$is_multiple_unidades && count($unidades) !== 1)) {
			return [$is_multiple_unidades ? 'Asigne al menos una unidad de investigación.' : 'Asigne exactamente una unidad de investigación.', [], [], null];
		}
		// ponytail: `denuncias_unidades_investigacion` vive en fiscalia_mpdigital.
		// $this->db en este controller apunta a fiscalia (auth); calificamos
		// explícitamente para que MySQL resuelva al schema correcto.
		$placeholders = implode(',', array_fill(0, count($unidades), '?'));
		$unidades_activas = $this->db->query(
			'SELECT id,id_region FROM fiscalia_mpdigital.denuncias_unidades_investigacion WHERE id IN (' . $placeholders . ') AND activo=1 FOR UPDATE',
			$unidades
		)->getResultArray();
		if (count($unidades_activas) !== count($unidades)) return ['Una de las unidades de investigación seleccionadas no está activa.', [], [], null];
		$regiones = array_values(array_unique(array_map('intval', array_column($unidades_activas, 'id_region'))));
		$type = $profile === MultasAccessScope::FISCAL_ESPECIALIZADO_COORDINADOR ? 'coordinador'
			: ($profile === MultasAccessScope::DIRECTOR ? 'director' : 'ministerio_publico');
		if ($type === 'coordinador') {
			foreach ($unidades as $unidad_id) {
				$ocupado = $this->usuariosModel->coordinador_unidad_ocupada($unidad_id, (int)$usuario_id);
				if ($ocupado) return ['La unidad seleccionada ya tiene coordinador activo: ' . $ocupado['name'] . '.', [], [], null];
			}
		}
		return [null, $regiones, $unidades, $type];
	}

	private function _sanitizeSpecialPermissions(array $permissions, $profile, array $modules) {
		$allowed = ['marcar_pago_manual', 'mostrar_total_multas', 'marcar_multas_pagadas',
			MultasAccessScope::PERMISSION_SEND_EXPEDIENTES, MultasAccessScope::PERMISSION_AUDIT_EXPEDIENTES,
			MultasAccessScope::PERMISSION_VIEW_ORGANIGRAMA];
		$permissions = array_values(array_intersect(array_unique($permissions), $allowed));
		if ($profile !== MultasAccessScope::FISCAL_ESPECIALIZADO_COORDINADOR) {
			$permissions = array_values(array_diff($permissions, [MultasAccessScope::PERMISSION_SEND_EXPEDIENTES]));
		}
		if ($profile !== MultasAccessScope::FISCAL_ESPECIALIZADO_COORDINADOR && !in_array('usuarios', $modules, true)) {
			$permissions = array_values(array_diff($permissions, [MultasAccessScope::PERMISSION_AUDIT_EXPEDIENTES]));
		}
		if ($profile === MultasAccessScope::DIRECTOR) {
			$permissions = array_values(array_diff($permissions, [MultasAccessScope::PERMISSION_AUDIT_EXPEDIENTES]));
		}
		if (!in_array('mpdigital', $modules, true)) {
			$permissions = array_values(array_diff($permissions, [MultasAccessScope::PERMISSION_VIEW_ORGANIGRAMA]));
		}
		return $permissions;
	}

	private function _hasModule($module) {
		return (int)session()->get('userid') > 0
			&& in_array($module, (array)session()->get('modules'), true);
	}
}
