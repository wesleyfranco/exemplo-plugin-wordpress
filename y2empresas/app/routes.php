<?php namespace MyPlugin;

use MyPlugin\Controllers\PostController;

/** @var \Herbert\Framework\Router $router */


$router->get([
    'as'   => 'empresaSingle',
    'uri'  => 'empresa/{id}/{slug}',
    'uses' => __NAMESPACE__ . '\Controllers\EmpresaController@show'
]);

$router->get([
    'as'   => 'empresasAll',
    'uri'  => 'empresas',
    'uses' => __NAMESPACE__ . '\Controllers\EmpresaController@all'
]);

$router->get([
    'as'   => 'empresasAllSearchEmpresa',
    'uri'  => 'empresas/pesquisa/{empresa}',
    'uses' => __NAMESPACE__ . '\Controllers\EmpresaController@search'
]);

$router->get([
    'as'   => 'empresasAllSearchCategory',
    'uri'  => 'empresas/pesquisa/categoria/{slug_categoria}',
    'uses' => __NAMESPACE__ . '\Controllers\EmpresaController@searchCategoria'
]);

$router->get([
    'as'   => 'empresasAllSearchEmpresaCategory',
    'uri'  => 'empresas/pesquisa/{empresa}/categoria/{slug_categoria}',
    'uses' => __NAMESPACE__ . '\Controllers\EmpresaController@search'
]);