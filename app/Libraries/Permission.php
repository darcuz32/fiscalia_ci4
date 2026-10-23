<?php
namespace App\Libraries;

use Config\Database;

class Permission
{
    /**
     * Obtener las rutas permitidas para un perfil.
     *
     * Devuelve un array de rutas o false si no hay permisos.
     */
    public function get_user_permissions($userid)
    {

        if(session()->get('is_empleado')){
            return ['comisiones/*', 'empleados/*', 'usuarios/*'];
        }

        $db = Database::connect();

        $query = $db->table('user_modules um')
            ->select('p.route')
            ->distinct()
            ->join(
                'module_routes mr',
                'mr.id_module = um.id_module'
            )
            ->join(
                'permissions p',
                'p.Id = mr.id_permission'
            )
            ->where('um.id_usuario', $userid)
            ->get();

        $permissions = array_column($query->getResultArray(), 'route');

        return count($permissions) > 0 ? $permissions : false;
    }
}
