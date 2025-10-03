<?php

add_filter('posts_search', 'remove_acentos_busca', 10, 2);

function remove_acentos_busca($search, $query) {
    global $wpdb;

    // Executa apenas em buscas
    if (!$query->is_search() || !$query->is_main_query()) {
        return $search;
    }

    // Termo buscado
    $termo = $query->get('s');

    // Remove acentos para fazer comparação
    $termo_sem_acentos = remove_acentos($termo);

    // Se os termos forem iguais, não precisa mudar nada
    if ($termo === $termo_sem_acentos) {
        return $search;
    }

    // Altera a cláusula de busca no SQL
    $search = " AND (";
    $search .= $wpdb->prepare("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER($wpdb->posts.post_title),
    'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u'),'â','a'),'ê','e'),'ô','o'),'ö','o'),'ã','a'),'ç','c') LIKE %s", '%' . $wpdb->esc_like($termo_sem_acentos) . '%');
    $search .= ")";

    return $search;
}

// Função básica para remover acentos
function remove_acentos($string) {
    $original = ['á','à','ã','â','ä','é','è','ê','ë','í','ì','î','ï','ó','ò','õ','ô','ö','ú','ù','û','ü','ç'];
    $substituta = ['a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c'];
    return str_ireplace($original, $substituta, $string);
}