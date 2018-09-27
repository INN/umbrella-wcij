<?php
/**
 * Trust Indicators Article Settings.
 *
 * @since   1.0
 * @package Trust_Indicators
 */

/**
 * Trust Indicators Article Settings class.
 *
 * @since 1.0
 */
class Trust_Indicators_Article_Settings {
	/**
	 * Parent plugin class.
	 *
	 * @var    Trust_Indicators
	 * @since  1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  1.0
	 *
	 * @param  Trust_Indicators $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'create_taxonomies' ), 10, 0 );
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'metabox_admin_scripts' ) );
	}

	/**
	 * Create taxonomies.
	 *
	 * @since  1.0
	 */
	public function create_taxonomies() {
		$labels = array(
			'name'              => esc_html__( 'Type of Work', 'trust-indicators' ),
			'singular_name'     => esc_html__( 'Type of Work', 'trust-indicators' ),
			'search_items'      => esc_html__( 'Search Type of Works', 'trust-indicators' ),
			'all_items'         => esc_html__( 'All Types', 'trust-indicators' ),
			'parent_item'       => esc_html__( 'Parent Type', 'trust-indicators' ),
			'parent_item_colon' => esc_html__( 'Parent Type:', 'trust-indicators' ),
			'edit_item'         => esc_html__( 'Edit Type', 'trust-indicators' ),
			'update_item'       => esc_html__( 'Update Type', 'trust-indicators' ),
			'add_new_item'      => esc_html__( 'Add New Type of Work', 'trust-indicators' ),
			'new_item_name'     => esc_html__( 'New Type of Work Name', 'trust-indicators' ),
			'menu_name'         => esc_html__( 'Type of Work', 'trust-indicators' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'type-of-work' ),
		);

		register_taxonomy( 'type-of-work', array( 'post' ), $args );
	}

	/**
	 * Add custom metaboxes.
	 *
	 * @since 1.0
	 */
	public function register_meta_boxes() {
		foreach ( $this->get_fields() as $meta_box_key => $meta_box ) {
			add_meta_box(
				'trust_indicators_' . $meta_box_key,
				$meta_box['label'],
				array( $this, 'render_metabox' ),
				'post',
				'advanced',
				'default',
				array_merge( array( 'key' => $meta_box_key ), $meta_box )
			);
		}
	}

	/**
	 * Setup fields.
	 *
	 * @since 1.0
	 */
	public static function get_fields() {
		return array(
			'corrections' => array(
				'label'	=> esc_html__( 'Corrections', 'trust-indicators' ),
				'description' => esc_html__( 'Enter a list of corrections to be displayed along with the post.', 'trust-indicators' ),
				'fields' => array(
					'corrections' => array(
						'label' => esc_html__( 'Corrections', 'trust-indicators' ),
						'description' => esc_html__( 'Enter a list of corrections to be displayed along with the post.', 'trust-indicators' ),
						'type' => 'textarea',
						'schema' => 'correction',
					),
				),
			),
			'sourcing_methodology' => array(
				'label' => esc_html__( 'Sourcing & Methodology Statement', 'trust-indicators' ),
				'description' => esc_html__( 'Information for the reader about how the idea for the story was developed.', 'trust-indicators' ),
				'fields' => array(
					'sourcing_methodology' => array(
						'label' => esc_html__( 'Sourcing & Methodology Statement', 'trust-indicators' ),
						'description' => esc_html__( 'Information for the reader about how the idea for the story was developed. The suggested length for this field is 1200 characters, including spaces.', 'trust-indicators' ),
						'type' => 'textarea',
						'schema' => 'backstory',
					),
				),
			),
			'citations' => array(
				'label' => esc_html__( 'Citations & References', 'trust-indicators' ),
				'description' => esc_html__( 'Enter one URL per line. Provide URLs to internal or external documents, related stories, and sources gathered by the newsroom.', 'trust-indicators' ),
				'fields' => array(
					'citations' => array(
						'label' => esc_html__( 'Citations & References', 'trust-indicators' ),
						'description' => wp_kses_post( __( 'Enter one reference per line. Provide URLs to internal or external documents, related stories, and sources gathered by the newsroom. When applicable, include the name of the publication and the date. Sources can be formatted using HTML or left as plain text, but must include URLs.<br/>Examples:<br/>“Touched By a Michael Landon: America\'s Jewish Angel”,  Religion Dispatches, Feb. 27, 2017. &lt;a href="http://religiondispatches.org/touched-by-a-michael-landon-americas-jewish-angel/"&gt;Source link&lt;/a&gt;<br/>"Developing Indicators of Trust", The Trust Project, March 27, 2018. https://thetrustproject.org/developing-indicators-of-trust/', 'trust-indicators' ) ),
						'type' => 'textarea',
						'schema' => 'citation',
					),
				),
			),
			'dateline' => array(
				'label' => esc_html__( 'Dateline', 'trust-indicators' ),
				'description' => esc_html__( '', 'trust-indicators' ),
				'fields' => array(
					'dateline' => array(
						'label' => esc_html__( 'Dateline', 'trust-indicators' ),
						'description' => esc_html__( 'The location where the story was written, not the location of the story.', 'trust-indicators' ),
						'type' => 'text',
						'schema' => 'locationCreated',
					),
				),
			)
		);
	}

	/**
	 * Render a custom metabox
	 *
	 * @since 1.0
	 * @param obj $post Post object.
	 * @param obj $args Arguments object.
	 */
	public function render_metabox( $post, $args ) {

		wp_nonce_field( $args['args']['key'], $args['args']['key'] . '_nonce' );

		foreach ( $args['args']['fields'] as $key => $field ) {
			echo sprintf(
				'<label class="screen-reader-text" for="%s">%s</label>',
				esc_attr( $key ),
				esc_html( $field['label'] )
			);

			if ( 'textarea' === $field['type'] ) {
				wp_editor(
					get_post_meta( $post->ID, $key, true ),
					$key,
					array(
						'wpautop'       =>      true,
						'media_buttons' =>      false,
						'textarea_name' =>      $key,
						'textarea_rows' =>      10,
						'teeny'         =>      true
					)
				);
			} else {
				echo sprintf(
					'<input type ="%1$s" name="%2$s" id="%2$s" value="%3$s"/>',
					esc_attr( $field['type'] ),
					esc_attr( $key ),
					esc_attr( get_post_meta( $post->ID, $key, true ) )
				);
			}

			echo sprintf( '<p>%s</p>', wp_kses_post( $field['description'] ) );
		}
	}

	/**
	 * Save data entered in custom meta boxes.
	 *
	 * @since 1.0
	 * @param int $post_id ID of the current post.
	 */
	public function save_meta_boxes( $post_id ) {

		// Check if there is data to save (if not, we could be loading a new post form page).
		if ( ! isset( $_POST['post_type'] ) ) {
			return;
		}

		// Do not proceed if this is not a post.
		//@codingStandardsIgnoreLine
		if ( 'post' !== $_POST['post_type'] ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		foreach ( $this->get_fields() as $meta_box_key => $meta_box ) {

			// Add nonce for security and authentication.
			//@codingStandardsIgnoreLine
			$nonce          = isset( $_POST[ $meta_box_key . '_nonce' ] ) ? $_POST[ $meta_box_key . '_nonce' ] : false;
			$nonce_action   = $meta_box_key;

			// Check if nonce is set.
			if ( ! $nonce ) {
				continue;
			}

			// Check if nonce is valid.
			if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
				continue;
			}

			foreach ( $meta_box['fields'] as $field_key => $field ) {
				// $field is unused here, on purpose.
				// $meta_box is not the values from $_POST, but from $this->get_fields,
				// and we use the array key $field_key to pull fields by key from $_POST below,
				// sanitizing that with wp_kses_post.
				if ( isset( $_POST[ $field_key ] ) ) {
					$sanitized_field = wp_kses_post( $_POST[ $field_key ] );
					update_post_meta( $post_id, $field_key, $sanitized_field );
				}
			}
		}
	}

	/**
	 * Enqueue admin scripts for custom metabox
	 *
	 * @since 1.0
	 * @param str $hook_suffix The current admin page.
	 */
	public function metabox_admin_scripts( $hook_suffix ) {
		if ( 'post.php' === $hook_suffix ) {
			wp_enqueue_style( 'trust_project_admin_css', plugins_url( 'assets/admin.css', dirname( __FILE__ ) ) );
		}
	}
}
