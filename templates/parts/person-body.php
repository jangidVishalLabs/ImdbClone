<?php
			$trailer_url = get_post_meta( get_the_ID(), '_person_trailer_url', true );
						if ( $trailer_url ) {

				if ( strpos( $trailer_url, 'youtube.com/watch' ) !== false ) {
					parse_str( wp_parse_url( $trailer_url, PHP_URL_QUERY ), $vars );
					if ( ! empty( $vars['v'] ) ) {
						$trailer_url = 'https://www.youtube.com/embed/' . esc_attr( $vars['v'] );
					}
				}

				// youtu.be short URLs
				if ( strpos( $trailer_url, 'youtu.be/' ) !== false ) {
					$video_id    = basename( wp_parse_url( $trailer_url, PHP_URL_PATH ) );
					$trailer_url = 'https://www.youtube.com/embed/' . esc_attr( $video_id );
				}
			}
?>
		<!-- POSTER -->
		<div class="movie-center">
		<div class="movie-poster">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'large' );
			}
			?>
		</div>
		<div class="movie-trailer">
		
	<iframe
		width="400"
		height="315"
		src="<?php echo esc_url( $trailer_url ); ?>"
		title       ="YouTube video player"
		frameborder ="0"
		allow       ="autoplay; encrypted-media; gyroscope; picture-in-picture"
		loading     ="lazy"
		allowfullscreen>
	</iframe>


		</div>

		</div>