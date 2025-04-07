<?php 

add_action('add_meta_boxes', 'add_banda_metabox_to_eventos');
function add_banda_metabox_to_eventos() {
    add_meta_box(
        'banda_metabox',
        'Bandas do Evento',
        'render_banda_metabox',
        'eventos',
        'side',
        'default'
    );
}

function render_banda_metabox($post) {
    $selected_bandas = get_post_meta($post->ID, '_bandas_relacionadas', true);
    $selected_bandas = is_array($selected_bandas) ? $selected_bandas : [];

    $bandas = get_posts([
        'post_type' => 'bandas',
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC'
    ]);

    echo '<select id="bandas-select" name="bandas_relacionadas[]" multiple>';
    foreach ($bandas as $banda) {
        $selected = in_array($banda->ID, $selected_bandas) ? 'selected' : '';
        echo "<option value='{$banda->ID}' {$selected}>{$banda->post_title}</option>";
    }
    echo '</select>';
}

add_action('save_post_eventos', 'save_bandas_relacionadas');
function save_bandas_relacionadas($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['bandas_relacionadas'])) {
        $bandas = array_map('intval', $_POST['bandas_relacionadas']);
        update_post_meta($post_id, '_bandas_relacionadas', $bandas);
    } else {
        delete_post_meta($post_id, '_bandas_relacionadas');
    }
}
