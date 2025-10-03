<?php

// Dependências do Front
function scripts_front() {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');
	wp_enqueue_style('iconfont', get_template_directory_uri() . '/assets/css/iconfont.min.css');
    wp_enqueue_script( 'bootstrap-js','https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array( 'jquery' ),'',true );
    wp_enqueue_script('meu-script', get_template_directory_uri() . '/assets/js/script.js', array(), '', true);
    wp_enqueue_style( 'style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'scripts_front' );

// Dependências do Admin
function scripts_admin($hook) {
    global $post_type;
    if ($post_type === 'eventos') {
        wp_enqueue_style('select2css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        wp_enqueue_script('select2js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], null, true);

        // Nosso JS personalizado pra inicializar
        wp_add_inline_script('select2js', "
            jQuery(document).ready(function($) {
                $('#bandas-select').select2({
                    placeholder: 'Selecione as bandas',
                    width: '100%'
                });
            });
        ");
    }
}
add_action('admin_enqueue_scripts', 'scripts_admin');

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
get_template_part('inc/metabox/meta-relation-eventos-banda');

// Funcionalidades
get_template_part('inc/func/func-admin-column-albuns');
get_template_part('inc/func/func-admin-column-evento-dia');
get_template_part('inc/func/func-token-spotify');
get_template_part('inc/func/func-api-youtube');
get_template_part('inc/func/func-utilidades');
//get_template_part('inc/func/func-search');