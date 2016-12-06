<?php

/** @var  \Herbert\Framework\Application $container */
/** @var  \Herbert\Framework\Http $http */
/** @var  \Herbert\Framework\Router $router */
/** @var  \Herbert\Framework\Enqueue $enqueue */
/** @var  \Herbert\Framework\Panel $panel */
/** @var  \Herbert\Framework\Shortcode $shortcode */
/** @var  \Herbert\Framework\Widget $widget */

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('categorias', function ($table) 
{
	$table->increments('id');
	
	$table->string('titulo');
	
	$table->string('slug');
	
	$table->timestamps();
});

Capsule::schema()->create('empresas', function($table)
{
	$table->increments('id');

	$table->string('nome');
		
	$table->string('localizacao');
	
	$table->string('imagem');
	
	$table->string('file');
	
	$table->string('slug');

	$table->timestamps();
});
	
Capsule::schema()->create('categoria_empresa', function ($table) 
{
	$table->increments('id');
	
	$table->integer('categoria_id')->unsigned()->index();
	
	$table->integer('empresa_id')->unsigned()->index();
	
	$table->timestamps();
	
	$table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');
	
	$table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
});