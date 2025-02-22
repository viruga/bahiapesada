<?php
/**
 * Title: Loop Single Álbuns
 * Slug: loop-single-albuns
 * Description: Loop Single
 * Inserter: no
 */

$first_artist_name = get_post_meta(get_the_ID(), '_spotify_album_artist_name', true);
$is_highlighted = get_post_meta(get_the_ID(), '_is_highlighted', true);
$is_classic = get_post_meta(get_the_ID(), '_is_classic', true);

$album_type = get_post_meta(get_the_ID(), '_spotify_album_type', true);
// Verifica o valor de $album_type e altera para o formato desejado
if ($album_type === 'album') {
    $album_type = 'Álbum';
} elseif ($album_type === 'single') {
    $album_type = 'Single';
}

$release_date = get_post_meta(get_the_ID(), '_spotify_album_release_date', true);
// Verifica se a data está completa no formato Y-m-d ou apenas o ano Y
if (strlen($release_date) === 10) { // Formato Y-m-d (10 caracteres)
	// Converte para o formato desejado d/m/Y
	$release_date_formatted = date('d/m/Y', strtotime($release_date));
} else { // Se não for Y-m-d, assume que é apenas o ano
	$release_date_formatted = $release_date; // Apenas exibe o ano
}
?>

<style>
	a {
		text-decoration: none;
	}
	.wp-block-site-logo {
		margin-top: -40px;
		transform: rotate(-90deg);
		position: absolute;
		top: 150px;
		left: -50px;
	}
	.album-container {
		width: 1080px; 
		height: 1080px; 
		padding: 60px;
		text-align: center;
	}
	.album-img {
		width:800px; 
		height:auto;
	}
	.sound-wave {
		height: 50px;
		display: flex;
		align-items: center;
		justify-content: center;
		transform: scale(1.67) translateY(-360px);
		position: relative;
		z-index: -1;
	}
	.bar {
	  animation-name: wave-lg;
	  animation-iteration-count: infinite;
	  animation-timing-function: ease-in-out;
	  animation-direction: alternate;
	  background: #888;
	  margin: 0 1.5px;
	  height: 10px;
	  width: 2px;
	}
	.bar:nth-child(-n+7), body .bar:nth-last-child(-n+7) {
	  animation-name: wave-md;
	}
	.bar:nth-child(-n+3), body .bar:nth-last-child(-n+3) {
	  animation-name: wave-sm;
	}
	@keyframes wave-sm {
	  0% {
		opacity: 0.35;
		height: 10px;
	  }
	  100% {
		opacity: 1;
		height: 25px;
	  }
	}
	@keyframes wave-md {
	  0% {
		opacity: 0.35;
		height: 15px;
	  }
	  100% {
		opacity: 1;
		height: 50px;
	  }
	}
	@keyframes wave-lg {
	  0% {
		opacity: 0.35;
		height: 15px;
	  }
	  100% {
		opacity: 1;
		height: 70px;
	  }
	}
</style>

<script>
	window.addEventListener("load", () => {
	  const bar = document.querySelectorAll(".bar");
	  for (let i = 0; i < bar.length; i++) {
		bar.forEach((item, j) => {
		  // Random move
		  item.style.animationDuration = `${Math.random() * (0.7 - 0.2) + 0.2}s`; // Change the numbers for speed / ( max - min ) + min / ex. ( 0.5 - 0.1 ) + 0.1
		});
	  }
	});
</script>

<div class="container border border-dark mt-0 album-container position-relative">
	
 	<div class="position-relative">
		<?php
		if ( $is_classic ) {
			echo '<span class="position-absolute top-0 translate-middle badge rounded-pill bg-primary border border-dark fs-4" style="right: 0;">Clássico</span>';
		}
		// Obtém a data de lançamento do álbum no Spotify
		$release_date = get_post_meta(get_the_ID(), '_spotify_album_release_date', true);
		$six_mes_atras = date('Y-m-d', strtotime('-6 month'));

		// Verifica se a data de lançamento é menor ou igual a um mês atrás
		if ( $release_date >= $six_mes_atras ) {
			echo '<span class="position-absolute top-0 translate-middle badge rounded-pill bg-success border border-dark fs-4" style="right: -36px;">Lançamento</span>';
		}
		?>
	</div>
	
	<img class="album-img rounded" src="<?php echo the_post_thumbnail_url(); ?>">
	
	<?php $limite_palavras = 10; 
	// Obtém o título e limita o número de palavras
	$titulo = wp_trim_words(get_the_title(), $limite_palavras, '...'); ?>
	
	<h1 class="mt-4 h1"><?php echo esc_html($titulo); ?></h1>
	
	<p class="mb-1 fs-2"><a href="#"><?php echo esc_attr($first_artist_name); ?></a></p>
	
	<p class="mb-1 fs-3">
		<?php echo esc_attr($album_type) . ' de ' . esc_attr($release_date_formatted); ?>
	
		<?php
		// Obtendo o nome da banda do álbum
		$band_posts = get_posts(array(
			'post_type' => 'bandas',
			'meta_query' => array(
				array(
					'key' => '_spotify_artist_name',
					'value' => $first_artist_name,
					'compare' => '='
				)
			)
		));
		
		echo '&nbsp;  •  &nbsp;';

		if (!empty($band_posts)) {
			$band_post = $band_posts[0];

			// Exibindo os gêneros
			$genres = wp_get_post_terms($band_post->ID, 'genero', array('fields' => 'names'));
			if (!empty($genres)) {
				echo esc_html(implode(', ', $genres)) . ' Metal';
			} else {
				echo 'Nenhum gênero encontrado.<br>';
			}

			// Exibindo os países
			$countries = wp_get_post_terms($band_post->ID, 'pais', array('fields' => 'names'));
			if (!empty($countries)) {
				echo '&nbsp;  •  &nbsp;' . esc_html(implode(', ', $countries)) . '<br>';
			} else {
				echo 'Nenhum país encontrado.<br>';
			}
		} else {
			echo '';
		}
		?>
	</p>
	<!-- wp:site-logo {"width":200,"isLink":false} /-->
	
	<div class='sound-wave'>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	  <div class='bar'></div>
	</div>
	
 </div>