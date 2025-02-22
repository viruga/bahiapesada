<?php 

// Função para formatar o nome do artista
function format_artist_name($artist_name) {
    // Converte caracteres com acentos para suas versões sem acento
    $formatted_name = iconv('UTF-8', 'ASCII//TRANSLIT', $artist_name);

    // Remove caracteres especiais e substitui espaços por hifens
    $formatted_name = strtolower(trim($formatted_name)); // Converte para minúsculas e remove espaços em branco
    $formatted_name = preg_replace('/[^a-z0-9\s-]/', '', $formatted_name); // Remove caracteres indesejados
    $formatted_name = preg_replace('/[\s-]+/', '-', $formatted_name); // Substitui espaços e hifens por um único hifen

    return $formatted_name;
}

// Função para formatar a duração
function format_duration($duration_ms) {
    if (isset($duration_ms) && $duration_ms > 0) {
        return gmdate("i:s", $duration_ms / 1000);
    }
    return '00:00'; // Valor padrão
}

// Função para arredondar números
function format_followers_count($count) {
    if ($count >= 2000000000) {
        return round($count / 1000000000, 1) . ' bilhões'; // Para 2.000.000.000 ou mais
    } elseif ($count >= 1000000000 && $count < 2000000000) {
        return round($count / 1000000000, 1) . ' bilhão'; // Para de 1.000.000.000 a 1.999.999.999
    } elseif ($count >= 2000000) {
        return round($count / 1000000, 1) . ' milhões'; // Para 2.000.000 ou mais
    } elseif ($count >= 1000000 && $count < 2000000) {
        return round($count / 1000000, 1) . ' milhão'; // Para de 1.000.000 a 1.999.999
    } elseif ($count >= 1000) {
        return round($count / 1000, 1) . ' mil'; // Para de 1.000 a 999.999
    }
    return intval($count); // Retorna o número original se for menor que 1000
}