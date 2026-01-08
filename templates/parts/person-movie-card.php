	<!-- FILMOGRAPHY -->
	<?php
			$args = array(
			'post_type'      => 'movie',
			'posts_per_page' => -1,
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_movie_director',
					'value'   => $person_id,
					'compare' => '=',
				),
				array(
					'key'     => '_movie_stars',
					'value'   => '"' . $person_id . '"',
					'compare' => 'LIKE',
				),
			),
		);

		$movies = new WP_Query( $args );
	?>
	<section class="person-filmography">
		<h2 class="movie_title">Known For</h2>

		<?php


		if ( $movies->have_posts() ) :
			echo '<div class="movie-grid">';
			while ( $movies->have_posts() ) :
				$movies->the_post();
				?>
				<div class="movie-card">
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'medium' ); ?>
						<h3><?php the_title(); ?></h3>
					</a>
				</div>
				<?php
			endwhile;
			echo '</div>';
			wp_reset_postdata();
		else :
			echo '<p>No movies found.</p>';
		endif;
		?>
	</section>