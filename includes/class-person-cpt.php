<?php
/**
 * Person Custom Post Type and Meta Boxes.
 *
 * @package IMDb_Movies
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'IMDB_Movies_Person_CPT' ) ) {
	return;
}

/**
 * Class IMDB_Movies_Person_CPT
 */
class IMDB_Movies_Person_CPT {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_person_cpt' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_person_metaboxes' ) );
		add_action( 'save_post_person', array( $this, 'save_person_meta' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_trailer_metabox' ) );
		add_action( 'save_post', array( $this, 'save_trailer_meta' ) );
	}

	/**
	 * Register Person Custom Post Type.
	 */
	public function register_person_cpt() {
		$args = array(
			'labels'      => array(
				'name'          => __( 'Persons', 'imdb-movies' ),
				'singular_name' => __( 'Person', 'imdb-movies' ),
				'add_new'       => __( 'Add New Person', 'imdb-movies' ),
				'add_new_item'  => __( 'Add New Person', 'imdb-movies' ),
				'edit_item'     => __( 'Edit Person', 'imdb-movies' ),
				'new_item'      => __( 'New Person', 'imdb-movies' ),
				'view_item'     => __( 'View Person', 'imdb-movies' ),
				'search_items'  => __( 'Search Persons', 'imdb-movies' ),
			),
			'public'      => true,
			'has_archive' => true,
			'menu_icon'   => 'dashicons-groups',
			'supports'    => array( 'title', 'editor', 'thumbnail' ),
			'rewrite'     => array( 'slug' => 'persons' ),
		);

		register_post_type( 'person', $args );
	}

	/**
	 * Add Person Details Metabox.
	 */
	public function add_person_metaboxes() {
		add_meta_box(
			'person_details',
			__( 'Person Details', 'imdb-movies' ),
			array( $this, 'render_person_details_metabox' ),
			'person',
			'normal',
			'default'
		);
	}

	/**
	 * Render Person Details Metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_person_details_metabox( $post ) {
		$roles      = get_post_meta( $post->ID, '_person_roles', true );
		$dob        = get_post_meta( $post->ID, '_person_dob', true );
		$birthplace = get_post_meta( $post->ID, '_person_birthplace', true );
		$bio        = get_post_meta( $post->ID, '_person_bio', true );
		wp_nonce_field( 'save_person_details', 'person_details_nonce' );
		?>
		<p>
			<label for="person_roles">
				<strong><?php esc_html_e( 'Roles (comma separated):', 'imdb-movies' ); ?></strong>
			</label><br>
			<input
				type="text"
				id="person_roles"
				name="person_roles"
				value="<?php echo esc_attr( implode( ', ', (array) $roles ) ); ?>"
				style="width:100%;"
			/>
			<small><?php esc_html_e( 'Example: Actor, Director, Producer', 'imdb-movies' ); ?></small>
		</p>

		<p>
			<label for="person_dob">
				<strong><?php esc_html_e( 'Date of Birth:', 'imdb-movies' ); ?></strong>
			</label><br>
			<input
				type="date"
				id="person_dob"
				name="person_dob"
				value="<?php echo esc_attr( $dob ); ?>"
			/>
		</p>

		<p>
			<label for="person_birthplace">
				<strong><?php esc_html_e( 'Place of Birth:', 'imdb-movies' ); ?></strong>
			</label><br>
			<input
				type="text"
				id="person_birthplace"
				name="person_birthplace"
				value="<?php echo esc_attr( $birthplace ); ?>"
				style="width:100%;"
			/>
		</p>

		<p>
			<label for="person_bio">
				<strong><?php esc_html_e( 'Biography:', 'imdb-movies' ); ?></strong>
			</label><br>
			<textarea
				id="person_bio"
				name="person_bio"
				rows="5"
				style="width:100%;"
			><?php echo esc_textarea( $bio ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Save Person Meta.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_person_meta( $post_id ) {
		if (
			! isset( $_POST['person_details_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['person_details_nonce'] ) ), 'save_person_details' )
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['person_roles'] ) ) {
			$roles = array_map(
				'trim',
				explode( ',', sanitize_text_field( wp_unslash( $_POST['person_roles'] ) ) )
			);
			update_post_meta( $post_id, '_person_roles', $roles );
		}

		if ( isset( $_POST['person_dob'] ) ) {
			update_post_meta(
				$post_id,
				'_person_dob',
				sanitize_text_field( wp_unslash( $_POST['person_dob'] ) )
			);
		}

		if ( isset( $_POST['person_birthplace'] ) ) {
			update_post_meta(
				$post_id,
				'_person_birthplace',
				sanitize_text_field( wp_unslash( $_POST['person_birthplace'] ) )
			);
		}

		if ( isset( $_POST['person_bio'] ) ) {
			update_post_meta(
				$post_id,
				'_person_bio',
				sanitize_textarea_field( wp_unslash( $_POST['person_bio'] ) )
			);
		}
	}

	/**
	 * Add Person Trailer Metabox.
	 */
	public function add_trailer_metabox() {
		add_meta_box(
			'person_trailer',
			__( 'Person Trailer URL', 'imdb-movies' ),
			array( $this, 'render_trailer_metabox' ),
			'person',
			'normal',
			'default'
		);
	}

	/**
	 * Render Person Trailer Metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_trailer_metabox( $post ) {
		$trailer_url = get_post_meta( $post->ID, '_person_trailer_url', true );

		wp_nonce_field( 'save_person_trailer', 'person_trailer_nonce' );
		?>
		<p>
			<label for="person_trailer_url">
				<strong><?php esc_html_e( 'Trailer URL:', 'imdb-movies' ); ?></strong>
			</label><br>
			<input
				type="text"
				id="person_trailer_url"
				name="person_trailer_url"
				value="<?php echo esc_url( $trailer_url ); ?>"
				style="width:100%;"
			/>
			<small>
				<?php esc_html_e( 'Enter the full URL of the person trailer (YouTube or Vimeo).', 'imdb-movies' ); ?>
			</small>
		</p>
		<?php
	}

	/**
	 * Save Person Trailer Meta.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_trailer_meta( $post_id ) {
		if (
			! isset( $_POST['person_trailer_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['person_trailer_nonce'] ) ), 'save_person_trailer' )
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['person_trailer_url'] ) ) {
			update_post_meta(
				$post_id,
				'_person_trailer_url',
				esc_url_raw( wp_unslash( $_POST['person_trailer_url'] ) )
			);
		}
	}
}
