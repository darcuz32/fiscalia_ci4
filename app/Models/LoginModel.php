<?php

namespace App\Models;

use Config\Database;

class LoginModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function login_user(string $email)
    {
        return $this->db->table('users')
            ->select('users.Id, users.name, users.email, users.idprofile, users.rfc, users.password, COALESCE(profiles.profile, "") as profile')
            ->join('profiles', 'profiles.Id = users.idprofile', 'left')
            ->where('users.active', 1)
            ->groupStart()
            ->where('users.email', $email)
            ->orWhere('users.rfc', $email)
            ->groupEnd()
            ->get()
            ->getRow();          
    }

    public function obtener_modulos($id_usuario){
        return $this->db->table('user_modules um')
            ->select('m.name, m.redirect_url')
            ->join('modules m', 'm.Id = um.id_module')
            ->where('um.id_usuario', $id_usuario)
            ->orderBy('m.sort_order')
            ->get()
            ->getResult();
    }

    public function obtener_permisos_especiales($id_usuario){

        return array_column($this->db->table('user_special_permissions')
            ->select('permission')
            ->where('id_usuario', $id_usuario)
            ->get()
            ->getResultArray(), 'permission');
    }
}