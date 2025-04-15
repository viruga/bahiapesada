<?php 

add_filter('manage_eventos_posts_columns', 'add_coluna_evento_dia');
function add_coluna_evento_dia($columns) {
    $columns['evento_dia'] = 'Data do Evento';
    return $columns;
}

add_action('manage_eventos_posts_custom_column', 'mostrar_coluna_evento_dia', 10, 2);
function mostrar_coluna_evento_dia($column, $post_id) {
    if ($column === 'evento_dia') {
        $data = get_post_meta($post_id, '_evento_dia', true);
        if ($data) {
            echo date('d/m/Y', strtotime($data));
        } else {
            echo '—';
        }
    }
}

add_filter('manage_edit-eventos_sortable_columns', 'coluna_evento_dia_ordenavel');
function coluna_evento_dia_ordenavel($columns) {
    $columns['evento_dia'] = 'evento_dia';
    return $columns;
}

add_action('pre_get_posts', 'ordenar_por_evento_dia');
function ordenar_por_evento_dia($query) {
    if (!is_admin() || !$query->is_main_query()) return;

    if ($query->get('orderby') === 'evento_dia') {
        $query->set('meta_key', '_evento_dia');
        $query->set('orderby', 'meta_value');
        $query->set('meta_type', 'DATE');
    }
}
