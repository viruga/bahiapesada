<?php

// Função para adicionar o metabox do YouTube
function render_youtube_meta_box($post) {
    // Obtém o valor atual do post_meta (caso já exista)
    $youtube_channel_id = get_post_meta($post->ID, 'youtube_channel_id', true);
    
    // Renderiza o campo de input na metabox
    ?>
    <label for="youtube_channel_id">YouTube Channel ID:</label>
    <input type="text" id="youtube_channel_id" name="youtube_channel_id" value="<?php echo esc_attr($youtube_channel_id); ?>" />
    <?php
}

// Função para salvar o ID do canal do YouTube
function save_youtube_meta_box($post_id) {
    // Verifica se é o post type "banda" e se não é uma revisão/autosave
    if (get_post_type($post_id) !== 'bandas' || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Salva o ID do canal do YouTube
    if (isset($_POST['youtube_channel_id'])) {
        $youtube_channel_id = sanitize_text_field($_POST['youtube_channel_id']);
        update_post_meta($post_id, 'youtube_channel_id', $youtube_channel_id);
    }
}
add_action('add_meta_boxes', function() {
    add_meta_box('youtube_meta_box', 'YouTube Channel Info', 'render_youtube_meta_box', 'bandas', 'normal', 'high');
});
add_action('save_post', 'save_youtube_meta_box');
