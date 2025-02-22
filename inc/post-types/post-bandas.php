<?php

// Register Custom Post Type
function registrar_post_type_bandas() {

    $labels = array(
        'name'                  => _x( 'Bandas', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Banda', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Bandas', 'text_domain' ),
        'name_admin_bar'        => __( 'Banda', 'text_domain' ),
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
        'label'                 => __( 'Banda', 'text_domain' ),
        'description'           => __( 'Post Type Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor' ),
        'taxonomies'            => array( 'genero', 'pais' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
		'menu_icon'			    => 'dashicons-format-audio',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'bandas', $args );

}
add_action( 'init', 'registrar_post_type_bandas', 0 );

// Adicionar taxonomias hierárquicas para o post type "bandas"
function criar_taxonomias_bandas() {
    // Taxonomia de Gêneros
    $labels_generos = array(
        'name'              => _x('Gêneros', 'taxonomy general name', 'textdomain'),
        'singular_name'     => _x('Gênero', 'taxonomy singular name', 'textdomain'),
        'search_items'      => __('Buscar Gêneros', 'textdomain'),
        'all_items'         => __('Todos os Gêneros', 'textdomain'),
        'parent_item'       => __('Gênero Pai', 'textdomain'),
        'parent_item_colon' => __('Gênero Pai:', 'textdomain'),
        'edit_item'         => __('Editar Gênero', 'textdomain'),
        'update_item'       => __('Atualizar Gênero', 'textdomain'),
        'add_new_item'      => __('Adicionar Novo Gênero', 'textdomain'),
        'new_item_name'     => __('Novo Nome de Gênero', 'textdomain'),
        'menu_name'         => __('Gêneros', 'textdomain'),
    );

    $args_generos = array(
        'hierarchical'      => true, // Definir como hierárquico
        'labels'            => $labels_generos,
        'show_ui'          => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'genero'),
    );

    register_taxonomy('genero', array('bandas'), $args_generos);

    // Taxonomia de Países
    $labels_paises = array(
        'name'              => _x('Países', 'taxonomy general name', 'textdomain'),
        'singular_name'     => _x('País', 'taxonomy singular name', 'textdomain'),
        'search_items'      => __('Buscar Países', 'textdomain'),
        'all_items'         => __('Todos os Países', 'textdomain'),
        'parent_item'       => __('País Pai', 'textdomain'),
        'parent_item_colon' => __('País Pai:', 'textdomain'),
        'edit_item'         => __('Editar País', 'textdomain'),
        'update_item'       => __('Atualizar País', 'textdomain'),
        'add_new_item'      => __('Adicionar Novo País', 'textdomain'),
        'new_item_name'     => __('Novo Nome de País', 'textdomain'),
        'menu_name'         => __('Países', 'textdomain'),
    );

    $args_paises = array(
        'hierarchical'      => true, 
        'labels'            => $labels_paises,
        'show_ui'          => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'pais'),
    );

    register_taxonomy('pais', array('bandas'), $args_paises);
	
	// Taxonomia de Cidades
    $labels_cidades = array(
        'name'              => _x('Cidades', 'taxonomy general name', 'textdomain'),
        'singular_name'     => _x('Cidade', 'taxonomy singular name', 'textdomain'),
        'search_items'      => __('Buscar Cidades', 'textdomain'),
        'all_items'         => __('Todas as Cidades', 'textdomain'),
        'parent_item'       => __('Cidade Pai', 'textdomain'),
        'parent_item_colon' => __('Cidade Pai:', 'textdomain'),
        'edit_item'         => __('Editar Cidade', 'textdomain'),
        'update_item'       => __('Atualizar Cidade', 'textdomain'),
        'add_new_item'      => __('Adicionar Nova Cidade', 'textdomain'),
        'new_item_name'     => __('Novo Nome da cidade', 'textdomain'),
        'menu_name'         => __('Cidades', 'textdomain'),
    );

    $args_cidades = array(
        'hierarchical'      => true, 
        'labels'            => $labels_cidades,
        'show_ui'          => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'cidades'),
    );

    register_taxonomy('cidades', array('bandas'), $args_cidades);
}

// Hook para registrar as taxonomias
add_action('init', 'criar_taxonomias_bandas');
