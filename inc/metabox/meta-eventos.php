<?php

function eventos_add_meta_box() {
    add_meta_box(
        'eventos_meta_box', // ID
        'Informações do Evento', // Título da Metabox
        'eventos_meta_box_callback', // Callback para exibir o conteúdo
        'eventos', // Post type
        'normal', // Localização
        'high' // Prioridade
    );
}
add_action('add_meta_boxes', 'eventos_add_meta_box');

function eventos_meta_box_callback($post) {
    // Recuperar os valores salvos (se existirem)
    $dia = get_post_meta($post->ID, '_evento_dia', true);
    $hora = get_post_meta($post->ID, '_evento_hora', true);
    $valor = get_post_meta($post->ID, '_evento_valor', true);
    $local = get_post_meta($post->ID, '_evento_local', true);
    $ingresso = get_post_meta($post->ID, '_evento_ingresso', true);
    
    // HTML para os campos da metabox
    ?>
    <label for="evento_dia">Dia do Evento *</label><br>
    <input type="date" name="evento_dia" id="evento_dia" value="<?php echo esc_attr($dia); ?>" style="width: 100%;" required><br><br>

    <label for="evento_hora">Hora do Evento</label>
    <input type="time" name="evento_hora" id="evento_hora" value="<?php echo esc_attr($hora); ?>" style="width: 100%;"><br><br>

    <label for="evento_valor">Valor do Ingresso (R$)</label>
    <input type="number" name="evento_valor" id="evento_valor" value="<?php echo esc_attr($valor); ?>" placeholder="Ex: 50.00" style="width: 100%;" min="0" step="0.01"><br><br>

    <label for="evento_ingresso">Link do Ingresso</label>
    <input type="url" name="evento_ingresso" id="evento_ingresso" value="<?php echo esc_attr($ingresso); ?>" placeholder="https://sympla.com.br..." style="width: 100%;"><br><br>

    <label for="evento_local">Local do Evento</label>
    <input type="text" name="evento_local" id="evento_local" value="<?php echo esc_attr($local); ?>" placeholder="Ex: Rua das Flores, 123" style="width: 100%;">
    <?php
}

function save_eventos_meta_box_data($post_id) {
    // Verificar se os dados foram enviados e salvar as informações
    if (isset($_POST['evento_dia'])) {
        // Salvar a data no formato padrão do WordPress
        $dia = sanitize_text_field($_POST['evento_dia']);
        update_post_meta($post_id, '_evento_dia', $dia);
    }

    if (isset($_POST['evento_hora'])) {
        update_post_meta($post_id, '_evento_hora', sanitize_text_field($_POST['evento_hora']));
    }
    
    if (isset($_POST['evento_valor'])) {
        update_post_meta($post_id, '_evento_valor', sanitize_text_field($_POST['evento_valor']));
    }

    if (isset($_POST['evento_ingresso'])) {
        update_post_meta($post_id, '_evento_ingresso', sanitize_text_field($_POST['evento_ingresso']));
    }

    if (isset($_POST['evento_local'])) {
        update_post_meta($post_id, '_evento_local', sanitize_text_field($_POST['evento_local']));
    }
}
add_action('save_post', 'save_eventos_meta_box_data');
