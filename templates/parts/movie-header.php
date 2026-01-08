<?php
	$years       = get_the_terms( get_the_ID(), 'release_year' );
	$duration    = get_post_meta( get_the_ID(), '_movie_duration', true );
	$hour        = floor( $duration / 60 );
	$minute      = $duration % 60;
	$duration    = ( $hour > 0 ? $hour . 'h ' : '' ) . ( $minute > 0 ? $minute . 'm' : '' );
	$imdb_rating = get_post_meta( get_the_ID(), '_movie_imdb_rating', true );

?>
<div class="movie-header">

		<!-- LEFT -->
		<div class="movie-header-left">
			<h1 class="movie-title"><?php the_title(); ?></h1>

			<div class="movie-meta">
				<!-- YEAR -->
				<?php
				if ( $years && ! is_wp_error( $years ) ) :
					foreach ( $years as $year ) :
						?>
						<a class="meta-chip" href="<?php echo esc_url( get_term_link( $year ) ); ?>">
							<?php echo esc_html( $year->name ); ?>
						</a>
						<?php
				endforeach;
		endif;
				?>

				<!-- CERTIFICATE -->
				<span class="meta-chip">PG-13</span>

				<!-- DURATION -->
				<?php
				if ( $duration ) :
					?>
					<span class="meta-chip"><?php echo esc_html( $duration ); ?></span>
				<?php endif; ?>
			</div>
		</div>

		<!-- RIGHT -->
		<div class="movie-header-right">
			<?php
				if ( $imdb_rating ) :
			?>
				<div class="rating-links">
        <a href="<?php echo esc_url( add_query_arg( 'rating', floor( $imdb_rating ), get_post_type_archive_link( 'movie' ) ) ); ?>">
            View movies rated <?php echo floor( $imdb_rating ); ?>+
        </a>
    </div>
				<?php endif; ?>


		</div>

	</div>