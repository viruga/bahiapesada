<?php
/**
 * Title: Loop de álbuns do Brasil
 * Slug: loop-albuns-brasil
 * Categories: loop
 * Description: Loop de albuns do Brasil
 * Inserter: no
 */
?>

<div class="container my-5 lancamentos-br-home">
	<h2 class="mb-4 d-flex justify-content-between align-items-end">Últimos lançamentos no Brasil 
		<small class="fs-6 fw-normal d-inline-block w-50 text-end"><a href="<?php echo site_url(); ?>/?post_type=albuns&pais=brasil&ordenacao=lancamentos&destaque=todos&tipo_album=todos">Todos os álbuns</a></small>
	</h2>
	<div class="row gx-md-4 gx-3">
		<?php 
		$country = 'Brasil'; // Substitua pelo país que deseja filtrar

		$band_posts = get_posts(array(
			'post_type' => 'bandas',
			'numberposts' => -1, // Pega todas as bandas
			'tax_query' => array(
				array(
					'taxonomy' => 'pais',
					'field' => 'name',
					'terms' => $country,
				),
			),
		));

		// Extrai os nomes das bandas que correspondem ao país
		$band_names = array();
		if (!empty($band_posts)) {
			foreach ($band_posts as $band_post) {
				$band_name = get_post_meta($band_post->ID, '_spotify_artist_name', true);
				if ($band_name) {
					$band_names[] = $band_name;
				}
			}
		}
		
		// Cria a query para buscar os álbuns randomizados
		$args = array(
			'post_type'      => 'albuns',
			'posts_per_page' => 4,
			'meta_key'       => '_spotify_album_release_date', // Define a chave meta para ordenação
			'orderby'        => 'meta_value',                  // Ordena pelo valor da meta key
			'order'          => 'DESC',                        // Ordem decrescente para pegar os lançamentos mais recentes
			'meta_query'     => array(
				array(
					'key'     => '_spotify_album_release_date',
					'compare' => 'EXISTS',
					'type'    => 'DATE',                       // Especifica o tipo como data
				),
				array(
					'key'     => '_spotify_album_artist_name',
					'value'   => $band_names,                  // Filtra pelos nomes das bandas
					'compare' => 'IN',                         // Verifica se o nome da banda está na lista
				),
			),
		);

	    // Executa a query
	    $query = new WP_Query( $args );
		?>
		
		<?php 
		if ( $query->have_posts() ) : 
		while ( $query->have_posts() ) : 
		$query->the_post(); 

		// Recuperando os metadados do álbum
	    $artist_name = get_post_meta(get_the_ID(), '_spotify_album_artist_name', true);
		//$release_date = get_post_meta(get_the_ID(), '_spotify_album_release_date', true);
		$release_date = date('d/m/Y', strtotime(get_post_meta(get_the_ID(), '_spotify_album_release_date', true)));
		
	    $album_type = get_post_meta(get_the_ID(), '_spotify_album_type', true);
		// Verifica o valor de $album_type e altera para o formato desejado
		if ($album_type === 'album') {
			$album_type = 'Álbum';
		} elseif ($album_type === 'single') {
			$album_type = 'Single';
		}
	    $album_id = get_post_meta(get_the_ID(), '_spotify_album_id', true);
	    $link_artist = format_artist_name($artist_name);
		?>
			<div class="lancamento-br-home col-md-3 col-6 mb-3">
				<div class="album">
					<a class="album-cover d-block bg-secondary rounded overflow-hidden" href="<?php echo home_url('/?bandas=') . esc_html($link_artist) . '/#album-id-' . esc_html($album_id); ?>">
						<?php the_post_thumbnail('medium', array('class' => 'w-100 h-auto')); ?>
					</a>
					
					<h2 class="h5 mt-3"><?php the_title(); ?></h2>

					<p class="mb-1">
						<small>
							<a href="<?php echo home_url('/?bandas=') . esc_html($link_artist); ?>">
								<?php echo esc_html($artist_name); ?>
							</a>
						</small>
					</p>
					<p class="mb-1">
						<small>
							<?php echo esc_html($release_date); ?> • <?php echo esc_html($album_type); ?>
						</small>
					</p>
					<?php //the_time( 'j \d\e F \d\e Y' ); ?>
					
					<p class="mb-1">
						<small>
						<?php
						// Obtendo o nome da banda do álbum
						$band_posts = get_posts(array(
							'post_type' => 'bandas',
							'meta_query' => array(
								array(
									'key' => '_spotify_artist_name',
									'value' => $artist_name,
									'compare' => '='
								)
							)
						));

						if (!empty($band_posts)) {
							$band_post = $band_posts[0];

							// Exibindo os gêneros
							$genres = wp_get_post_terms($band_post->ID, 'genero', array('fields' => 'names'));
							if (!empty($genres)) {
								echo esc_html(implode(', ', $genres));
							} else {
								echo 'Nenhum gênero encontrado.<br>';
							}

							// Exibindo os países
							$countries = wp_get_post_terms($band_post->ID, 'pais', array('fields' => 'names'));
							if (!empty($countries)) {
								echo ' • ' . esc_html(implode(', ', $countries)) . '<br>';
							} else {
								echo 'Nenhum país encontrado.<br>';
							}
						} else {
							echo 'Banda não encontrada.<br>';
						}
						?>
						</small>
					</p>
				</div>
			</div>

		<?php endwhile; wp_reset_postdata(); else : ?>
			<p>Nenhum álbum foi encontrado</p>
		<?php endif; ?>
	</div>
 </div>