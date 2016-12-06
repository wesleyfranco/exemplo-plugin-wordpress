<?php namespace MyPlugin;

/** @var \Herbert\Framework\Panel $panel */

$panel->add([
    'type'   => 'panel',
    'as'     => 'mainPanel',
    'title'  => 'Y2 Empresas',
    'slug'   => 'myplugin-index',
	'rename' => 'Empresas',
	'icon'  => 'dashicons-admin-page',
    'uses'   => __NAMESPACE__ . '\Controllers\AdminController@index',
	'post' => [
        'save' => __NAMESPACE__ .'\Controllers\AdminController@save',		
        ],
	'get' => [
		'new' => __NAMESPACE__ .'\Controllers\AdminController@novo',
		'edit' => __NAMESPACE__ .'\Controllers\AdminController@edit',
	]	
]);

$panel->add([
    'type'   => 'sub-panel',
    'parent' => 'mainPanel',
    'as'     => 'mainCategoria',
    'title'  => 'Categorias',
    'slug'   => 'myplugin-categorias',
    'uses'   => __NAMESPACE__ . '\Controllers\CategoriaController@index',
	'post' => [
        'save' => __NAMESPACE__ .'\Controllers\CategoriaController@save',
        ],
	'get' => [
		'new' => __NAMESPACE__ .'\Controllers\CategoriaController@novo',
		'edit' => __NAMESPACE__ .'\Controllers\CategoriaController@edit',
	]	
]);