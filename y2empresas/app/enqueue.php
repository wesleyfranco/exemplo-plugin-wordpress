<?php namespace MyPlugin;

/** @var \Herbert\Framework\Enqueue $enqueue */

$enqueue->front([
    'as'  => 'bootstrapCSS',
    'src' => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css',
]);

$enqueue->front([
    'as'     => 'funcoesFront',
    'src'    => Helper::assetUrl('/js/funcoes_front.js'),
]);

$enqueue->admin([
    'as'  => 'lightbox',
    'src' => Helper::assetUrl('/css/jquery.fancybox.css'),
]);

$enqueue->admin([
    'as'     => 'lightbox',
    'src'    => Helper::assetUrl('/js/jquery.fancybox.pack.js'),
]);

$enqueue->admin([
    'as'     => 'funcoesAdmin',
    'src'    => Helper::assetUrl('/js/funcoes_admin.js'),
]);