<?php
/**
 * Title: Loop de Álbuns
 * Slug: loop-albuns
 * Categories: loop
 * Description: Loop de albuns
 * Inserter: no
 */
?>

<div class="container">
	<div class="d-block d-md-flex justify-content-between my-5">
		<h2 class="mb-0 pe-4">Álbuns</h2>
		<!-- wp:pattern {"slug":"filtros-albuns"} /-->
	</div>
	<div class="row gx-md-4 gx-3">
		<?php 
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		
		// Obtém o valor de tipo_album do filtro, se existir
		$tipo_album = $_GET['tipo_album'] ?? 'todos';
		
		$args = array(
			'post_type' => 'albuns',
			'paged' => $paged,
			'posts_per_page' => 66,
			'meta_query' => array(
				array(
					'key' => '_is_highlighted',
					'value' => '1',
					'compare' => '='
				)
			),
		);

		// Verifica o filtro de destaque
		if (isset($_GET['destaque'])) {
			if ($_GET['destaque'] === 'classico') {
				$args['meta_query'] = array(
					array(
						'key' => '_is_classic',
						'value' => '1',
						'compare' => '='
					)
				);
			} elseif ($_GET['destaque'] === 'todos') {
				// Remove o filtro para mostrar todos os álbuns
				$args['meta_query'] = array();
			}
		}

		// Verifica o filtro de ordenação
		if (isset($_GET['ordenacao'])) {
			if ($_GET['ordenacao'] === 'recentes') {
				$args['orderby'] = 'date';
				$args['order'] = 'DESC';
			} elseif ($_GET['ordenacao'] === 'antigos') {
				$args['meta_key'] = '_spotify_album_release_date';
				$args['orderby'] = 'meta_value';
				$args['order'] = 'ASC';
			} elseif ($_GET['ordenacao'] === 'lancamentos') {
				$args['meta_key'] = '_spotify_album_release_date';
				$args['orderby'] = 'meta_value';
				$args['order'] = 'DESC';
			}
		}
		
		// Adiciona o filtro do tipo de álbum à query
		if ($tipo_album === 'album') {
			$args['meta_query'][] = array(
				'key' => '_spotify_album_type',
				'value' => 'album',
				'compare' => '='
			);
		} elseif ($tipo_album === 'single') {
			$args['meta_query'][] = array(
				'key' => '_spotify_album_type',
				'value' => 'single',
				'compare' => '='
			);
		}
		
		// Verifica se o filtro de país foi enviado
		if (isset($_GET['pais']) && !empty($_GET['pais'])) {
			$pais_slug = sanitize_text_field($_GET['pais']);

			if ($pais_slug === 'mundo') {
				// Exclui o Brasil
				$bandas = get_posts(array(
					'post_type' => 'bandas',
					'posts_per_page' => -1,
					'fields' => 'ids',
					'tax_query' => array(
						array(
							'taxonomy' => 'pais',
							'field' => 'slug',
							'terms' => 'brasil',
							'operator' => 'NOT IN', // Excluir o Brasil
						),
					),
				));
			} else {
				// Filtros para Brasil ou Bahia
				$bandas = get_posts(array(
					'post_type' => 'bandas',
					'posts_per_page' => -1,
					'fields' => 'ids',
					'tax_query' => array(
						array(
							'taxonomy' => 'pais',
							'field' => 'slug',
							'terms' => $pais_slug, // Filtra por Brasil ou Bahia
						),
					),
				));
			}

			if (!empty($bandas)) {
				// Recupera os nomes das bandas
				$band_names = array();
				foreach ($bandas as $banda_id) {
					$band_name = get_post_meta($banda_id, '_spotify_artist_name', true);
					if (!empty($band_name)) {
						$band_names[] = $band_name;
					}
				}

				if (!empty($band_names)) {
					$args['meta_query'][] = array(
						'key' => '_spotify_album_artist_name',
						'value' => $band_names,
						'compare' => 'IN',
					);
				} else {
					$args['meta_query'][] = array(
						'key' => '_spotify_album_artist_name',
						'value' => 'none',
						'compare' => '=',
					);
				}
			} else {
				$args['meta_query'][] = array(
					'key' => '_spotify_album_artist_name',
					'value' => 'none',
					'compare' => '=',
				);
			}
		}

		// Executa a query
		$query = new WP_Query( $args );
		?>
		
		<?php 
		if ( $query->have_posts() ) : 
		while ( $query->have_posts() ) : 
		$query->the_post(); 

		// Recuperando os metadados do álbum
	    $artist_name = get_post_meta(get_the_ID(), '_spotify_album_artist_name', true);
	    $release_date = get_post_meta(get_the_ID(), '_spotify_album_release_date', true);
		//$release_date = date('d/m/Y', strtotime(get_post_meta(get_the_ID(), '_spotify_album_release_date', true)));
		
		// Verifica se a data está completa no formato Y-m-d ou apenas o ano Y
		if (strlen($release_date) === 10) { // Formato Y-m-d (10 caracteres)
			// Converte para o formato desejado d/m/Y
			$release_date_formatted = date('d/m/Y', strtotime($release_date));
		} else { // Se não for Y-m-d, assume que é apenas o ano
			$release_date_formatted = $release_date; // Apenas exibe o ano
		}
		
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
			<div class="col-md-2 col-6 mb-3">
				<div class="album">
					<a class="album-cover d-block bg-secondary rounded overflow-hidden" href="<?php echo home_url('/?bandas=') . esc_html($link_artist) . '/#album-id-' . esc_html($album_id); ?>">
						<?php the_post_thumbnail('medium', array('class' => 'w-100 h-auto', 'loading' => 'lazy', 'alt' => get_the_title() )); ?>
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
							<?php echo esc_html($release_date_formatted); ?> • <?php echo esc_html($album_type); ?>
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
							echo '';
						}
						?>
						</small>
					</p>
				</div>
			</div>

		<?php endwhile; 
		
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
		
		wp_reset_postdata(); else : ?>
			<p>Nenhum álbum foi encontrado</p>
		<?php endif; ?>
	</div>
 </div>