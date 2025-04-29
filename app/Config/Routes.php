<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('api/register', 'AuthController::register');
//$routes->get('api/register', 'AuthController::register');


$routes->post('api/login', 'AuthController::login');
$routes->get('api/protected', 'AuthController::protected');
