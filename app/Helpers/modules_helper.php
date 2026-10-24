<?php

if (! function_exists('module_owner')) {
    function module_owner(string $route): string
    {
        static $owners = null;

        if ($owners === null) {
            $file = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR
                  . 'fiscalia_bridge' . DIRECTORY_SEPARATOR . 'module_owners.php';

            $owners = is_file($file) ? require $file : [];
        }

        $segment = strtolower(explode('/', ltrim($route, '/'))[0] ?? '');

        return $owners[$segment] ?? 'ci3';
    }
}

if (! function_exists('module_url')) {
    function module_url(string $route = ''): string
    {
        $route = ltrim($route, '/');

        if ($route === '') {
            return base_url();
        }

        if (module_owner($route) === 'ci4') {
            // Owned by this app
            return base_url($route);
        }

        // Owned by CI3
        $legacy = rtrim((string) config('ModuleRouting')->ci3BaseURL, '/');
        return $legacy . '/' . $route;
    }
}

if (! function_exists('module_is_external')) {
    function module_is_external(string $route): bool
    {
        return module_owner($route) === 'ci3';
    }
}