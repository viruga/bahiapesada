<?php

function myprefix_forcar_busca_desacentuada($search, $query) {
    global $wpdb;

    if ( ! $query->is_search() || ! $query->is_main_query() || empty( $query->get('s') ) ) {
        return $search;
    }

    $termo = $query->get('s');
    // 1. Desacentua o termo DE BUSCA (PHP)
    $termo_desacentuado = remove_accents( $termo ); 
    
    // Se não houver acentos no termo, não precisamos injetar SQL complexo.
    if ( $termo === $termo_desacentuado ) {
        return $search;
    }

    $search_like = '%' . $wpdb->esc_like( $termo_desacentuado ) . '%';

    // 2. Desacentua o post_title e post_content (SQL) antes de comparar.
    // Esta parte é complexa, mas é obrigatória para desacentuar no MySQL.
    $sql_replaces = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(%s), 'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u'),'â','a'),'ê','e'),'ô','o'),'ö','o'),'ã','a'),'ç','c')";
    
    $sql_title_check = sprintf($sql_replaces, "{$wpdb->posts}.post_title");
    $sql_content_check = sprintf($sql_replaces, "{$wpdb->posts}.post_content");
    
    // Substitui a busca padrão por uma nova, desacentuada, no título e conteúdo.
    $new_search = $wpdb->prepare(" AND ( {$sql_title_check} LIKE %s OR {$sql_content_check} LIKE %s ) ", $search_like, $search_like);

    // Retorna a nova cláusula.
    return $new_search;
}
// Usar prioridade bem alta (999) é crucial quando a busca não funciona.
add_filter('posts_search', 'myprefix_forcar_busca_desacentuada', 999, 2);