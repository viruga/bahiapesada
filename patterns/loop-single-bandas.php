<?php
/**
 * Title: Loop Single Bandas
 * Slug: loop-single-bandas
 * Description: Loop Single
 * Inserter: no
 */

//$release_date = get_post_meta(get_the_ID(), '_spotify_album_release_date', true);
$release_date = date('d/m/Y', strtotime(get_post_meta(get_the_ID(), '_spotify_album_release_date', true)));
$album_type = get_post_meta(get_the_ID(), '_spotify_album_type', true);
$formation_year = get_post_meta(get_the_ID(), '_banda_formation_year', true);
$status = get_post_meta(get_the_ID(), '_banda_status', true);
$spotify_url = get_post_meta(get_the_ID(), '_spotify_url', true);
$youtube_channel_id = get_post_meta(get_the_ID(), 'youtube_channel_id', true);
$youtube_views = get_post_meta(get_the_ID(), 'youtube_views', true);
$current_artist_name = get_post_meta(get_the_ID(), '_spotify_artist_name', true);
?>
<style>
	.track-name {
	    display: inline-block;
	    width: 300px;
	}
</style>
<div class="container">
	<div class="row my-md-5">
		<div class="col">
			<h1 class="mb-4"><?php the_title(); ?></h1>

			<?php 
			if( '' !== get_post()->post_content ) {
				echo '<div class="content">';
				the_content();
				echo '</div>';
			}
			?>

			<div class="info">
				<?php 
				// Exibir categorias da taxonomia 'pais'
				$countries = get_the_terms(get_the_ID(), 'pais');
				if ($countries && !is_wp_error($countries)) {
					echo '<div class="band-countries">';
					echo '<strong>País:</strong> ';
					$country_names = [];
					foreach ($countries as $country) {
						$country_names[] = esc_html($country->name);
					}
					echo implode(', ', $country_names);
					echo '</div>';
				}
				// Exibir categorias da taxonomia 'genero'
				$genres = get_the_terms(get_the_ID(), 'genero');
				if ($genres && !is_wp_error($genres)) {
					echo '<div class="band-genres">';
					echo '<strong>Gêneros:</strong> ';
					$genre_names = [];
					foreach ($genres as $genre) {
						$genre_names[] = esc_html($genre->name);
					}
					echo implode(', ', $genre_names);
					echo '</div>';
				}
				echo '<div><strong>Formada em:</strong> ' . $formation_year . '</div>';
				echo '<div><strong>Status:</strong> ' . esc_html($status) . '</div>';
				echo '<div><strong>Views:</strong> ' . esc_html(format_followers_count($youtube_views)) . '</div>';
				echo '<div><strong>Ouça no:</strong> <a target="blank" href="' . $spotify_url . '">Spotify</a>, <a target="blank" href="https://www.youtube.com/channel/' . $youtube_channel_id . '">Youtube</a></div>';
				?>
			</div>

			<div class="albums mt-4">
				<?php
				// Buscar álbuns com o mesmo nome de artista
				$args = array(
					'post_type' => 'albuns', 
					'meta_query' => array(
						array(
							'key' => '_spotify_album_artist_name',
							'value' => $current_artist_name,
							'compare' => '='
						)
					),
					'meta_key' => '_spotify_album_release_date', 
					'orderby' => 'meta_value', 
					'order' => 'DESC',
				);

				$album_query = new WP_Query($args);

				if ($album_query->have_posts()) {
					echo '<h2>Álbuns</h2>';
					echo '<div class="albums-list">';
					while ($album_query->have_posts()) {
						$album_query->the_post();

						// Recupera os meta dados do álbum            
						$album_id = get_post_meta(get_the_ID(), '_spotify_album_id', true);
						$album_url = get_post_meta(get_the_ID(), '_spotify_album_url', true);
						$album_name = get_post_meta(get_the_ID(), '_spotify_album_name', true);
						$album_release_date = get_post_meta(get_the_ID(), '_spotify_album_release_date', true);
						//$album_release_date = date('d/m/Y', strtotime(get_post_meta(get_the_ID(), '_spotify_album_release_date', true)));
						// Verifica se a data está completa no formato Y-m-d ou apenas o ano Y
						if (strlen($album_release_date) === 10) { // Formato Y-m-d (10 caracteres)
							// Converte para o formato desejado d/m/Y
							$release_date_formatted = date('d/m/Y', strtotime($album_release_date));
						} else { // Se não for Y-m-d, assume que é apenas o ano
							$release_date_formatted = $album_release_date; // Apenas exibe o ano
						}
						$album_type = get_post_meta(get_the_ID(), '_spotify_album_type', true);
						// Verifica o valor de $album_type e altera para o formato desejado
						if ($album_type === 'album') {
							$album_type = 'Álbum';
						} elseif ($album_type === 'single') {
							$album_type = 'Single';
						}
						$album_image = get_post_meta(get_the_ID(), '_spotify_album_image', true);
						$is_highlighted = get_post_meta(get_the_ID(), '_is_highlighted', true);
						$is_classic = get_post_meta(get_the_ID(), '_is_classic', true);

						echo '<div id="album-id-'. esc_html($album_id) .'" class="row mt-4">';
							echo '<div class="col-md-3 position-relative">';
								echo '<span class="position-absolute top-0 end-0" style="margin-right: -30px;">';
								if ( $is_highlighted ) {
									echo '<span class="translate-middle badge rounded-pill bg-danger border border-dark">Destaque</span>';
								}
								if ( $is_classic ) {
									echo '<span class="translate-middle badge rounded-pill bg-primary border border-dark">Clássico</span>';
								}
								echo '</span>';
								echo '<img class="w-100" src="' . esc_url($album_image) . '" alt="' . esc_attr($album_name) . '">';
							echo '</div>';
							echo '<div class="col-md-9">';
								echo '<h3>' . esc_html($album_name) .  '<a class="ms-3 fs-4 text-white text-decoration-none" title="Spotify" href="' . $album_url . '" target="blank"><span class="icon-icon-spotify"></span></a>' . '</h3>';
								echo '<div>' . esc_html($release_date_formatted);
								echo ' • ' . esc_html($album_type) . '</div>';

								echo '<div class="mt-4">';
								$track_data = get_post_meta(get_the_ID(), '_spotify_album_tracks', true);

								if (!empty($track_data) && is_array($track_data)) {  // Verifica se há dados e se é um array
									echo '<div>
									<strong class="track-name">Faixas</strong>
									<strong class="track-time" style="margin-left:26px;">Duração</strong></div>';
									echo '<ol class="track-list">';
										foreach ($track_data as $track) {
											// Verifica se os dados da faixa estão definidos corretamente
											if (isset($track['name']) && isset($track['duration_ms'])) {
												echo '<li class="border-bottom border-secondary">';
												echo '<span class="track-name">' . esc_html($track['name']) . '</span>';
												echo '<span class="track-time">' . esc_html(format_duration($track['duration_ms'])) . '</span>';
												echo '</li>';
											} else {
												echo '<li>Dados da faixa estão incompletos.</li>';
											}
										}
									echo '</ol>';
								} else {
									echo '<p>Nenhuma faixa disponível para este álbum.</p>';
								}
								echo '</div>';
							echo '</div>';
						echo '</div>';

					}
					echo '</div>';
					wp_reset_postdata(); // Restaura a query original
				} else {
					echo 'Nenhum álbum cadastrado... ainda :)';
				}
				?>
			</div>
		</div>

		<?php
		$banda_id = get_the_ID();
		$hoje = date('Y-m-d');
		$eventos = new WP_Query([
			'post_type' => 'eventos',
			'posts_per_page' => -1,
			'post_status' => 'any',
			'meta_key' => '_evento_dia',
			'orderby'  => 'meta_value',
			'order'    => 'DESC',
			'meta_query' => [
				[
					'key'     => '_bandas_relacionadas',
					'value'   => $banda_id,
					'compare' => 'LIKE'
				]
			]
		]);

		if ($eventos->have_posts()) {
			echo '<div class="col-md-2 offset-1 ps-4 border-secondary border-start">';
				echo '<div class="sidebar">';
				echo '<h4>Eventos na Bahia:</h4>';
				while ($eventos->have_posts()) {
					$eventos->the_post();
					// Pega a data do evento
					$data_evento = get_post_meta(get_the_ID(), '_evento_dia', true);
					$classe_antigo = ($data_evento < $hoje) ? 'evento-antigo' : '';
					$data_formatada = date('d/m/Y', strtotime($data_evento));
					// Apresenta
					echo '<div class="mb-3 ' . esc_attr($classe_antigo) . '">';
					echo '<a href="' . get_permalink() . '" class="evento-thumb">';
					the_post_thumbnail('thumbnail', ['class' => 'w-100 h-auto']);
					echo '</a>';
					echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
					echo '<br><span>' . $data_formatada . '</span>: ';
					echo '</div>';
				}
				echo '</div>';
			echo '</div>';
			wp_reset_postdata();
		}

		if (!empty($dia)) {
			echo esc_html($dia);
		}
		?>
	</div>
 </div>