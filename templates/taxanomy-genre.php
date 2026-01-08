<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

<div class="imdb-archive">
	<h1>Genre : <?php single_term_title(); ?></h1>

	<div class="imdb-movie-grid">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<?php include plugin_dir_path( __FILE__ ) . 'parts/movie-card.php'; ?>
			<?php endwhile; ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No movies found in this genre.', 'imdb-movies' ); ?></p>
		<?php endif; ?>

	</div>
</div>
