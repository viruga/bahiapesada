<?php

// Adiciona colunas personalizadas na listagem de álbuns
function toma_add_album_columns($columns) {
    // Cria um array vazio para armazenar as novas colunas
    $new_columns = [];

    // Adiciona a coluna de checklist
    $new_columns['cb'] = $columns['cb'];

    // Adiciona as novas colunas na ordem desejada
    $new_columns['title'] = 'Título';
    $new_columns['artist_name'] = 'Nome do Artista';
    $new_columns['album_type'] = 'Tipo de Álbum';
    $new_columns['release_date'] = 'Data de Lançamento';
    
    // Adiciona a nova coluna para álbuns destacados
    $new_columns['highlighted'] = 'Destacado';

    // Remove a coluna 'date' padrão
    unset($columns['date']);
    
    // Adiciona a coluna de data ao final
    $columns['date'] = 'Data';

    // Mescla as novas colunas com as colunas restantes
    return array_merge($new_columns, $columns);
}
add_filter('manage_albuns_posts_columns', 'toma_add_album_columns');


// Preenche as colunas personalizadas com dados
function toma_fill_album_columns($column, $post_id) {
    switch ($column) {
        case 'artist_name':
            $artist_name = get_post_meta($post_id, '_spotify_album_artist_name', true);
            echo esc_html($artist_name);
            break;
        case 'album_type':
            $album_type = get_post_meta($post_id, '_spotify_album_type', true);
            echo esc_html($album_type);
            break;
        case 'release_date':
            $release_date = get_post_meta($post_id, '_spotify_album_release_date', true);
            echo esc_html($release_date);
            break;
        case 'highlighted':
            // Verifica se o álbum está destacado ou como clássico
            $is_highlighted = get_post_meta($post_id, '_is_highlighted', true);
			$is_classic = get_post_meta($post_id, '_is_classic', true);
            // Mostra o ícone apropriado
            if ($is_highlighted === '1') {
                echo '<span class="dashicons dashicons-star-filled" style="color: gold;" title="Destacado"></span>';
            } elseif ($is_classic === '1') {
				echo '<span class="dashicons dashicons-awards" style="color: gold;" title="Clássico"></span>';
            } else {
                echo '<span class="dashicons dashicons-star-empty" style="color: lightgray;" title="Sem destaque"></span>';
            }
            break;
    }
}
add_action('manage_albuns_posts_custom_column', 'toma_fill_album_columns', 10, 2);



// Adiciona o filtro por tipo de álbum na tela de listagem de álbuns
function toma_filter_album_by_type() {
    // Verifica se estamos na tela correta
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'albuns') {
        $types = ['Album', 'Single', 'EP']; // Adicione os tipos de álbum disponíveis
        ?>
        <select name="album_type">
            <option value="">Tipos de álbuns</option>
            <?php foreach ($types as $type): ?>
                <option value="<?php echo esc_attr($type); ?>" <?php selected(isset($_GET['album_type']) ? $_GET['album_type'] : '', $type); ?>>
                    <?php echo esc_html($type); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
}
add_action('restrict_manage_posts', 'toma_filter_album_by_type');


// Filtra os resultados com base no tipo de álbum selecionado
function toma_filter_albums_by_type($query) {
    if (!is_admin()) {
        return;
    }

    global $pagenow;

    // Verifica se estamos na tela de listagem de álbuns
    if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'albuns') {
        if (!empty($_GET['album_type'])) {
            $query->set('meta_query', [
                [
                    'key' => '_spotify_album_type', // Alterado para _spotify_album_type
                    'value' => sanitize_text_field($_GET['album_type']),
                    'compare' => '='
                ]
            ]);
        }
    }
}
add_action('pre_get_posts', 'toma_filter_albums_by_type');

// Adiciona o filtro por nome do artista na tela de listagem de álbuns
function toma_filter_album_by_artist() {
    global $wpdb;
    
    // Verifica se estamos na tela correta
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'albuns') {
        
        // Obtém todos os valores únicos do campo meta '_spotify_album_artist_name'
        $artists = $wpdb->get_col("
            SELECT DISTINCT meta_value 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_spotify_album_artist_name' 
            ORDER BY meta_value ASC
        ");
        
        if (!empty($artists)) {
            ?>
            <select name="artist_name">
                <option value="">Todos os artistas</option>
                <?php foreach ($artists as $artist): ?>
                    <option value="<?php echo esc_attr($artist); ?>" <?php selected(isset($_GET['artist_name']) ? $_GET['artist_name'] : '', $artist); ?>>
                        <?php echo esc_html($artist); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php
        }
    }
}
add_action('restrict_manage_posts', 'toma_filter_album_by_artist');


// Filtra os resultados com base no nome do artista selecionado
function toma_filter_albums_by_artist($query) {
    if (!is_admin()) {
        return;
    }

    global $pagenow;

    // Verifica se estamos na tela de listagem de álbuns
    if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'albuns') {
        if (!empty($_GET['artist_name'])) {
            $query->set('meta_query', [
                [
                    'key' => '_spotify_album_artist_name',
                    'value' => sanitize_text_field($_GET['artist_name']),
                    'compare' => '='
                ]
            ]);
        }
    }
}
add_action('pre_get_posts', 'toma_filter_albums_by_artist');


// Registra a data de lançamento como uma coluna ordenável
function toma_register_album_sortable_columns($columns) {
    $columns['release_date'] = 'release_date'; // Adiciona a coluna de data de lançamento como ordenável
    return $columns;
}
add_filter('manage_edit-albuns_sortable_columns', 'toma_register_album_sortable_columns');


// Ordena os álbuns pela data de lançamento
function toma_order_albums_by_release_date($query) {
    if (!is_admin()) {
        return;
    }

    // Verifica se estamos na tela de listagem de álbuns
    if ($query->is_main_query() && isset($_GET['post_type']) && $_GET['post_type'] === 'albuns') {
        // Verifica se a coluna de ordenação é a data de lançamento
        if ('release_date' === $query->get('orderby')) {
            $query->set('meta_key', '_spotify_album_release_date'); // A chave do meta para ordenar
            $query->set('orderby', 'meta_value'); // Ordena pelo valor do meta
        }
    }
}
add_action('pre_get_posts', 'toma_order_albums_by_release_date');


// Função para salvar o tipo de álbum ao criar ou editar um álbum
function toma_save_album_meta_box($post_id) {
    // Verifica se o usuário tem permissão para salvar
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Salvar o tipo de álbum
    if (isset($_POST['album_type'])) {
        $album_type = sanitize_text_field($_POST['album_type']);
        update_post_meta($post_id, '_spotify_album_type', $album_type); // Alterado para _spotify_album_type
    }

    // Salvar outros metadados conforme necessário
    if (isset($_POST['_spotify_album_artist_name'])) {
        $artist_name = sanitize_text_field($_POST['_spotify_album_artist_name']);
        update_post_meta($post_id, '_spotify_album_artist_name', $artist_name);
    }

    if (isset($_POST['_spotify_album_release_date'])) {
        $release_date = sanitize_text_field($_POST['_spotify_album_release_date']);
        update_post_meta($post_id, '_spotify_album_release_date', $release_date);
    }
}
add_action('save_post', 'toma_save_album_meta_box');

