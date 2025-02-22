<?php

// Register Custom Post Type
function registrar_post_type_eventos() {

    $labels = array(
        'name'                  => _x( 'Eventos', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Evento', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Eventos', 'text_domain' ),
        'name_admin_bar'        => __( 'Evento', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( 'All Items', 'text_domain' ),
        'add_new_item'          => __( 'Add New Item', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Item', 'text_domain' ),
        'edit_item'             => __( 'Edit Item', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Evento', 'text_domain' ),
        'description'           => __( 'Post Type Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail' ),
        'taxonomies'            => array( 'evento_produtora', 'evento_cidade' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 10,
		'menu_icon'			    => 'dashicons-calendar',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'eventos', $args );

}
add_action( 'init', 'registrar_post_type_eventos', 0 );

// Adicionar taxonomias hierárquicas para o post type "eventos"
function criar_taxonomias_eventos() {
    // Taxonomia de Produtoras
    $labels_eventos_produtora = array(
        'name'              => _x('Produtoras', 'taxonomy general name', 'textdomain'),
        'singular_name'     => _x('Produtora', 'taxonomy singular name', 'textdomain'),
        'search_items'      => __('Buscar Produtoras', 'textdomain'),
        'all_items'         => __('Todos os Produtoras', 'textdomain'),
        'parent_item'       => __('Produtora Pai', 'textdomain'),
        'parent_item_colon' => __('Produtora Pai:', 'textdomain'),
        'edit_item'         => __('Editar Produtora', 'textdomain'),
        'update_item'       => __('Atualizar Produtora', 'textdomain'),
        'add_new_item'      => __('Adicionar Nova Produtora', 'textdomain'),
        'new_item_name'     => __('Novo Nome de Produtora', 'textdomain'),
        'menu_name'         => __('Produtoras', 'textdomain'),
    );

    $args_eventos_produtora = array(
        'hierarchical'      => true, // Definir como hierárquico
        'labels'            => $labels_eventos_produtora,
        'show_ui'          => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'produtora'),
    );

    register_taxonomy('evento_produtora', array('eventos'), $args_eventos_produtora);

    // Taxonomia de Cidades
    $labels_eventos_cidades = array(
        'name'              => _x('Cidades', 'taxonomy general name', 'textdomain'),
        'singular_name'     => _x('Cidade', 'taxonomy singular name', 'textdomain'),
        'search_items'      => __('Buscar Cidades', 'textdomain'),
        'all_items'         => __('Todas as Cidades', 'textdomain'),
        'parent_item'       => __('Cidade Pai', 'textdomain'),
        'parent_item_colon' => __('Cidade Pai:', 'textdomain'),
        'edit_item'         => __('Editar Cidade', 'textdomain'),
        'update_item'       => __('Atualizar Cidade', 'textdomain'),
        'add_new_item'      => __('Adicionar Nova Cidade', 'textdomain'),
        'new_item_name'     => __('Novo Nome de Cidade', 'textdomain'),
        'menu_name'         => __('Cidades', 'textdomain'),
    );

    $args_eventos_cidades = array(
        'hierarchical'      => true, 
        'labels'            => $labels_eventos_cidades,
        'show_ui'          => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'cidade'),
    );

    register_taxonomy('evento_cidade', array('eventos'), $args_eventos_cidades);
}

// Hook para registrar as taxonomias
add_action('init', 'criar_taxonomias_eventos');
