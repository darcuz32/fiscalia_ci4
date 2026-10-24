<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SsoAuthFilter implements FilterInterface
{
    private const IDENTITY_KEYS = [
        'is_logued_in',
        'userid',
        'is_empleado',
        'modules',
        'username',
        'rfc',
        'name',
        'special_permissions',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $identity = service('ssoauth')->identity();

        if ($identity !== null) {
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
            return null;
        }

        // SSO is authoritative. Remove identity only.
        foreach (self::IDENTITY_KEYS as $key) {
            session()->remove($key);
        }

        return null;
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
    }
}