<?php

// Função para baixar a imagem e definir como imagem destacada
function toma_set_album_featured_image($post_id, $image_url) {
    // Verifica se a URL da imagem não está vazia
    if (empty($image_url)) {
        return;
    }

    // Verifica se o post já está publicado
    $post_status = get_post_status($post_id);
    if ($post_status !== 'publish') {
        return;
    }

    // Recupera a URL da imagem salva nos metadados do post
    $existing_image_url = get_post_meta($post_id, 'toma_album_image_url', true);

    // Se a imagem já foi salva, não faz nada
    if ($existing_image_url === $image_url) {
        return;
    }

    // Faz o download da imagem
    $response = wp_remote_get($image_url);
    
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
        return;
    }

    // Obtém o conteúdo da imagem
    $image_data = wp_remote_retrieve_body($response);
    
    // Verifica o tipo MIME da imagem
    $content_type = wp_remote_retrieve_header($response, 'content-type');
    $file_extension = '';

    // Define a extensão com base no tipo MIME
    if (strpos($content_type, 'image/jpeg') !== false) {
        $file_extension = 'jpg';
    } elseif (strpos($content_type, 'image/png') !== false) {
        $file_extension = 'png';
    }

    // Se a extensão não for reconhecida, não continua
    if (empty($file_extension)) {
        return;
    }

    // Gera um nome de arquivo para a imagem
    $file_name = 'album_' . $post_id . '.' . $file_extension;
    
    // Define os diretórios para upload
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['path'] . '/' . $file_name;

    // Salva a imagem no diretório de uploads
    file_put_contents($file_path, $image_data);

    // Prepara os dados do arquivo para o WordPress
    $attachment = array(
        'post_mime_type' => $content_type,
        'post_title' => sanitize_file_name($file_name),
        'post_content' => '',
        'post_status' => 'inherit'
    );

    // Insere o anexo na biblioteca de mídia
    $attachment_id = wp_insert_attachment($attachment, $file_path, $post_id);

    // Gera metadados para a imagem
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attachment_id, $file_path);
    wp_update_attachment_metadata($attachment_id, $attach_data);

    // Define a imagem destacada do post
    set_post_thumbnail($post_id, $attachment_id);

    // Salva a URL da imagem nos metadados do post
    update_post_meta($post_id, 'toma_album_image_url', $image_url);
}

// Função para buscar os dados do álbum no Spotify
function toma_fetch_spotify_album_data($spotify_album_url, $post_id) {
    preg_match('/album\/([a-zA-Z0-9]+)/', $spotify_album_url, $matches);
    if (!isset($matches[1])) {
        return;
    }
    $album_id = $matches[1];

    $url = "https://api.spotify.com/v1/albums/" . $album_id;
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
        // Salvar dados do álbum
        update_post_meta($post_id, '_spotify_album_id', sanitize_text_field($data['id']));
        update_post_meta($post_id, '_spotify_album_name', sanitize_text_field($data['name']));
        update_post_meta($post_id, '_spotify_album_type', sanitize_text_field($data['album_type']));
        update_post_meta($post_id, '_spotify_album_image', sanitize_text_field($data['images'][0]['url']));
        update_post_meta($post_id, '_spotify_album_release_date', sanitize_text_field($data['release_date']));
        update_post_meta($post_id, '_spotify_album_total_tracks', intval($data['total_tracks']));

        // Salvar apenas o primeiro artista
        if (!empty($data['artists'][0])) {
            $first_artist = $data['artists'][0];
            update_post_meta($post_id, '_spotify_album_artist_id', sanitize_text_field($first_artist['id']));
            update_post_meta($post_id, '_spotify_album_artist_name', sanitize_text_field($first_artist['name']));
        }

        // Salvar nomes das tracks com id e duração
        if (!empty($data['tracks']['items'])) {
            $track_data = array(); // Array para armazenar os dados das faixas
            foreach ($data['tracks']['items'] as $track) {
                $track_data[] = array(
                    'id' => sanitize_text_field($track['id']),
                    'name' => sanitize_text_field($track['name']),
                    'duration_ms' => intval($track['duration_ms'])
                );
            }
            // Salvar dados das faixas como um único meta
            update_post_meta($post_id, '_spotify_album_tracks', $track_data);
        }

        // Baixar imagem e definir como imagem destacada
        toma_set_album_featured_image($post_id, $data['images'][0]['url']);
    }
}

// Função para adicionar a metabox de URL do Álbum
function toma_add_spotify_album_url_meta_box() {
    add_meta_box(
        'spotify_album_url_meta_box',
        'URL do Álbum no Spotify',
        'toma_render_spotify_album_url_meta_box',
        'albuns',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'toma_add_spotify_album_url_meta_box');

// Função para renderizar a metabox de URL do Álbum
function toma_render_spotify_album_url_meta_box($post) {
    $spotify_album_url = get_post_meta($post->ID, '_spotify_album_url', true);
    ?>
    <p>
        <label for="spotify_album_url">Insira a URL do álbum no Spotify:</label>
        <input type="url" id="spotify_album_url" name="spotify_album_url" value="<?php echo esc_attr($spotify_album_url); ?>" style="width: 100%;" />
    </p>
    <?php
    $is_highlighted = get_post_meta($post->ID, '_is_highlighted', true);
    $checked = ($is_highlighted === '1') ? 'checked' : '';
    ?>
	<p>
        <label for="is_highlighted">
            <input type="checkbox" id="is_highlighted" name="is_highlighted" value="1" <?php echo $checked; ?> />
            Destacar Álbum
        </label>
    </p>
	
	<?php
    $is_classic = get_post_meta($post->ID, '_is_classic', true);
    $checked2 = ($is_classic === '1') ? 'checked' : '';
    ?>
	<p>
        <label for="is_classic">
            <input type="checkbox" id="is_classic" name="is_classic" value="1" <?php echo $checked2; ?> />
            Álbum clássico
        </label>
    </p>
    <?php
}

// Função para salvar a URL do Álbum
if (!function_exists('toma_save_spotify_album_url_meta_box')) {
function toma_save_spotify_album_url_meta_box($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Verifica se a URL do álbum está presente
    if (!isset($_POST['spotify_album_url'])) {
        return;
    }

    $spotify_album_url = sanitize_text_field($_POST['spotify_album_url']);
    update_post_meta($post_id, '_spotify_album_url', $spotify_album_url);

    // Somente buscar dados se a postagem já estiver publicada
    if (get_post_status($post_id) === 'publish') {
        toma_fetch_spotify_album_data($spotify_album_url, $post_id);
    }

    // Verifica se o checkbox está definido
    $is_highlighted = isset($_POST['is_highlighted']) ? '1' : '0';
    update_post_meta($post_id, '_is_highlighted', $is_highlighted);
	
	// Verifica se o checkbox está definido
    $is_classic = isset($_POST['is_classic']) ? '1' : '0';
    update_post_meta($post_id, '_is_classic', $is_classic);
}
}
add_action('save_post', 'toma_save_spotify_album_url_meta_box');

// Função para adicionar a metabox de dados do álbum
function toma_add_album_data_meta_box() {
    add_meta_box(
        'album_data_meta_box',
        'Dados do Álbum',
        'toma_render_album_data_meta_box',
        'albuns',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'toma_add_album_data_meta_box');

// Função para renderizar a metabox de dados do álbum
function toma_render_album_data_meta_box($post) {
    $album_id = get_post_meta($post->ID, '_spotify_album_id', true);
    $album_name = get_post_meta($post->ID, '_spotify_album_name', true);
    $album_type = get_post_meta($post->ID, '_spotify_album_type', true);
    $album_image = get_post_meta($post->ID, '_spotify_album_image', true);
    $release_date = get_post_meta($post->ID, '_spotify_album_release_date', true);
    $total_tracks = get_post_meta($post->ID, '_spotify_album_total_tracks', true);

    // Recupera o primeiro artista do álbum
    $first_artist_id = get_post_meta($post->ID, '_spotify_album_artist_id', true);
    $first_artist_name = get_post_meta($post->ID, '_spotify_album_artist_name', true);

    // Recupera os dados das faixas
    $track_data = get_post_meta($post->ID, '_spotify_album_tracks', true);    

    ?>
    <label>ID do Álbum:</label>
    <input type="text" value="<?php echo esc_attr($album_id); ?>" readonly style="width: 100%;" />
    <br>
    <label>Nome do Álbum:</label>
    <input type="text" value="<?php echo esc_attr($album_name); ?>" readonly style="width: 100%;" />
    <br>
    <label>Tipo de Álbum:</label>
    <input type="text" value="<?php echo esc_attr($album_type); ?>" readonly style="width: 100%;" />
    <br>
    <label>Data de Lançamento:</label>
    <input type="text" value="<?php echo esc_attr($release_date); ?>" readonly style="width: 100%;" />
    <br>
    <label>Total de Faixas:</label>
    <input type="number" value="<?php echo esc_attr($total_tracks); ?>" readonly style="width: 100%;" />
    <br>
    <label>Nome do Artista:</label>
    <input type="text" value="<?php echo esc_attr($first_artist_name); ?>" readonly style="width: 100%;" />
    <br>
    <label>ID do Artista:</label>
    <input type="text" value="<?php echo esc_attr($first_artist_id); ?>" readonly style="width: 100%;" />
    <br>
    <label>Faixas:</label>
    <style>
        .track-admin span:first-child {
            width: 45%;
            display: inline-block;
        }
        .track-admin span:last-child {
            display: inline-block;
            float: right;
            width: 200px;
        }
    </style>
    <div class="track-admin" style="margin-left: 24px; font-weight: bold;">
        <span>Nome</span>
        <span>Duração</span>
        <span>ID</span>
    </div>
    <ol>
        <?php
        // Exibir a lista de faixas com id e duração
        if (!empty($track_data)) {
            foreach ($track_data as $track) {
                echo '<li class="track-admin">';
                    echo '<span>' . esc_html($track['name']) . '</span>';
                    echo '<span>' . esc_html(format_duration($track['duration_ms'])) . '</span>';
                    echo '<span>' . esc_html($track['id']) . '</span>';
                echo '</li>';
            }
        }
        ?>
    </ol>
    <?php
}

// Adiciona a metabox para mostrar os gêneros da banda
function toma_add_band_genre_meta_box() {
    add_meta_box(
        'band_genre_meta_box', // ID da metabox
        'Gêneros da Banda', // Título da metabox
        'toma_render_band_genre_meta_box', // Função para renderizar o conteúdo
        'albuns', // Post type onde a metabox será adicionada
        'side', // Contexto (posição) da metabox
        'default' // Prioridade
    );
}
add_action('add_meta_boxes', 'toma_add_band_genre_meta_box');

// Função que renderiza o conteúdo da metabox
function toma_render_band_genre_meta_box($post) {
    // Obtém o nome da banda do álbum
    $band_name = get_post_meta($post->ID, '_spotify_album_artist_name', true);
    
    // Caso o nome da banda exista
    if ($band_name) {
        // Busca o post da banda pelo nome
        $band_posts = get_posts(array(
            'post_type' => 'bandas',
            'meta_query' => array(
                array(
                    'key' => '_spotify_artist_name', // Meta key onde o nome da banda está salvo
                    'value' => $band_name, // Valor a ser buscado
                    'compare' => '='
                )
            )
        ));

        if (!empty($band_posts)) {
            // Supondo que apenas um post corresponda
            $band_post = $band_posts[0];
            $terms = wp_get_post_terms($band_post->ID, 'genero', array('fields' => 'all'));
            
            // Exibe os termos da taxonomia 'gênero'
            if (!empty($terms)) {
                echo '<ul>';
                foreach ($terms as $term) {
                    echo '<li>' . esc_html($term->name) . '</li>'; // Exibe o nome de cada gênero
                }
                echo '</ul>';
            } else {
                echo 'Nenhum gênero encontrado para esta banda.';
            }
        } else {
            echo 'Banda não cadastrada.';
        }
    } else {
        echo 'Nome da banda não disponível.';
    }
}

// Adiciona a metabox para mostrar os países da banda
function toma_add_band_country_meta_box() {
    add_meta_box(
        'band_country_meta_box', // ID da metabox
        'País da Banda', // Título da metabox
        'toma_render_band_country_meta_box', // Função para renderizar o conteúdo
        'albuns', // Post type onde a metabox será adicionada
        'side', // Contexto (posição) da metabox
        'default' // Prioridade
    );
}
add_action('add_meta_boxes', 'toma_add_band_country_meta_box');

// Função que renderiza o conteúdo da metabox
function toma_render_band_country_meta_box($post) {
    // Obtém o nome da banda do álbum
    $band_name = get_post_meta($post->ID, '_spotify_album_artist_name', true);
    
    // Caso o nome da banda exista
    if ($band_name) {
        // Busca o post da banda pelo nome
        $band_posts = get_posts(array(
            'post_type' => 'bandas',
            'meta_query' => array(
                array(
                    'key' => '_spotify_artist_name', // Meta key onde o nome da banda está salvo
                    'value' => $band_name, // Valor a ser buscado
                    'compare' => '='
                )
            )
        ));

        if (!empty($band_posts)) {
            // Supondo que apenas um post corresponda
            $band_post = $band_posts[0];
            $terms = wp_get_post_terms($band_post->ID, 'pais', array('fields' => 'all'));
            
            // Exibe os termos da taxonomia 'país'
            if (!empty($terms)) {
                echo '<ul>';
                foreach ($terms as $term) {
                    echo '<li>' . esc_html($term->name) . '</li>'; // Exibe o nome de cada país
                }
                echo '</ul>';
            } else {
                echo 'Nenhum país encontrado para esta banda.';
            }
        } else {
            echo 'Banda não cadastrada.';
        }
    } else {
        echo 'Nome da banda não disponível.';
    }
}
