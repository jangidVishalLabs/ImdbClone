<?php
if( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); 

$rating_filter = '';

if ( isset( $_GET['rating_filter'] ) ) {
    $rating_filter = floatval( $_GET['rating_filter'] );
} elseif ( isset( $_GET['rating'] ) ) {
    $rating_filter = floatval( $_GET['rating'] );
}

$args = array(
	'post_type'     => 'movie',
	'post_per_page' => 12,
);
if ( $rating_filter ) {
	$args['meta_query'] = array(
		array(
			'key'     => '_movie_imdb_rating',
			'value'   => $rating_filter,
			'compare' => '>=',
			'type'    => 'NUMERIC',
		),
	);
}

$query = new WP_Query( $args );

?>
<div class="imdb-archive rating-archive">
	<h1 class="archive-title">
		<?php
		if ( $rating_filter ) {
			echo 'Movies with IMDb Rating of ' . esc_html( $rating_filter ) . ' or higher';
		} else {
			echo 'All Movies';
		}
		?>
	</h1>
	<!-- Rating Filter Form
	<div class="rating-filter-bar">
		<?php foreach ( range( 1, 10 ) as $rating ) : ?>
			<a href="<?php echo esc_url( add_query_arg( 'rating_filter', $rating ) ); ?>" class="rating-filter-link <?php echo ( $rating_filter == $rating ) ? 'active' : ''; ?>">
				⭐ <?php echo esc_html( $rating ); ?>+
			</a>
		<?php endforeach; ?>
	</div>
		-->
	<div class="imdb-movie-grid">
		<?php if ( $query->have_posts() ) : ?>
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php include plugin_dir_path( __FILE__ ) . 'parts/movie-card.php'; ?>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<p>No movies found.</p>
		<?php endif; ?>
	</div>
</div>