<?php

// Registrar o post type "álbuns"
function registrar_post_type_albuns() {
    $labels = array(
        'name'               => _x('Álbuns', 'post type general name', 'textdomain'),
        'singular_name'      => _x('Álbum', 'post type singular name', 'textdomain'),
        'menu_name'          => _x('Álbuns', 'admin menu', 'textdomain'),
        'name_admin_bar'     => _x('Álbum', 'add new on admin bar', 'textdomain'),
        'add_new'            => _x('Adicionar Nova', 'Álbum', 'textdomain'),
        'add_new_item'       => __('Adicionar Nova Álbum', 'textdomain'),
        'new_item'           => __('Nova Álbum', 'textdomain'),
        'edit_item'          => __('Editar Álbum', 'textdomain'),
        'view_item'          => __('Ver Álbum', 'textdomain'),
        'all_items'          => __('Todas as Álbuns', 'textdomain'),
        'search_items'       => __('Buscar Álbuns', 'textdomain'),
        'parent_item_colon'  => __('Álbum Pai:', 'textdomain'),
        'not_found'          => __('Nenhuma Álbum encontrada.', 'textdomain'),
        'not_found_in_trash' => __('Nenhuma Álbum encontrada na lixeira.', 'textdomain'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'albuns'),
        'capability_type'    => 'page',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
		'menu_icon'			 => 'dashicons-album',
        'supports'           => array('title', 'thumbnail'),
    );

    register_post_type('albuns', $args);
}

// Hook para registrar o post type
add_action('init', 'registrar_post_type_albuns');
