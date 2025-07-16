<?php
/**
 * Title: Loop Single Evento
 * Slug: loop-single-evento
 * Description: Loop Single
 * Inserter: no
 */

//$dia = get_post_meta(get_the_ID(), '_evento_dia', true);
$local = get_post_meta(get_the_ID(), '_evento_local', true);

setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'Portuguese_Brazil');
$data_evento = strtotime(get_post_meta(get_the_ID(), '_evento_dia', true));
$dia = strftime('%d de %B de %Y', $data_evento);
$ingresso = get_post_meta(get_the_ID(), '_evento_ingresso', true);

echo '<p>Data do evento: ' . $dia;
echo ' • ' . esc_html(get_post_meta(get_the_ID(), '_evento_hora', true));
echo '</p>';
echo '<p class="mt-2">Local: ' . $local;
// Exibir termos da taxonomia 'evento_produtora'
$produtora_terms = get_the_terms(get_the_ID(), 'evento_produtora');
if ($produtora_terms && !is_wp_error($produtora_terms)) {
    foreach ($produtora_terms as $term) {
        $term_info = get_term($term->term_id, 'evento_produtora');
        echo esc_html($term_info->name) . ' - ';
        //echo esc_html($term_info->description) . ' - ';
    }
}
// Exibir termos da taxonomia 'eventos_cidade'
$cidade_terms = get_the_terms(get_the_ID(), 'evento_cidade');
if ($cidade_terms && !is_wp_error($cidade_terms)) {
    $cidade_names = wp_list_pluck($cidade_terms, 'name');
    echo implode(', ', $cidade_names);
} else {
    echo ''; // Exibe mensagem se não houver termos
}
echo '</p>';

// Exibir bandas
$bandas_ids = get_post_meta(get_the_ID(), '_bandas_relacionadas', true);
if (!empty($bandas_ids)) {
    echo '<p>Com as bandas: ';
    $links = [];
    foreach ($bandas_ids as $banda_id) {
        $links[] = '<a class="d-inline-block" href="' . get_permalink($banda_id) . '">' . get_the_title($banda_id) . '</a>';
    }
    echo implode(', ', $links);
    echo '</p>';
} 

// Mostra ingressos
if (!empty($ingresso)) {
    echo '<p><a href="' . esc_html($ingresso) . '" target="blank" class="wp-element-button py-1 mt-2 rounded d-inline-block has-background has-black-color has-vivid-green-cyan-background-color">Comprar ingresso</a></p>';
}
?>
