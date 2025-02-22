<?php
/**
 * Title: Filtros de bandas
 * Slug: filtros-bandas
 * Inserter: no
 */
?>

<form method="GET" action="" class="d-block d-100">
    <input type="hidden" name="post_type" value="bandas">
    
    <!-- Filtro por Gênero -->
    <select class="form-select form-select-sm d-inline-block w-auto me-2 my-1" name="genero" id="filtro-genero">
        <option value="">Todos os gêneros</option>
        <?php
        // Obtém apenas as categorias pai (parent = 0)
        $generos = get_terms(array(
            'taxonomy' => 'genero',
            'hide_empty' => false,
            'parent' => 0 // Filtra apenas as categorias pai
        ));

        foreach ($generos as $genero) {
            $selected = (isset($_GET['genero']) && $_GET['genero'] == $genero->slug) ? 'selected' : '';
            echo '<option value="' . $genero->slug . '" ' . $selected . '>' . $genero->name . '</option>';
        }
        ?>
    </select>

    <!-- Filtro por País -->
    <?php
    $brasil_term = get_term_by('slug', 'brasil', 'pais'); /* Obtém o ID do termo "brasil" */

    if ($brasil_term) {
        // Obtém todas as categorias filhas de "brasil"
        $child_terms = get_term_children($brasil_term->term_id, 'pais');

        // Verifica se estamos na taxonomia "pais" com o termo "brasil" ou uma de suas categorias filhas
        if (is_tax('pais', 'brasil') || in_array(get_queried_object_id(), $child_terms)) {
        ?>
            <select class="form-select form-select-sm d-inline-block w-auto me-2 my-1" name="pais" id="filtro-pais">
                <option value="">Estados do Brasil</option>
                <?php
                // Obtém as categorias filhas (termos com parent definido)
                $paises = get_terms(array(
                    'taxonomy'   => 'pais',
                    'hide_empty' => false,
                    'parent'     => get_term_by('slug', 'brasil', 'pais')->term_id, // Ajuste aqui se necessário
                ));

                // Exibe cada país filho no select
                foreach ($paises as $pais) {
                    $selected = (isset($_GET['pais']) && $_GET['pais'] == $pais->slug) ? 'selected' : '';
                    echo '<option value="' . esc_attr($pais->slug) . '" ' . $selected . '>' . esc_html($pais->name) . '</option>';
                }
                ?>
            </select>
        <?php
        } else {
        ?>
            <select class="form-select form-select-sm d-inline-block w-auto me-2 my-1" name="pais" id="filtro-pais">
                <option value="">Todos os países</option>
                <?php
                // Obtendo todos os países
                $paises = get_terms(array('taxonomy' => 'pais', 'hide_empty' => false));

                // Filtrando para mostrar apenas as categorias pai
                foreach ($paises as $pais) {
                    if ($pais->parent == 0) { // Verifica se é uma categoria pai
                        $selected = (isset($_GET['pais']) && $_GET['pais'] == $pais->slug) ? 'selected' : '';
                        echo '<option value="' . esc_attr($pais->slug) . '" ' . $selected . '>' . esc_html($pais->name) . '</option>';
                    }
                }
                ?>
            </select>
        <?php
        }
    }
    ?>

    <!-- Filtro por Décadas -->
    <select class="form-select form-select-sm d-inline-block w-auto me-2 my-1" name="decada" id="filtro-decada">
        <option value="">Todas as décadas</option>
        <option value="1970" <?php echo (isset($_GET['decada']) && $_GET['decada'] == '1970') ? 'selected' : ''; ?>>Anos 70</option>
        <option value="1980" <?php echo (isset($_GET['decada']) && $_GET['decada'] == '1980') ? 'selected' : ''; ?>>Anos 80</option>
        <option value="1990" <?php echo (isset($_GET['decada']) && $_GET['decada'] == '1990') ? 'selected' : ''; ?>>Anos 90</option>
        <option value="2000" <?php echo (isset($_GET['decada']) && $_GET['decada'] == '2000') ? 'selected' : ''; ?>>Anos 2000</option>
        <option value="2010" <?php echo (isset($_GET['decada']) && $_GET['decada'] == '2010') ? 'selected' : ''; ?>>Anos 2010</option>
        <option value="2020" <?php echo (isset($_GET['decada']) && $_GET['decada'] == '2020') ? 'selected' : ''; ?>>Anos 2020</option>
    </select>

    <!-- Ordenar por ano ou relevância -->
    <select class="form-select form-select-sm d-inline-block w-auto me-2 my-1" name="ordenar" id="ordenar">
        <option value="formation_year_desc" <?php selected( isset($_GET['ordenar']) && $_GET['ordenar'] == 'formation_year_desc' ); ?>>Ordenar por mais novas</option>
        <option value="formation_year_asc" <?php selected( isset($_GET['ordenar']) && $_GET['ordenar'] == 'formation_year_asc' ); ?>>Ordenar por clássicas</option>
        <option value="youtube_views" <?php selected( isset($_GET['ordenar']) && $_GET['ordenar'] == 'youtube_views' ); ?>>Por relevância</option>
    </select>

    <button type="submit" class="btn btn-primary btn-sm my-1">Filtrar</button>
</form>
