	<?php
	if( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

	$rating      = get_post_meta( get_the_ID(), '_movie_imdb_rating', true );
	$duration    = get_post_meta( get_the_ID(), '_movie_duration', true );
	$description = get_post_meta( get_the_ID(), '_movie_description', true );
	$hour        = floor( $duration / 60 );
	$minute      = $duration % 60;
	$duration    = ( $hour > 0 ? $hour . 'h ' : '' ) . ( $minute > 0 ? $minute . 'm' : '' );
	?>

	<div class="imdb-movie-card">
		<a href="<?php the_permalink(); ?>" class="idmb-movie-poster">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'medium' );
			}
			?>
		</a>
		<div class="imdb-movie-information">
		<div class="imdb-movie-info">
			<h3 class="imdb-movie-title">
				<?php the_title(); ?>
			</h3>
			<div class="imdb-stats">
			<?php if ( $rating ) : ?>
				<p class="imdb-movie-rating">⭐ <?php echo esc_html( $rating ); ?></p>
			<?php endif; ?>
			<?php if ( $duration ) : ?>
				<!-- DURATION -->
					<?php
					if ( $duration ) :
						?>
						<p><?php echo esc_html( $duration ); ?></p>
					<?php endif; ?>	
			<?php endif; ?>
			</div>
		</div>
		<div class="imdb-movie-desc">
			<p><?php echo esc_html( wp_trim_words( the_content(), 20, '...' ) ); ?></p>
		</div>
		</div>
	</div>
