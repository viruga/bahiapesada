<?php

/**
 * Altera a cláusula de busca (WHERE) no WordPress para ignorar acentos.
 * Isso permite que a busca por "töhil" encontre "tohil", "tôhil", etc.
 * * @param string $search A cláusula WHERE SQL de busca.
 * @param WP_Query $query O objeto WP_Query atual.
 * @return string A nova cláusula WHERE SQL.
 */
function myprefix_ignorar_acentos_na_busca($search, $query) {
    global $wpdb;

    // 1. Condições de Execução
    // Só executa se for uma busca principal e a busca não estiver vazia.
    if ( ! $query->is_search() || ! $query->is_main_query() || empty( $query->get('s') ) ) {
        return $search;
    }

    // 2. Termo de Busca Normalizado (Desacentuado)
    // Usamos a função nativa do WP, que é a mais robusta.
    $termo = $query->get('s');
    $termo_desacentuado = remove_accents( $termo );

    // Se a desacentuação não mudou o termo (ex: buscou "bola"), não faz nada.
    if ( $termo === $termo_desacentuado ) {
        return $search;
    }

    // O termo de busca que vamos usar no LIKE.
    $search_like = '%' . $wpdb->esc_like( $termo_desacentuado ) . '%';

    // 3. Montagem da Cláusula SQL
    /* * Aqui é a chave: precisamos criar um novo REPLACE para o SQL 
     * que garanta a conversão dos acentos (incluindo o 'ö') para que o LIKE funcione.
     */
    
    // Lista robusta de substituições no SQL (incluindo o 'ö')
    $sql_replaces = "REPLACE(
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(LOWER({$wpdb->posts}.post_title), 
                                            'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u'),'â','a'),'ê','e'),'ô','o'),'ö','o'),'ã','a'),'ç','c')
    ";
    
    // Constrói a nova cláusula AND ( ... ) para substituir a padrão.
    // Usamos o prepare para garantir a segurança.
    $new_search = $wpdb->prepare(" AND ( {$sql_replaces} LIKE %s ) ", $search_like);

    // Você pode expandir isso para buscar no conteúdo também:
    // $sql_replaces_content = str_replace('post_title', 'post_content', $sql_replaces);
    // $new_search = $wpdb->prepare(" AND ( {$sql_replaces} LIKE %s OR {$sql_replaces_content} LIKE %s ) ", $search_like, $search_like);

    return $new_search;
}
add_filter('posts_search', 'myprefix_ignorar_acentos_na_busca', 500, 2);