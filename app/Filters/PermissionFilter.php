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

        $permissionService = new Permission();
        $permissions = $permissionService->get_user_permissions($userid);

        if (empty($permissions)) {
            return redirect()->to(base_url('noaccess'));
        }

        if(session()->get('changedpassword')=='0' && $request->getUri()->getSegment(1)!='changepassword'){
            return redirect()->to(base_url('changepassword'));
        }

        if(session()->get('username')=='' && $request->getUri()->getSegment(2)!='registrarcorreo'){
            return redirect()->to(base_url('usuarios/registrarcorreo'));
        }

        //Prevenir url sin controlador
        if ($request->getUri()->getSegment(1) == '') {
            if (substr($permissions[0], -1) == '*') {
                return redirect()->to(base_url(service('routes')->getDefaultController()));
            }

            return redirect()->to(base_url($permissions[0]));
        }

        //Url a buscar en los permisos
        $url = strtolower($request->getUri()->getSegment(1));

        //Si quisieramos validar el acceso a cada función de los controladores
        if ($request->getUri()->getSegment(2) != '') {
            $url .= '/' . $request->getUri()->getSegment(2);
        }

        $is_registrarcorreo = ($request->getUri()->getSegment(1) == 'usuarios' && $request->getUri()->getSegment(2) == 'registrarcorreo');
        // Always allow micuenta/firma (self-service signature page)
        $is_micuenta_firma = ($request->getUri()->getSegment(1) == 'micuenta' && $request->getUri()->getSegment(2) == 'firma');
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
