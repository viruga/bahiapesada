<?php 

// Adiciona a metabox para o ano de formação e status da banda
function toma_add_banda_info_meta_box() {
    add_meta_box(
        'banda_info_meta_box', // ID da metabox
        'Informações da Banda', // Título da metabox
        'toma_render_banda_info_meta_box', // Função de callback para renderizar o conteúdo
        'bandas', // Post type onde a metabox será exibida
        'normal', // Contexto
        'high' // Prioridade
    );
}
add_action('add_meta_boxes', 'toma_add_banda_info_meta_box');

// Renderiza o conteúdo da metabox
function toma_render_banda_info_meta_box($post) {
	// Adiciona a nonce para verificação
    wp_nonce_field('save_banda_info_meta_box', 'banda_info_meta_box_nonce');
    
    // Recupera os valores salvos
    $formation_year = get_post_meta($post->ID, '_banda_formation_year', true);
    $status = get_post_meta($post->ID, '_banda_status', true);

    // Campo para o ano de formação
    echo '<label for="banda_formation_year">Ano de Formação:</label>';
    echo '<input type="number" id="banda_formation_year" name="banda_formation_year" value="' . esc_attr($formation_year) . '" />';

    // Campo para o status
    echo '<label for="banda_status">Status:</label>';
    $statuses = ['Ativa', 'Pendente', 'Pausa', 'Encerrada'];
    echo '<select id="banda_status" name="banda_status">';
    foreach ($statuses as $option) {
        $selected = ($option === $status) ? 'selected' : '';
        echo '<option value="' . esc_attr($option) . '" ' . $selected . '>' . esc_html($option) . '</option>';
    }
    echo '</select>';
}

// Salva os dados da metabox
function toma_save_banda_info_meta_box($post_id) {
    // Verifica a nonce para segurança
    if (!isset($_POST['banda_info_meta_box_nonce']) || !wp_verify_nonce($_POST['banda_info_meta_box_nonce'], 'save_banda_info_meta_box')) {
        return;
    }

    // Verifica se o usuário tem permissão para editar o post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Salva o ano de formação
    if (isset($_POST['banda_formation_year'])) {
        update_post_meta($post_id, '_banda_formation_year', sanitize_text_field($_POST['banda_formation_year']));
    }

    // Salva o status
    if (isset($_POST['banda_status'])) {
        update_post_meta($post_id, '_banda_status', sanitize_text_field($_POST['banda_status']));
    }
}
add_action('save_post', 'toma_save_banda_info_meta_box');
