<?php

function save_youtube_channel_info($post_id) {
    // Verifica se o post é do tipo "banda"
    if (get_post_type($post_id) !== 'bandas') {
        return; // Se não for do tipo "banda", não faz nada
    }

    // Obtém o ID do canal do YouTube
    $youtube_channel_id = get_post_meta($post_id, 'youtube_channel_id', true);
    if (!$youtube_channel_id) {
        return; // Se não houver um ID do canal, não faz nada
    }

    // Sua chave de API do YouTube
    $api_key = 'AIzaSyB0t1HXu5x1Mr-fUhKO13scbNkn9N6_REM';

    // Faz a requisição para obter os detalhes do canal
    $youtube_api_url = 'https://www.googleapis.com/youtube/v3/channels?part=statistics&id=' . $youtube_channel_id . '&key=' . $api_key;
    $response = wp_remote_get($youtube_api_url);

    // Verifica se houve erro na requisição
    if (is_wp_error($response)) {
        return; // Se houver um erro, não faz nada
    }

    // Processa o corpo da resposta
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Salva as informações do canal
    if (isset($data['items'][0]['statistics'])) {
        $statistics = $data['items'][0]['statistics'];
        //update_post_meta($post_id, 'youtube_followers', intval($statistics['subscriberCount']));
        update_post_meta($post_id, 'youtube_views', intval($statistics['viewCount']));
    }
}
add_action('save_post', 'save_youtube_channel_info');