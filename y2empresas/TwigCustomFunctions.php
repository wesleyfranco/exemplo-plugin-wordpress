<?php

$twig = herbert('Twig_Environment');

$funcoes = [
			'get_header',
			'get_sidebar',
			'get_footer',
			'get_search_form',
			'get_template_part',
			'the_ID',
			'post_class',
			'sanitize_title',
            ];
// Registro as novas funcoes para o twig
foreach ((array) $funcoes as $function)
{
   $twig->addFunction(new \Twig_SimpleFunction($function, $function));
}