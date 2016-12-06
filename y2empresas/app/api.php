<?php namespace MyPlugin;

use MyPlugin\Controllers\EmpresaController;

/** @var \Herbert\Framework\API $api */

/**
 * Gives you access to the Helper class from Twig
 * {{ MyPlugin.helper('assetUrl', 'icon.png') }}
 */
$api->add('helper', function ()
{
    $args = func_get_args();
    $method = array_shift($args);

    return forward_static_call_array(__NAMESPACE__ . '\\Helper::' . $method, $args);
});

$api->add('show', function ($id) {
    $empresa = new EmpresaController();
    return $empresa->show($id);
});

$api->add('list', function () {
    $empresa = new EmpresaController();
    return $empresa->all();
});
