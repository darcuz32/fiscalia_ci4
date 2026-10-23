<?php

namespace App\Controllers;

use App\Models\LoginModel;

class Login extends BaseController
{

    private $moduleRedirects = [
        'buss'                    => 'buss',
        'registro'                => 'registro',
        'registro_mp'             => 'registro/historial',
        'constancias'             => 'constanciahechosrevision',
        'constancias_titulares'   => 'constancias_titulares',
        'citas'                   => 'administracionCitas',
        'gestiones'               => 'gestiones',
        'gestiones_administracion'=> 'gestiones/administracion',
        'comisiones'              => 'comisiones',
        'comisiones_reporte'      => 'comisiones/reporte',
        'comisiones_solicitudes'  => 'comisiones/solicitudes',
        'mpdigital'               => 'mpdigital/inicio',
        'multas'                  => 'multas',
        'denuncias'               => 'denuncias',
        'bienes_asegurados'       => 'bienesasegurados',
        'mandamientos'            => 'mandamientosjudiciales',
        'instituto'               => 'instituto',
        'instituto_personal'      => 'instituto',
        'instituto_seguimiento'   => 'instituto',
        'estadisticas'            => 'estadisticas/gestiones',
        'estadisticas_gestiones'  => 'estadisticas/gestiones',
        'estadisticas_constancias'=> 'estadisticas/constancias',
        'estadisticas_registro'   => 'estadisticas/registro',
        'colaboracionforanea'     => 'colaboracionforanea',
        'motivos_visita'          => 'motivos_visita',
        'areas'                   => 'areas',
        'usuarios'                => 'usuarios',
    ];

    private LoginModel $loginModel;

    public function __construct()
    {
        $this->loginModel = new LoginModel();
    }

    private function redirectByProfile($view = 'login_view') {
        $userid = session()->get('userid');
        
        if (empty($userid)) {
            $data['token'] = $this->token();
            $data['titulo'] = 'Login';
            return view($view, $data);
        } 
        
        if(session()->get('is_empleado')){
            return redirect()->to(base_url('comisiones'));
        }

        $modules = session()->get('modules') ?: [];

        if (in_array('mpdigital', $modules)) {
            return redirect()->to(base_url('mpdigital/inicio'));
        }

        $first = !empty($modules) ? $modules[0] : null;
        $redirect = ($first && isset($this->moduleRedirects[$first]))
            ? $this->moduleRedirects[$first]
            : 'usuarios';
        
        return redirect()->to(base_url($redirect));
        
    }

    public function index() {
        return $this->redirectByProfile();
    }

    public function new_user($view = ''){
        $postedToken = (string) $this->request->getPost('token');
        $sessionToken = (string) session()->get('token');

        $rules = [
            'username' => [
                'rules' => 'required|trim|min_length[2]|max_length[150]',
            ],
            'password' => [
                'rules' => 'required|trim|min_length[2]|max_length[150]',
            ],
        ];

        if($postedToken && $postedToken == $sessionToken){

            //lanzamos mensajes de error si es que los hay
            if($this->validate($rules) == FALSE){
                return $this->index();
            }else{
                $username = $this->request->getPost('username');
                $password = $this->request->getPost('password');
                $check_user = $this->loginModel->login_user($username);
                
                if(isset($check_user->Id) && (password_verify(hash('sha512', $password), $check_user->password))){

                    $special_permissions = $this->loginModel->obtener_permisos_especiales($check_user->Id);
                    $modules_raw  = $this->loginModel->obtener_modulos($check_user->Id);
                    $module_names = array_column((array)$modules_raw, 'name');
                    $is_empleado  = ((int)$check_user->idprofile === 3);

                    $data = array(
                            'is_logued_in'       => TRUE,
                            'userid'             => $check_user->Id,
                            'is_empleado'        => $is_empleado,
                            'modules'            => $module_names,
                            'username'           => $check_user->email,
                            'rfc'                => $check_user->rfc,
                            'name'               => $check_user->name,
                            'changedpassword'    => 1,
                            'special_permissions'=> $special_permissions
                            );
                    session()->set($data);
                    return $this->index();
                }else{
                    session()->setFlashdata('error', 'Usuario y/o contraseña incorrectos');

                    return redirect()->to(
                        base_url('login' . ($view !== '' ? '/' . $view : ''))
                    );
                }
            }
        }else{
            return redirect()->to(
                base_url('login' . ($view !== '' ? '/' . $view : ''))
            );
        }
    }

    public function token()
    {
        $token = md5(uniqid(rand(),true));
        session()->set('token',$token);
        return $token;
    }
    
    public function logout(){
        session()->destroy();
        $data = array(
                'is_logued_in'  =>         FALSE,
                'userid'        =>         '',
                'idarea'        =>         '',
                'areaname'      =>         '',
                'username'      =>         '',
                'name'          =>         ''
                );
        session()->set($data);

        return redirect()->to(
                        base_url('login')
                    );
    }
}
