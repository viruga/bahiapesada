<?php
/**
 * Title: Loop de Últimos Eventos
 * Slug: loop-ultimos-eventos
 * Categories: loop
 * Description: Loop de Últimos Eventos
 * Inserter: no
 */
?>

<?php
//Próximos eventos
$current_date = current_time('Y-m-d'); // Data atual

$args = array(
    'post_type' => 'eventos',
    'meta_query' => array(
        array(
            'key' => '_evento_dia',
            'value' => $current_date,
            'compare' => '>=', // Filtra eventos que ocorrem a partir da data atual
            'type' => 'DATE'
        ),
    ),
    'orderby' => '_evento_dia', // Ordenar por dia do evento
    'order' => 'ASC',
    'posts_per_page' => 8
);

$query = new WP_Query($args);
echo '<div class="container my-5">';
if ($query->have_posts()) {
    echo '<h2 class="mb-4">Próximos eventos</h2>';
    echo '<div class="row gx-md-4 gx-3">';
    while ($query->have_posts()) {
        $query->the_post();
		
		// Recupera e formata a data do evento em português
		setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'Portuguese_Brazil');
		$data_evento = strtotime(get_post_meta(get_the_ID(), '_evento_dia', true));
		$data_formatada = strftime('%d de %b', $data_evento);
		$local = get_post_meta(get_the_ID(), '_evento_local', true);
		
        // Exibir thumbnail, título, metadados e termos das taxonomias
        ?>
        <div class="col-md-3 col-6">
			<a href="#evento-<?php the_ID(); ?>" data-bs-toggle="modal">
			<?php 
			if (has_post_thumbnail()) {
				the_post_thumbnail('medium', array('class' => 'w-100 h-auto rounded mb-3')); 
			}
			?>
			</a>
			<p class="mb-1"><?php echo esc_html($data_formatada); ?> • <?php echo esc_html(get_post_meta(get_the_ID(), '_evento_hora', true)); ?></p>
			<h3><?php the_title(); ?></h3>
            <!--<p>Valor: R$ <?php //echo esc_html(get_post_meta(get_the_ID(), '_evento_valor', true)); ?></p>-->
			
			<p>
				<small>
				<?php
				// Exibir termos da taxonomia 'evento_produtora'
				$produtora_terms = get_the_terms(get_the_ID(), 'evento_produtora');
				if ($produtora_terms && !is_wp_error($produtora_terms)) {
					foreach ($produtora_terms as $term) {
						$term_info = get_term($term->term_id, 'evento_produtora');
						echo esc_html($term_info->name) . ' - ';
						//echo esc_html($term_info->description) . ' - ';
					}
				}
		
				echo get_the_content() . ' ';
		
				echo esc_html($local) . ' ';

				// Exibir termos da taxonomia 'eventos_cidade'
				$cidade_terms = get_the_terms(get_the_ID(), 'evento_cidade');
				if ($cidade_terms && !is_wp_error($cidade_terms)) {
					$cidade_names = wp_list_pluck($cidade_terms, 'name');
					echo implode(', ', $cidade_names);
				} else {
					echo ''; // Exibe mensagem se não houver termos
				}

				// Exibir bandas
				$bandas_ids = get_post_meta(get_the_ID(), '_bandas_relacionadas', true);
				if (!empty($bandas_ids)) {
					echo '<br>Com as bandas: ';
					$links = [];
					foreach ($bandas_ids as $banda_id) {
						$links[] = '<a class="d-inline-block" href="' . get_permalink($banda_id) . '">' . get_the_title($banda_id) . '</a>';
					}
					echo implode(', ', $links);
				}
				?>
				</small>
			</p>
        </div>

		<div class="modal fade" id="evento-<?php the_ID(); ?>" tabindex="-1" aria-labelledby="evento-<?php the_ID(); ?>Label" aria-hidden="true">
		  <div class="modal-dialog modal-xl">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			  </div>
			  <div class="modal-body">
				<?php 
				if (has_post_thumbnail()) {
					the_post_thumbnail('full', array('class' => 'w-100 h-auto')); 
				}
				?>
			  </div>
			</div>
		  </div>
		</div>

        <?php
    }
    echo '</div>';
} else {
    echo '';
}
echo '</div>';
wp_reset_postdata();
?>


