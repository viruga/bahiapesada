<?php
/**
 * Title: Loop de Banners
 * Slug: loop-banners
 * Categories: loop
 * Description: Loop de Últimos Banners
 * Inserter: no
 */
?>

<?php

$args = array(
    'post_type' => 'banners',
    'posts_per_page' => 2 // Ou um número específico para limitar a exibição
);

$query = new WP_Query($args);
echo '<div class="container my-5 d-none">';
if ($query->have_posts()) {
    echo '<div class="row gx-md-4 gx-3">';
    while ($query->have_posts()) {
        $query->the_post();
		
        ?>
        <div class="col-6">
			<?php 
			if (has_post_thumbnail()) {
				the_post_thumbnail('medium', array('class' => 'w-100 h-auto rounded mb-3')); 
			} elseif () {
				the_title();
				the_content();
			}
			?>
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