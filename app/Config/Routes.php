<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->group('areas', static function ($routes) {
    $routes->get('/', 'Areas::index');
    $routes->get('loaddata', 'Areas::loaddata');
    $routes->post('guardar', 'Areas::guardar');
    $routes->post('editar', 'Areas::editar');
    $routes->post('eliminar', 'Areas::eliminar');
});

$routes->group('login', static function ($routes) {
    $routes->get('/', 'Login::index');
    $routes->post('new_user', 'Login::new_user');
    $routes->get('logout', 'Login::logout');
});

$routes->get('noaccess', 'Noaccess::index');
