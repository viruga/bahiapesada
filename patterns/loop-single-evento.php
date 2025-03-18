<?php
/**
 * Title: Loop Single Evento
 * Slug: loop-single-evento
 * Description: Loop Single
 * Inserter: no
 */

$dia = get_post_meta(get_the_ID(), '_evento_dia', true);
$local = get_post_meta(get_the_ID(), '_evento_local', true);

echo '<div>Evento realizado em: ' . $dia . '</div>';
echo '<div>Local: ' . $local . '</div>';
?>
