<?php
/**
 * Title: Loop de Eventos
 * Slug: loop-eventos
 * Categories: loop
 * Description: Loop de eventos
 * Inserter: no
 */
?>

<div class="container">
	<div class="d-block d-md-flex justify-content-between my-5">
		<h2 class="mb-0 pe-4">Todos os eventos da Bahia</h2>
	</div>
	<div class="row gx-md-4 gx-3">
	<?php
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$hoje = date('Y-m-d');

	$args = array(
		'post_type'      => 'eventos',
		'posts_per_page' => -1,
		'meta_key'       => '_evento_dia',
		'orderby'        => 'meta_value',
		'order'          => 'DESC',
		'meta_query'     => array(
			array(
				'key'     => '_evento_dia',
				'value'   => '0000-01-01', // pega tudo
				'compare' => '>=',
				'type'    => 'DATE'
			)
		)
	);

	$query = new WP_Query($args);

	if ($query->have_posts()) :
		while ($query->have_posts()) : $query->the_post();
			$dia = get_post_meta(get_the_ID(), '_evento_dia', true);
			$ingresso = get_post_meta(get_the_ID(), '_evento_ingresso', true);
			$produtora_terms = wp_get_post_terms(get_the_ID(), 'evento_produtora', array('fields' => 'names'));
			$cidade_terms = wp_get_post_terms(get_the_ID(), 'evento_cidade', array('fields' => 'names'));
			$produtora_nome = !empty($produtora_terms) ? implode(', ', $produtora_terms) : '';
			$cidade_nome = !empty($cidade_terms) ? implode(', ', $cidade_terms) : '';

			// Verifica se o evento já passou
			$is_passado = ($dia < $hoje);
			$extra_class = $is_passado ? 'evento-antigo' : '';
			?>
			<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
				<div class="evento mb-4 <?php echo $extra_class; ?>">
					<a class="evento-cover d-block bg-secondary rounded overflow-hidden" href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail('thumbnail', array('class' => 'w-100 h-auto', 'loading' => 'lazy', 'alt' => get_the_title())); ?>
					</a>
					<h3 class="h5 mt-3"><?php the_title(); ?></h3>
					<?php 
					if ($produtora_nome || $cidade_nome) {
						echo '<p class="mb-1">';
						if ($produtora_nome) {
							echo esc_html($produtora_nome);
						}
						if ($produtora_nome && $cidade_nome) {
							echo ' - ';
						}
						if ($cidade_nome) {
							echo esc_html($cidade_nome);
						}
						echo '</p>';
					}

					if (!empty($dia)) {
						$timestamp = strtotime($dia);
						echo '<p class="mb-1">' . esc_html(date_i18n(get_option('date_format'), $timestamp)) . '</p>';
					}

					if (!$is_passado) {
						if (!empty($ingresso)) {
							echo '<a href="' . esc_html($ingresso) . '" target="blank" class="wp-element-button py-1 mt-2 rounded d-inline-block has-background has-black-color has-vivid-green-cyan-background-color">Comprar ingresso</a>';
						}
					}
					?>
				</div>
			</div>
			<?php
		endwhile;

		// Paginação
		$pagination_args = array(
			'current' => $paged,
			'total'   => $query->max_num_pages,
			'prev_text' => __('Anterior', 'textdomain'),
			'next_text' => __('Próxima', 'textdomain'),
		);
		echo '<div class="paginate border border-0 pt-5">';
		echo paginate_links($pagination_args);
		echo '</div>';

		wp_reset_postdata();
	endif;
	?>
	</div>
 </div>