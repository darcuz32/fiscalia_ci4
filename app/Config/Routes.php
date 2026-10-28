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

$routes->group('motivos_visita', static function ($routes) {
    $routes->get('/', 'MotivosVisita::index');
    $routes->get('loaddata', 'MotivosVisita::loaddata');
    $routes->post('guardar', 'MotivosVisita::guardar');
    $routes->post('editar', 'MotivosVisita::editar');
    $routes->post('eliminar', 'MotivosVisita::eliminar');
});

$routes->group('constancias_titulares', static function ($routes) {
    $routes->get('/', 'ConstanciasTitulares::index');
    $routes->get('loaddata', 'ConstanciasTitulares::loaddata');
    $routes->post('actualizar', 'ConstanciasTitulares::actualizar');
    $routes->post('guardar', 'ConstanciasTitulares::guardar');
});

$routes->group('usuarios', static function ($routes) {
    $routes->get('/', 'Usuarios::index');
    $routes->get('loaddata', 'Usuarios::loaddata');
    $routes->get('nuevo', 'Usuarios::nuevo');
    $routes->post('guardar', 'Usuarios::guardar');
    $routes->get('editar/(:num)', 'Usuarios::editar/$1');
    $routes->post('actualizar/(:num)', 'Usuarios::actualizar/$1');
    $routes->get('eliminar/(:num)', 'Usuarios::eliminar/$1');
    $routes->post('mostrarpassword', 'Usuarios::mostrarpassword');
    $routes->get('registrarcorreo', 'Usuarios::registrarcorreo');
});

$routes->group('UnidadesInvestigacion', static function ($routes) {
    $routes->get('get_activos_by_regiones', 'UnidadesInvestigacion::get_activos_by_regiones');
    $routes->get('getUnidadesInvestigacion', 'UnidadesInvestigacion::getUnidadesInvestigacion');
});

$routes->group('InformeEjecutivo', static function ($routes) {
    $routes->get('/', 'InformeEjecutivo::index');
    $routes->get('getDenunciasPorInstrumento', 'InformeEjecutivo::getDenunciasPorInstrumento');
    $routes->get('getDetenidosPorRangoEdad', 'InformeEjecutivo::getDetenidosPorRangoEdad');
    $routes->get('getDetenidosPorDelito', 'InformeEjecutivo::getDetenidosPorDelito');
});

$routes->group('estadisticas', static function ($routes) {
    $routes->get('getMunicipios', 'Estadisticas::getMunicipios');
    $routes->get('getOficinasGestiones', 'Estadisticas::getOficinasGestiones');
});

$routes->get('noaccess', 'Noaccess::index');
