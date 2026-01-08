<?php
/**
 * Plugin Name: IMDb Movies
 * Description: Registers Movie CPT and related taxonomies (Genre, Release Year).
 * Version: 1.0
 * Author: Vishal Jangid
 * 
 * @package IMDbMovies
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Stop direct access.
}

/**
 * Include required files.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-movie-cpt.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-person-cpt.php';

/**
 * Initialize IMDb Movies Plugin.
 */
function imdb_movies_init() {
	new MovieCPT();
	new IMDB_Movies_Person_CPT();
}
add_action( 'plugins_loaded', 'imdb_movies_init' );

/**
 * Enqueue frontend styles.
 */
function imdb_enqueue_styles() {
	if ( is_admin() ) {
		return;
	}

	$post_type = get_post_type();

	if (
		in_array( $post_type, array( 'movie', 'person' ), true ) ||
		is_post_type_archive( 'movie' )
	) {
		wp_enqueue_style(
			'imdb-movies-style',
			plugin_dir_url( __FILE__ ) . 'assets/css/imdb-movie.css',
			array(),
			filemtime( plugin_dir_path( __FILE__ ) . 'assets/css/imdb-movie.css' )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'imdb_enqueue_styles', 20 );

/**
 * Enqueue Select2 in admin.
 */
add_action(
	'admin_enqueue_scripts',
	function () {
		wp_enqueue_style(
			'select2',
			'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
		);

		wp_enqueue_script(
			'select2',
			'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
			array( 'jquery' ),
			null,
			true
		);
	}
);

/**
 * Initialize Select2 for movie people fields.
 */
add_action(
	'admin_footer',
	function () {
		?>
		<script>
			jQuery( document ).ready( function ( $ ) {
				$( '#movie_stars, #movie_writers' ).select2( {
					width: '100%'
				} );
			} );
		</script>
		<?php
	}
);

/**
 * Override templates for custom post types and taxonomies.
 *
 * @param string $template The path to the template file.
 * @return string
 */
function imdb_override_templates( $template ) {

	if ( is_singular( 'movie' ) ) {
		$custom_template = plugin_dir_path( __FILE__ ) . 'templates/single-movie.php';
		if ( file_exists( $custom_template ) ) {
			return $custom_template;
		}
	}

	if ( is_singular( 'person' ) ) {
		$custom_template = plugin_dir_path( __FILE__ ) . 'templates/single-person.php';
		if ( file_exists( $custom_template ) ) {
			return $custom_template;
		}
	}

	if ( is_tax( 'genre' ) ) {
		$custom_template = plugin_dir_path( __FILE__ ) . 'templates/taxanomy-genre.php';
		if ( file_exists( $custom_template ) ) {
			return $custom_template;
		}
	}

	if ( is_tax( 'release_year' ) ) {
		$custom_template = plugin_dir_path( __FILE__ ) . 'templates/taxanomy-release__year.php';
		if ( file_exists( $custom_template ) ) {
			return $custom_template;
		}
	}

	if ( is_post_type_archive( 'movie' ) ) {
		$custom_template = plugin_dir_path( __FILE__ ) . 'templates/archive-movie.php';
		if ( file_exists( $custom_template ) ) {
			return $custom_template;
		}
	}

	return $template;
}
add_filter( 'template_include', 'imdb_override_templates' );
