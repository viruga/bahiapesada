<?php

// Dependências
function scripts_front() {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');
	wp_enqueue_style('iconfont', get_template_directory_uri() . '/assets/css/iconfont.min.css');
    wp_enqueue_script( 'bootstrap-js','https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array( 'jquery' ),'',true );
    wp_enqueue_script('meu-script', get_template_directory_uri() . '/assets/js/script.js', array(), '', true);
    wp_enqueue_style( 'style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'scripts_front' );

// Post Types
get_template_part('inc/post-types/post-banners');
get_template_part('inc/post-types/post-bandas');
get_template_part('inc/post-types/post-albuns');
get_template_part('inc/post-types/post-eventos');

// Meta boxes
get_template_part('inc/metabox/meta-banda');
get_template_part('inc/metabox/meta-album');
get_template_part('inc/metabox/meta-info-banda');
get_template_part('inc/metabox/meta-youtube');
get_template_part('inc/metabox/meta-eventos');

// Funcionalidades
get_template_part('inc/func/func-token-spotify');
get_template_part('inc/func/func-admin-column-albuns');
get_template_part('inc/func/func-api-youtube');
get_template_part('inc/func/func-utilidades');