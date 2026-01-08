<?php
/**
 * Movie Custom Post Type and Meta Boxes.
 *
 * @package IMDb_Movies
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Stop direct access.
}

if ( class_exists( 'MovieCPT' ) ) {
	return;
}

/**
 * Class MovieCPT
 */
class MovieCPT {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'create_custom_post_for_movies' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_movie_people_metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_movie_people_meta' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_rating_metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_movie_rating_meta' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_duration_metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_movie_duration_meta' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_trailer_metabox' ) );
		add_action( 'save_post', array( $this, 'save_trailer_meta' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_streaming_platform_metabox' ) );
		add_action( 'save_post', array( $this, 'save_streaming_platform_meta' ) );
	}

	/**
	 * Register Movie Custom Post Type and Taxonomies.
	 */
	public function create_custom_post_for_movies() {
		$args = array(
			'labels'      => array(
				'name'          => 'Movies',
				'singular_name' => 'Movie',
			),
			'public'      => true,
			'has_archive' => true,
			'menu_icon'   => 'dashicons-video-alt2',
			'supports'    => array( 'title', 'editor', 'thumbnail' ),
			'rewrite'     => array( 'slug' => 'movie' ),
		);

		register_post_type( 'movie', $args );

		$genre_args = array(
			'labels'       => array(
				'name'          => 'Genres',
				'singular_name' => 'Genre',
			),
			'public'       => true,
			'hierarchical' => false,
			'rewrite'      => array( 'slug' => 'genre' ),
			'show_in_rest' => true,
		);

		register_taxonomy( 'genre', 'movie', $genre_args );

		$years_args = array(
			'labels'       => array(
				'name'          => 'Release Years',
				'singular_name' => 'Release Year',
			),
			'public'       => true,
			'hierarchical' => false,
			'rewrite'      => array( 'slug' => 'release-year' ),
			'show_in_rest' => true,
		);

		register_taxonomy( 'release_year', 'movie', $years_args );
	}

	/**
	 * Add Movie People Metabox.
	 */
	public function add_movie_people_metaboxes() {
		add_meta_box(
			'movie-people-box',
			'Movie People',
			array( $this, 'render_movie_people_metabox' ),
			'movie',
			'normal',
			'default'
		);
	}

	/**
	 * Render Movie People Metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_movie_people_metabox( $post ) {
		$director = get_post_meta( $post->ID, '_movie_director', true );
		$stars    = get_post_meta( $post->ID, '_movie_stars', true );
		$writers  = get_post_meta( $post->ID, '_movie_writers', true );

		$people = get_posts(
			array(
				'post_type'      => 'person',
				'posts_per_page' => -1,
			)
		);
		?>
		<p><strong>Director</strong></p>
		<?php wp_nonce_field( 'save_movie_people_meta', 'movie_people_nonce' ); ?>

		<select name="movie_director" id="movie_director">
			<option value="">Select Director</option>
			<?php foreach ( $people as $person ) : ?>
				<option value="<?php echo esc_attr( $person->ID ); ?>" <?php selected( $director, $person->ID ); ?>>
					<?php echo esc_html( get_the_title( $person->ID ) ); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<p><strong>Stars</strong></p>
		<select name="movie_stars[]" id="movie_stars" multiple>
			<?php foreach ( $people as $person ) : ?>
				<option value="<?php echo esc_attr( $person->ID ); ?>" <?php echo in_array( $person->ID, (array) $stars, true ) ? 'selected' : ''; ?>>
					<?php echo esc_html( get_the_title( $person->ID ) ); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<p><strong>Writers</strong></p>
		<select name="movie_writers[]" id="movie_writers" multiple>
			<?php foreach ( $people as $person ) : ?>
				<option value="<?php echo esc_attr( $person->ID ); ?>" <?php echo in_array( $person->ID, (array) $writers, true ) ? 'selected' : ''; ?>>
					<?php echo esc_html( get_the_title( $person->ID ) ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Save Movie People Meta.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_movie_people_meta( $post_id ) {
		if ( ! isset( $_POST['movie_people_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['movie_people_nonce'] ) ), 'save_movie_people_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'movie' !== get_post_type( $post_id ) ) {
			return;
		}

		if ( isset( $_POST['movie_director'] ) ) {
			update_post_meta(
				$post_id,
				'_movie_director',
				sanitize_text_field( wp_unslash( $_POST['movie_director'] ) )
			);
		}

		if ( isset( $_POST['movie_stars'] ) && is_array( $_POST['movie_stars'] ) ) {
			update_post_meta(
				$post_id,
				'_movie_stars',
				array_map( 'intval', wp_unslash( $_POST['movie_stars'] ) )
			);
		} else {
			delete_post_meta( $post_id, '_movie_stars' );
		}

		if ( isset( $_POST['movie_writers'] ) && is_array( $_POST['movie_writers'] ) ) {
			update_post_meta(
				$post_id,
				'_movie_writers',
				array_map( 'intval', wp_unslash( $_POST['movie_writers'] ) )
			);
		} else {
			delete_post_meta( $post_id, '_movie_writers' );
		}
	}

	/**
	 * Add Movie Rating Metabox.
	 */
	public function add_rating_metaboxes() {
		add_meta_box(
			'movie_rating_box',
			'IMDB Rating',
			array( $this, 'render_movie_rating_metabox' ),
			'movie',
			'side',
			'default'
		);
	}

	/**
	 * Render Movie Rating Metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_movie_rating_metabox( $post ) {
		$rating = get_post_meta( $post->ID, '_movie_imdb_rating', true );
		?>
		<p>
			<label for="movie_imdb_rating">IMDB Rating:</label>
			<?php wp_nonce_field( 'save_movie_rating_meta', 'movie_rating_nonce' ); ?>
			<input
				type="number"
				step="0.1"
				min="0"
				max="10"
				name="movie_imdb_rating"
				id="movie_imdb_rating"
				value="<?php echo esc_attr( $rating ); ?>"
			/>
		</p>
		<?php
	}

	/**
	 * Save Movie Rating Meta.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_movie_rating_meta( $post_id ) {
		if (
			! isset( $_POST['movie_rating_nonce'] ) ||
			! wp_verify_nonce(
				sanitize_text_field( wp_unslash( $_POST['movie_rating_nonce'] ) ),
				'save_movie_rating_meta'
			)
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			// Revisions autosave.
			return;
		}

		if ( 'movie' !== get_post_type( $post_id ) ) {
			return;
		}

		if ( isset( $_POST['movie_imdb_rating'] ) ) {
			$rating = floatval( $_POST['movie_imdb_rating'] );

			if ( $rating >= 0 && $rating <= 10 ) {
				update_post_meta( $post_id, '_movie_imdb_rating', $rating );
			} else {
				delete_post_meta( $post_id, '_movie_imdb_rating' );
			}
		}
	}

	/**
	 * Add Movie Trailer Metabox.
	 */
	public function add_trailer_metabox() {
		add_meta_box(
			'movie_trailer_box',
			'Movie Trailer (YouTube)',
			array( $this, 'render_trailer_metabox' ),
			'movie',
			'normal',
			'default'
		);
	}

	/**
	 * Render Movie Trailer Metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_trailer_metabox( $post ) {
		$trailer_url = get_post_meta( $post->ID, '_movie_trailer_url', true );
		wp_nonce_field( 'save_trailer_meta', 'movie_trailer_nonce' );
		?>
		<p>
			<label for="movie_trailer_url">YouTube Trailer URL:</label>
			<input
				type="url"
				name="movie_trailer_url"
				id="movie_trailer_url"
				value="<?php echo esc_attr( $trailer_url ); ?>"
				style="width:100%;"
			/>
		</p>
		<?php
	}

	/**
	 * Save Movie Trailer Meta.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_trailer_meta( $post_id ) {
		if (
			! isset( $_POST['movie_trailer_nonce'] ) ||
			! wp_verify_nonce(
				sanitize_text_field( wp_unslash( $_POST['movie_trailer_nonce'] ) ),
				'save_trailer_meta'
			)
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'movie' !== get_post_type( $post_id ) ) {
			return;
		}

		if ( isset( $_POST['movie_trailer_url'] ) ) {
			update_post_meta(
				$post_id,
				'_movie_trailer_url',
				esc_url_raw( wp_unslash( $_POST['movie_trailer_url'] ) )
			);
		}
	}

	/**
	 * Add Movie Duration Metabox.
	 */
	public function add_duration_metaboxes() {
		add_meta_box(
			'movie_duration_box',
			'Movie Duration',
			array( $this, 'render_movie_duration_metabox' ),
			'movie',
			'side',
			'default'
		);
	}

	/**
	 * Render Movie Duration Metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_movie_duration_metabox( $post ) {
		$duration = get_post_meta( $post->ID, '_movie_duration', true );
		?>
		<p>
			<label for="movie_duration">Duration (in minutes):</label>
			<?php wp_nonce_field( 'save_movie_duration_meta', 'movie_duration_nonce' ); ?>
			<input
				type="number"
				min="0"
				name="movie_duration"
				id="movie_duration"
				value="<?php echo esc_attr( $duration ); ?>"
			/>
		</p>
		<?php
	}

	/**
	 * Save Movie Duration Meta.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_movie_duration_meta( $post_id ) {
		if (
			! isset( $_POST['movie_duration_nonce'] ) ||
			! wp_verify_nonce(
				sanitize_text_field( wp_unslash( $_POST['movie_duration_nonce'] ) ),
				'save_movie_duration_meta'
			)
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'movie' !== get_post_type( $post_id ) ) {
			return;
		}

		if ( isset( $_POST['movie_duration'] ) ) {
			$duration = intval( $_POST['movie_duration'] );

			if ( $duration >= 0 ) {
				update_post_meta( $post_id, '_movie_duration', $duration );
			} else {
				delete_post_meta( $post_id, '_movie_duration' );
			}
		}
	}

	/**
	 * Add Streaming Platform Metabox.
	 */
	public function add_streaming_platform_metabox() {
		add_meta_box(
			'movie_streaming_platform',
			'Streaming Platform',
			array( $this, 'render_streaming_platform_metabox' ),
			'movie',
			'side',
			'default'
		);
	}

	/**
	 * Render Streaming Platform Metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_streaming_platform_metabox( $post ) {
		$platform = get_post_meta( $post->ID, '_movie_streaming_platform', true );
		wp_nonce_field( 'save_streaming_platform_meta', 'movie_streaming_platform_nonce' );

		$platforms = array(
			'netflix'     => 'Netflix',
			'amazonprime' => 'Amazon Prime',
			'jiohotstar'  => 'JioHotstar',
		);
		?>
		<p>
			<label for="movie_streaming_platform">Platform Name:</label>
			<select name="movie_streaming_platform" id="movie_streaming_platform">
				<option value="">Select Platform</option>
				<?php foreach ( $platforms as $key => $name ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $platform, $key ); ?>>
						<?php echo esc_html( $name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Save Streaming Platform Meta.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_streaming_platform_meta( $post_id ) {
		if (
			! isset( $_POST['movie_streaming_platform_nonce'] ) ||
			! wp_verify_nonce(
				sanitize_text_field( wp_unslash( $_POST['movie_streaming_platform_nonce'] ) ),
				'save_streaming_platform_meta'
			)
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'movie' !== get_post_type( $post_id ) ) {
			return;
		}

		if ( isset( $_POST['movie_streaming_platform'] ) ) {
			update_post_meta(
				$post_id,
				'_movie_streaming_platform',
				sanitize_text_field( wp_unslash( $_POST['movie_streaming_platform'] ) )
			);
		}
	}

	/**
	 * Helper: Map Streaming Platform Name & Image.
	 *
	 * @return array
	 */
	public static function imdb_get_streaming_platforms() {
		return array(
			'netflix'     => array(
				'name' => 'Netflix',
				'img'  => 'http://imdb-website.local/wp-content/plugins/imdb-movies/assets/images/netflix.png',
			),
			'amazonprime' => array(
				'name' => 'Amazon Prime',
				'img'  => 'http://imdb-website.local/wp-content/plugins/imdb-movies/assets/images/amazon.png',
			),
			'jiohotstar'  => array(
				'name' => 'JioHotstar',
				'img'  => 'http://imdb-website.local/wp-content/plugins/imdb-movies/assets/images/jiohotstar.png',
			),
		);
	}
}
