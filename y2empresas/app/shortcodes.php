<?php namespace MyPlugin;

/** @var \Herbert\Framework\Shortcode $shortcode */

/**
 * Allows usage of [showEmpresa empresa_id=2] as a shortcode
 */
$shortcode->add(
    'showEmpresa',
    'MyPlugin::show',
    [
        'empresa_id' => 'id'
    ]
);
/**
 * Allows usage of [listEmpresas] as a shortcode
 */
$shortcode->add(
    'listEmpresas',
    'MyPlugin::list'
);