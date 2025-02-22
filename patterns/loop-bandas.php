<?php
/**
 * Title: Loop de Bandas
 * Slug: loop-bandas
 * Categories: loop
 * Description: Loop de bandas
 * Inserter: no
 */
?>

<div class="container">

	<div class="d-block d-md-flex justify-content-between my-5">
		<h2 class="mb-0 pe-4">
	        <?php
	        // Verifica se estamos na página de arquivo de bandas
	        if (isset($_GET['post_type']) && $_GET['post_type'] === 'bandas') {
	            echo 'Bandas'; // Título padrão

	            // Verifica se o parâmetro 'genero' está definido
	            if (isset($_GET['genero']) && !empty($_GET['genero'])) {
	                echo ' ' . esc_html(ucfirst($_GET['genero'])); // Exibe o gênero
	            }

	            // Verifica se o parâmetro 'pais' está definido
	            if (isset($_GET['pais']) && !empty($_GET['pais'])) {
	                echo ' ' . esc_html(ucfirst($_GET['pais'])); // Exibe o país
	            }   
	        }
	        ?>
	    </h2>
		<!-- wp:pattern {"slug":"filtros-bandas"} /-->
	</div>

	<div class="table-responsive">
	<table class="table table-banda">
		<thead>
			<tr>
				<th scope="col">Banda</th>
				<th scope="col">Gênero</th>
				<th scope="col">
					<?php
					// Obtém o ID do termo "brasil"
					$brasil_term = get_term_by('slug', 'brasil', 'pais');

					if ($brasil_term) {
					    // Obtém todas as categorias filhas de "brasil"
					    $child_terms = get_term_children($brasil_term->term_id, 'pais');

					    // Verifica se estamos na taxonomia "pais" e se o termo atual é "brasil" ou uma de suas categorias filhas
					    if (is_tax('pais', 'brasil') || in_array(get_queried_object_id(), $child_terms)) {
					        echo 'Estado'; // Mostra "Estado" se o termo for "brasil" ou uma categoria filha
					    } else {
					        echo 'País';   // Caso contrário, mostra "País"
					    }
					}
					?>
				</th>
				<th scope="col">Status</th>
				<th scope="col" style="width:140px; min-width: 120px;">Formada em</th>
				<th scope="col" style="width:140px; min-width: 120px;">Views 
					<button type="button" class="btn btn-sm text-white" data-bs-toggle="popover" data-bs-content="Esse número representa apenas uma única conquista da banda. Não use isso como argumento de que uma banda é melhor do que outra." style="height: 23px; padding-top: 0;">
						<span class="text-white fs-5 icon-icon-aviso"></span>
					</button>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$meta_query = [];
			$tax_query = array('relation' => 'AND');
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    		//$posts_per_page = isset($_GET['posts_per_page']) ? intval($_GET['posts_per_page']) : 6;


			// Filtro por Gênero
			if (isset($_GET['genero']) && !empty($_GET['genero'])) {
			    $tax_query[] = array(
			        'taxonomy' => 'genero',
			        'field'    => 'slug',
			        'terms'    => sanitize_text_field($_GET['genero']),
			    );
			}

			// Filtro por País
			if (isset($_GET['pais']) && !empty($_GET['pais'])) {
			    $tax_query[] = array(
			        'taxonomy' => 'pais',
			        'field'    => 'slug',
			        'terms'    => sanitize_text_field($_GET['pais']),
			    );
			}

			// Filtro por Décadas
			if (isset($_GET['decada']) && !empty($_GET['decada'])) {
			    $decada = intval($_GET['decada']);
			    $meta_query[] = array(
			        'key'     => '_banda_formation_year',
			        'value'   => array($decada, $decada + 9),
			        'compare' => 'BETWEEN',
			        'type'    => 'NUMERIC'
			    );
			}

			// Definindo o valor padrão de ordenação
			$orderby = '_banda_formation_year'; // Por padrão, ordena por ano de formação
			$order = 'DESC'; // Ordem decrescente por padrão

			// Verifica se o usuário selecionou uma opção de ordenação
			if (isset($_GET['ordenar'])) {
			    if ($_GET['ordenar'] === 'formation_year_asc') {
			        $orderby = '_banda_formation_year';
			        $order = 'ASC'; // Ordem crescente
			    } elseif ($_GET['ordenar'] === 'formation_year_desc') {
			        $orderby = '_banda_formation_year';
			        $order = 'DESC'; // Ordem decrescente
			    } elseif ($_GET['ordenar'] === 'youtube_views') {
			        $orderby = 'youtube_views';
			        $meta_query[] = array(
			            'key'     => 'youtube_views',
			            'compare' => 'EXISTS',  // Garante que só serão considerados posts com esse campo
			            'type'    => 'NUMERIC',
			        );
			        $order = 'DESC'; // Ordem decrescente para relevância
			    }
			}

			$args = array(
				'post_type' => 'bandas',
				'paged' => $paged,
				'tax_query' => $tax_query,
				'meta_query' => $meta_query,
				'meta_key'    => $orderby, 
			    'orderby'     => 'meta_value_num', 
			    'order'       => $order, 
			);
			$query = new WP_Query( $args ); 
			?>
			<?php 
			if ( $query->have_posts() ) : 
			while ( $query->have_posts() ) : 
			$query->the_post(); 

			// Recuperando os metadados da banda
			$formation_year = get_post_meta(get_the_ID(), '_banda_formation_year', true);
			$status = get_post_meta(get_the_ID(), '_banda_status', true);
			$youtube_views = get_post_meta(get_the_ID(), 'youtube_views', true);
			?>

				<tr>
					<td>
						<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
						<?php the_title(); ?>
						</a>
					</td>
					<td>
						<?php 
						$genres = get_the_terms(get_the_ID(), 'genero');
						if ($genres && !is_wp_error($genres)) {
							$genre_names = [];
					        foreach ($genres as $genre) {
					            $genre_names[] = esc_html($genre->name);
					        }
					        echo implode(', ', $genre_names);
						}
						?>
					</td>
					<td>
						<?php 
						// Obtém as categorias do post
						$countries = get_the_terms(get_the_ID(), 'pais');

						if ($countries && !is_wp_error($countries)) {
						    // Obtém o ID do termo "brasil"
							if ($brasil_term) {
							    // Obtém todas as categorias filhas de "brasil"
							    $child_terms = get_term_children($brasil_term->term_id, 'pais');

							    // Verifica se estamos na taxonomia "pais" e se o termo atual é "brasil" ou uma de suas categorias filhas
							    if (is_tax('pais', 'brasil') || in_array(get_queried_object_id(), $child_terms)) {
							        foreach ($countries as $country) {
							            // Verifica se a categoria tem um pai (ou seja, é uma categoria filha)
							            if ($country->parent != 0) {
							                // Exibe o nome da categoria filha
							                echo esc_html($country->name);
							                break; // Para o loop após encontrar a primeira categoria filha
							            }
							        }
							    } else {
							        foreach ($countries as $country) {
							            // Verifica se a categoria não tem pai (ou seja, é uma categoria pai)
							            if ($country->parent == 0) {
							                // Exibe o nome da categoria pai
							                echo esc_html($country->name);
							                break; // Para o loop após encontrar a primeira categoria pai
							            }
							        }
							    }
							}
						}
						?>
					</td>
					<td><?php echo esc_html($status); ?></td>
					<td><?php echo esc_html($formation_year); ?></td>
					<td>
						<?php 
						if (!empty($youtube_views)) {
						    echo esc_html(format_followers_count($youtube_views));
						} else {
						    echo " - ";
						}
						?>
					</td>
				</tr>

			<?php 
			endwhile; 

			// Paginação
	        $pagination_args = array(
	            'current' => $paged,
	            'total'   => $query->max_num_pages,
	            'prev_text' => __('Anterior', 'textdomain'),
	            'next_text' => __('Próxima', 'textdomain'),
	        );
	        echo '<tr><td class="paginate border border-0 pt-5" colspan="6">';
	        	echo paginate_links($pagination_args);
	        echo '</td></tr>';

			wp_reset_postdata(); 
			else : ?>
				<tr><td colspan="6">Nenhuma banda foi encontrada</td></tr>
			<?php endif; ?>
		</tbody>
	</table>
	</div>
 </div>