<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Sso extends BaseConfig
{
    public int $ttl = 43200;
    public string $cookieName = 'fiscalia_sso';
    public string $path = '/';
    public string $domain = '';
    public ?bool $secure = null;
    public string $sameSite = 'Lax';
}