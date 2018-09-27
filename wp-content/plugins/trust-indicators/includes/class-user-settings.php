<?php
/**
 * Trust Indicators User Settings.
 *
 * @since   1.0
 * @package Trust_Indicators
 */

/**
 * Trust Indicators User Settings class.
 *
 * @since 1.0
 */
class Trust_Indicators_User_Settings {
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
		add_action( 'show_user_profile', array( $this, 'extra_user_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'extra_user_profile_fields' ) );
		add_action( 'personal_options_update', array( $this, 'save_extra_user_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_extra_user_profile_fields' ) );

		/**
		* @TODO Languages Spoken field (and possibly others) should be moved into a user taxonomy
		* once user taxonomies are fully supported in core.
		* See https://core.trac.wordpress.org/ticket/31383
		*/
	}

	/**
	 * Add additional fields to the user profile.
	 *
	 * @param obj $user User object.
	 * @since  1.0
	 */
	public function extra_user_profile_fields( $user ) {
		echo '<h3>' . esc_html__( 'Trust Indicators', 'trust-indicators' ) . '</h3>';
		echo '<table class="form-table">';
		foreach ( $this->get_fields() as $field_key => $field ) {
			echo '<tr>';
				echo '<th><label for="' . esc_attr( $field_key ). '">' . esc_html( $field['label'] ) . '</label></th>';
				echo '<td>';
					if ( 'textarea' === $field['type'] ) {
						printf(
							'<textarea name="%1$s" id="%1$s" class="regular-text">%2$s</textarea><br /><span class="description">%3$s</span>',
							esc_attr( $field_key ),
							esc_html( get_the_author_meta( $field_key, $user->ID ) ),
							wp_kses_post( $field['description'] )
						);
					} else {
						printf(
							'<input type="%1$s" name="%2$s" id="%2$s" value="%3$s" class="regular-text" /><br /><span class="description">%4$s</span>',
							esc_attr( $field['type'] ),
							esc_attr( $field_key ),
							esc_html( get_the_author_meta( $field_key, $user->ID ) ),
							wp_kses_post( $field['description'] )
						);
					}
				echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}

	/**
	 * Save additional field data submitted on user profile screen.
	 *
	 * @param int $user_id ID of the user to save data for.
	 * @since 1.0
	 */
	public function save_extra_user_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		foreach ( $this->get_fields() as $field_key => $field ) {
			if ( 'textarea' === $field['type'] ) {
				// this sanitization is okay, per https://github.com/INN/trust-indicators/pull/50#issuecomment-395926120
				//@codingStandardsIgnoreLine
				$sanitized_field = sanitize_textarea_field( $_POST[ $field_key ] );
			} else {
				//@codingStandardsIgnoreLine
				$sanitized_field = sanitize_text_field( $_POST[ $field_key ] );
			}

			if ( function_exists( 'get_user_attribute' ) ) {
				update_user_attribute( $user_id, 'trust_indicators_' . $field_key, $sanitized_field );
			} else {
				//@codingStandardsIgnoreLine
				update_user_meta( $user_id, $field_key, $sanitized_field );
			}
		}
	}

	/**
	 * Store the fields used in this user settings
	 *
	 * @since 1.0
	 * @return Array An associative array of the settings in the form $field_key => (array) $field, where $field is an array of string label, description, and type, and string|array schema.
	 */
	public static function get_fields() {
		return array(
			'location' => array(
				'label' => esc_html__( 'Location', 'trust-indicators' ),
				'description' => esc_html__( '', 'trust-indicators' ),
				'type' => 'text',
				'schema' => array(
					'parent' => 'workLocation',
					'key' => 'name'
				)
			),
			'languages_spoken' => array(
				'label' => esc_html__( 'Languages Spoken', 'trust-indicators' ),
				'description' => esc_html__( 'Enter a comma-separated list of languages you are fluent in.', 'trust-indicators' ),
				'type' => 'text',
				'schema' => array(
					'parent' => 'knowsLanguage',
					'key' => 'name',
				)
			),
			'areas_of_expertise' => array(
				'label' => esc_html__( 'Areas of Expertise', 'trust-indicators' ),
				'description' => wp_kses_post( __( 'Enter a list of topics and demographics where you are considered a subject matter expert. Use only one expertise item per line. For example, if your topics are Energy, Politics, and Immigrants, you would put them on separate lines:<br/>Energy<br/>Politics<br/>Immigrants', 'trust-indicators' ) ),
				'type' => 'textarea',
				'schema' => array(
					'parent' => 'knowsAbout',
					'key' => 'name',
				),
			),
			'location_expertise' => array(
				'label' => esc_html__( 'Location Expertise', 'trust-indicators' ),
				'description' => wp_kses_post( __( 'Enter one geographic location per line in this format: City, State/Province, Country. For example:<br/>Chicago, Illinois, USA<br/>London, United Kingdom', 'trust-indicators' ) ),
				'type' => 'textarea',
				'schema' => array(
					'parent' => 'knowsAbout',
					'key' => 'name',
				),
			),
			'title' => array(
				'label' => esc_html__( 'Official Title', 'trust-indicators' ),
				'description' => esc_html__( '(Affiliation with Publisher)', 'trust-indicators' ),
				'type' => 'text',
				'schema' => 'jobTitle',
			),
			'public_contact_info_tel' => array(
				'label' => esc_html__( 'Phone Number', 'trust-indicators' ),
				'description' => esc_html__( 'Public-facing and must include an international country code prefix.', 'trust-indicators' ),
				'type' => 'tel',
				'schema' => array(
					'parent' => 'contactPoint',
					'key' => 'telephone',
				),
			),
			'public_contact_info_email' => array(
				'label' => esc_html__( 'Email Address', 'trust-indicators' ),
				'description' => esc_html__( '(Public-facing)', 'trust-indicators' ),
				'type' => 'email',
				'schema' => array(
					'parent' => 'contactPoint',
					'key' => 'email',
				),
			),
			'twitter' => array(
				'label' => esc_html__( 'Twitter Profile', 'trust-indicators' ),
				'description' => '',
				'type' => 'url',
				'schema' => 'sameAs',
			),
			'linkedin' => array(
				'label' => esc_html__( 'Linkedin Profile', 'trust-indicators' ),
				'description' => '',
				'type' => 'url',
				'schema' => 'sameAs',
			),
		);
	}
}
