<?php
// Função para obter o token de acesso do Spotify
function toma_get_spotify_access_token() {
    $client_id = 'eafee449d3924d11938173425e338425'; 
    $client_secret = 'f10d0aea56c2487cb88a8756e6c2da8b'; 

    $response = wp_remote_post('https://accounts.spotify.com/api/token', array(
        'body' => array(
            'grant_type' => 'client_credentials'
        ),
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret)
        )
    ));

    if (is_wp_error($response)) {
        return null;
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    return $data['access_token'];
}
