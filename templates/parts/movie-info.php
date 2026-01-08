<?php
	$genres       = get_the_terms( get_the_ID(), 'movie_genre' );
	$director_id  = get_post_meta( get_the_ID(), '_movie_director', true );
	$stars        = get_post_meta( get_the_ID(), '_movie_stars', true );
	$writers      = get_post_meta( get_the_ID(), '_movie_writers', true );
	$platform_key = get_post_meta( get_the_ID(), '_movie_streaming_platform', true );
	$platforms    = MovieCPT::imdb_get_streaming_platforms();

?>

<div class="movie-additional-info">
				<div class="movie-additional-info-inner-left">
				<!-- GENRES -->
				<div class="movie-genres">
					<?php
					if ( $genres && ! is_wp_error( $genres ) ) :
						foreach ( $genres as $genre ) :
							?>
							<a class="genre-chip" href="<?php echo esc_url( get_term_link( $genre ) ); ?>">
								<?php echo esc_html( $genre->name ); ?>
							</a>
							<?php
					endforeach;
				endif;
					?>
				</div>
	
				<!-- DIRECTOR -->
				<div class="movie-info">
					<strong>Director:</strong>
					<?php
					if ( $director_id ) :
						?>
						<a href="<?php echo esc_url( get_permalink( $director_id ) ); ?>">
							<?php echo esc_html( get_the_title( $director_id ) ); ?>
						</a>
					<?php endif; ?>
				</div>
	
				<!-- STARS -->
				<div class="movie-info">
					<strong>Stars:</strong>
					<?php
					if ( is_array( $stars ) ) :
						foreach ( $stars as $star_id ) :
							?>
							<a class="star-href" href="<?php echo esc_url( get_permalink( $star_id ) ); ?>">
								<?php echo esc_html( get_the_title( $star_id ) ); ?>
							</a>
							<?php
					endforeach;
				endif;
					?>
				</div>
	
							<!-- WRITERS -->
				<div class="movie-info">
					<strong>Writers:</strong>
					<?php
					if ( is_array( $writers ) ) :
						foreach ( $writers as $writer_id ) :
							?>
							<a class="star-href" href="<?php echo esc_url( get_permalink( $writer_id ) ); ?>">
							<?php echo esc_html( get_the_title( $writer_id ) ); ?>
							</a>
							<?php
					endforeach;
				endif;
					?>
				</div>
				</div>
				<div class="movie-additional-info-inner-right">
				<!-- LOGO RENDER -->
			
					<?php


					if ( $platform_key && isset( $platforms[ $platform_key ] ) ) :
						$platform = $platforms[ $platform_key ];
						?>
						<?php error_log( print_r( $platform, true ) ); ?>
							<div class="movie-streaming-platform">
								<span>STREAMING ON:</span>
								<img
									src="<?php echo esc_url( $platform['img'] ); ?>"
									alt="<?php echo esc_attr( $platform['name'] ); ?>"
									title="<?php echo esc_attr( $platform['name'] ); ?>"
									loading="lazy"
									width="120"
								/>
							</div>
						<?php endif; ?>

				
			</div>

		</div>
