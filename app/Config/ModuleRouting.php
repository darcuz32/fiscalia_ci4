<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class ModuleRouting extends BaseConfig
{
    /**
     * Base URL of the legacy CI3 app
     */
    public string $ci3BaseURL;

    public function __construct()
    {
        parent::__construct();

        $base = env('CI3_BASE_URL');
        if ($base === null || $base === false ) {
            $base = '';
        }

        $this->ci3BaseURL = rtrim($base, '/') . '/';
    }
}