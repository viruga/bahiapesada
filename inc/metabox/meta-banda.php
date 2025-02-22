<?php

// Função para adicionar a metabox de URL do Spotify
function toma_add_spotify_url_meta_box() {
    add_meta_box(
        'spotify_url_meta_box',
        'URL do Spotify',
        'toma_render_spotify_url_meta_box',
        'bandas',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'toma_add_spotify_url_meta_box');

// Função para renderizar a metabox de URL do Spotify
function toma_render_spotify_url_meta_box($post) {
    $spotify_url = get_post_meta($post->ID, '_spotify_url', true);
    ?>
    <label for="spotify_url">Insira a URL da banda no Spotify:</label>
    <input type="url" id="spotify_url" name="spotify_url" value="<?php echo esc_attr($spotify_url); ?>" style="width: 100%;" />
    <?php
}

// Função para salvar a URL do Spotify
function toma_save_spotify_url_meta_box($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Verifica se a URL do Spotify está presente
    if (!isset($_POST['spotify_url'])) {
        return;
    }

    $spotify_url = sanitize_text_field($_POST['spotify_url']);
    update_post_meta($post_id, '_spotify_url', $spotify_url);

    // Somente buscar dados se a postagem já estiver publicada
    if (get_post_status($post_id) === 'publish') {
        toma_fetch_spotify_artist_data($spotify_url, $post_id);
    }
}
add_action('save_post', 'toma_save_spotify_url_meta_box');

// Função para buscar os dados do artista no Spotify
function toma_fetch_spotify_artist_data($spotify_url, $post_id) {
    preg_match('/artist\/([a-zA-Z0-9]+)/', $spotify_url, $matches);
    if (!isset($matches[1])) {
        return;
    }
    $artist_id = $matches[1];

    $url = "https://api.spotify.com/v1/artists/" . $artist_id;
    $access_token = toma_get_spotify_access_token();

    // Verifica se o token de acesso foi obtido corretamente
    if (!$access_token) {
        return;
    }

    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $access_token
        )
    ));

    if (is_wp_error($response)) {
        return;
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    
    // Verifica se os dados foram retornados corretamente
    if (!empty($data['name'])) {
        update_post_meta($post_id, '_spotify_artist_id', $artist_id);
        update_post_meta($post_id, '_spotify_artist_name', sanitize_text_field($data['name']));
    }
}

// Função para adicionar a metabox de dados do artista
function toma_add_artist_data_meta_box() {
    add_meta_box(
        'artist_data_meta_box',
        'Dados do Artista',
        'toma_render_artist_data_meta_box',
        'bandas',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'toma_add_artist_data_meta_box');

// Função para renderizar a metabox de dados do artista
function toma_render_artist_data_meta_box($post) {
    $artist_id = get_post_meta($post->ID, '_spotify_artist_id', true);
    $artist_name = get_post_meta($post->ID, '_spotify_artist_name', true);
    ?>
    <label>ID da Banda:</label>
    <input type="text" value="<?php echo esc_attr($artist_id); ?>" readonly style="width: 100%;" />
    <br>
    <label>Nome da Banda:</label>
    <input type="text" value="<?php echo esc_attr($artist_name); ?>" readonly style="width: 100%;" />
    <?php
}

