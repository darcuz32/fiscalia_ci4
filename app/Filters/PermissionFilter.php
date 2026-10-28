<?php
namespace App\Filters;

use App\Libraries\Permission;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if($request->isAJAX()){
            return null;
        }

        $userid = session()->get('userid');

         //Perfil: si no hay sesión, redirigir a login
        if ($userid == false) {
            return redirect()->to(base_url('login'));
        }

        $uri      = $request->getUri();
        $segment  = strtolower((string) $uri->getSegment(1));
        $segment2 = $uri->getTotalSegments() > 1 ? (string) $uri->getSegment(2) : '';

        // TODO: Permiso temporal hardcodeado para Informe Ejecutivo.
        // Permite que cualquier usuario autenticado acceda a la ruta raíz y a
        // /InformeEjecutivo sin validar el permiso asignado en la base de datos.
        // Eliminar este bloque completo cuando se configure el permiso real.
        if ($segment === '' || $segment === 'informeejecutivo') {
            return null;
        }

        $permissionService = new Permission();
        $permissions = $permissionService->get_user_permissions($userid);

        if (empty($permissions)) {
            return redirect()->to(base_url('noaccess'));
        }

        if(session()->get('changedpassword')=='0' && $segment != 'changepassword'){
            return redirect()->to(module_url('changepassword'));
        }

        if(session()->get('username')=='' && $segment2 != 'registrarcorreo'){
            return redirect()->to(module_url('usuarios/registrarcorreo'));
        }

        //Prevenir url sin controlador
        if ($segment === '') {
            if (substr($permissions[0], -1) == '*') {
                return redirect()->to(base_url(service('routes')->getDefaultController()));
            }

            return redirect()->to(module_url($permissions[0]));
        }

        //Url a buscar en los permisos
        $url = $segment;

        //Si quisieramos validar el acceso a cada función de los controladores
        if ($segment2 != '') {
            $url .= '/' . $segment2;
        }

        $is_registrarcorreo = ($segment === 'usuarios' && $segment2 === 'registrarcorreo');
        // Always allow micuenta/firma (self-service signature page)
        $is_micuenta_firma = ($segment === 'micuenta' && $segment2 === 'firma');
        $permitted = in_array($url, $permissions);
        if (!$permitted) {
            foreach ($permissions as $perm) {
                if (substr($perm, -2) === '/*') {
                    $prefix = substr($perm, 0, -2);
                    if ($url === $prefix || strpos($url, $prefix.'/') === 0) {
                        $permitted = true;
                        break;
                    }
                }
            }
        }
        if (!$is_registrarcorreo && !$is_micuenta_firma && !$permitted){
            return redirect()->to(base_url('noaccess'));
        }

        return null;
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // No se requiere acción posterior.
    }
}
