<?php
/**
 * Trust Indicators Settings.
 *
 * @since   1.0
 * @package Trust_Indicators
 */

/**
 * Trust Indicators Settings class.
 *
 * @since 1.0
 */
class Trust_Indicators_Settings {
	/**
	 * Parent plugin class.
	 *
	 * @var    Trust_Indicators
	 * @since  1.0
	 */
	protected $plugin = null;

	/**
	 * Settings page slug
	 *
	 * @var String
	 * @since 1.0
	 */
	protected $settings_page = 'trust-indicators';

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
		add_action( 'admin_menu', array( $this, 'create_admin_page' ) );
		add_action( 'admin_init', array( $this, 'create_settings' ) );
	}

	/**
	 * Create admin page
	 *
	 * @since 1.0
	 */
	public function create_admin_page() {
		add_submenu_page(
			'options-general.php',
			esc_html__( 'Trust Indicators', 'trust-indicators' ),
			esc_html__( 'Trust Indicators', 'trust-indicators' ),
			'manage_options',
			$this->settings_page,
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Render admin page html.
	 *
	 * @since 1.0
	 */
	public function render_admin_page() {
		wp_enqueue_script( 'trust_project_admin_js', plugins_url( 'assets/admin.js', dirname( __FILE__ ) ), array( 'jquery' ), '1.2.0', true );
		wp_enqueue_style( 'trust_project_admin_css', plugins_url( 'assets/admin.css', dirname( __FILE__ ) ), array(), '1.2.0', 'screen' );
		?>
		<div class="wrap options-page <?php echo esc_attr( $this->settings_page ); ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<form method="POST" action="options.php">
				<?php settings_fields( 'trust-indicators' ); ?>
				<?php do_settings_sections( 'trust-indicators' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Create settings section.
	 *
	 * @since 1.0
	 */
	public function create_settings() {

		foreach ( $this->get_fields() as $section_key => $section ) {
			add_settings_section(
				$section_key,
				$section['label'],
				array( $this, 'section_callback' ),
				$this->settings_page
			);

			foreach ( $section['fields'] as $field_key => $field ) {
				add_settings_field(
					$field_key,
					$field['label'],
					array( $this, 'field_callback' ),
					$this->settings_page,
					$section_key,
					array_merge( array( 'key' => $field_key ), $field )
				);
				register_setting( 'trust-indicators', $field_key );
			}
		}
	}

	/**
	 * Return all the settings fields for sitewide policies
	 *
	 * @return Array all the fields for sitewide policies.
	 */
	public static function get_fields() {
		return array(
			'sitewide_policies' => array(
				'label' => esc_html__( 'Sitewide Policies', 'trust-indicators' ),
				'fields' => array(
					'sitewide_policies_single_page' => array(
						'label' => esc_html__( 'Are your policies on a single page?', 'trust-indicators' ),
						'description' => esc_html__( 'If your policies are on a single page, put that page\'s URL in the Editorial Standards Page input below. If your policies are on separate pages, enter them separately below.', 'trust-indicators' ),
						'type' => 'checkbox',
						'schema' => '',
						'classes' => 'code',
					),
					'publishing_principles' => array(
						'label' => esc_html__( 'Editorial Standards Page', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'url',
						'schema' => '', // Schema is publishingPrinciples, but it's only outputted manually
						'classes' => 'regular-text',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
					'ethics_policy' => array(
						'label' => esc_html__( 'Ethics Policy', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'url',
						'schema' => 'ethicsPolicy',
						'classes' => 'regular-text not-single',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
					'diversity_statement' => array(
						'label' => esc_html__( 'Diversity Statement', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'url',
						'schema' => 'diversityPolicy',
						'classes' => 'regular-text not-single',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
					'diversity_staffing_report' => array(
						'label' => esc_html__( 'Diversity Staffing Report', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'url',
						'schema' => '', // @TODO
						'classes' => 'regular-text not-single',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
					'corrections_policy' => array(
						'label' => esc_html__( 'Corrections Policy', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'url',
						'schema' => 'correctionsPolicy',
						'classes' => 'regular-text not-single',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
					'ownership_and_funding' => array(
						'label' => esc_html__( 'Ownership Structure, Funding', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'url',
						'schema' => '',
						'classes' => 'regular-text not-single',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
					'founding_date' => array(
						'label' => esc_html__( 'Founding Date', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'date',
						'schema' => 'foundingDate',
						'classes' => 'regular-text',
						'placeholder' => esc_html__( 'Enter a date in the format YYYY-MM-DD', 'trust-indicators' ),
					),
					'masthead' => array(
						'label' => esc_html__( 'Masthead', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'url',
						'schema' => 'masthead',
						'classes' => 'regular-text not-single',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
					'mission_statement' => array(
						'label' => esc_html__( 'Mission Statement with Coverage Priorities', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'url',
						'schema' => 'missionCoveragePrioritiesPolicy',
						'classes' => 'regular-text not-single',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
					'fact_checking_standards' => array(
						'label' => esc_html__( 'Fact-checking Standards', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'url',
						'schema' => 'verificationFactCheckingPolicy',
						'classes' => 'regular-text not-single',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
					'unnamed_sources_policy' => array(
						'label' => esc_html__( 'Unnamed Sources Policy', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'url',
						'schema' => 'unnamedSourcesPolicy',
						'classes' => 'regular-text not-single',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
				),
			),
			'actionable_feedback' => array(
				'label' => esc_html__( 'Actionable Feedback', 'trust-indicators' ),
				'fields' => array(
					'actionable_feedback_policy' => array(
						'label' => esc_html__( 'Policy', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'url',
						'schema' => 'actionableFeedbackPolicy',
						'classes' => 'regular-text',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
					'actionable_feedback_email' => array(
						'label' => esc_html__( 'Email', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'email',
						'schema' => array(
							'parent' => 'contactPoint_feedback',
							'key' => 'email',
						),
						'classes' => 'regular-text',
						'placeholder' => esc_html__( 'Enter an email address', 'trust-indicators' ),
					),
					'actionable_feedback_phone' => array(
						'label' => esc_html__( 'Phone', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'phone',
						'schema' => array(
							'parent' => 'contactPoint_feedback',
							'key' => 'telephone',
						),
						'classes' => 'regular-text',
						'placeholder' => esc_html__( 'Enter a phone number', 'trust-indicators' ),
					),
					'actionable_feedback_url' => array(
						'label' => esc_html__( 'URL', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'url',
						'schema' => array(
							'parent' => 'contactPoint_feedback',
							'key' => 'url',
						),
						'classes' => 'regular-text',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
					'actionable_feedback_social_media_profiles' => array(
						'label' => esc_html__( 'Social Media Profiles', 'trust-indicators' ),
						'description' => esc_html__( 'Enter one URL per line.', 'trust-indicators' ),
						'type' => 'textarea',
						'schema' => array(
							'parent' => 'contactPoint_feedback',
							'key' => 'sameAs',
						),
						'classes' => 'regular-text',
						'placeholder' => esc_html__( 'https://example.com/user/foo', 'trust-indicators' ),
					),
				),
			),
			'bylines' => array(
				'label' => esc_html__( 'Bylines', 'trust-indicators' ),
				'fields' => array(
					'site_uses_bylines' => array(
						'label' => esc_html__( 'This site uses bylines', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'checkbox',
						'schema' => '', // @TODO
						'classes' => 'code',
					),
					'no_byline_policy' => array(
						'label' => esc_html__( 'No-byline Policy Explanation', 'trust-indicators' ),
						'description' => esc_html__( 'Enter a link to a page with information on the no-byline policy. This should include a senior editor bio, link to staff directory, and a brand profile (or link to one).', 'trust-indicators' ),
						'type' => 'url',
						'schema' => '', // @TODO
						'classes' => 'regular-text',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
				),
			),
			'newsroom_contact_info' => array(
				'label' => esc_html__( 'Newsroom Contact Info', 'trust-indicators' ),
				'fields' => array(
					'newsroom_contact_info_email' => array(
						'label' => esc_html__( 'Email', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'email',
						'schema' => array(
							'parent' => 'contactPoint_newsroom',
							'key' => 'email',
						),
						'classes' => 'regular-text',
						'placeholder' => esc_html__( 'Enter an email address', 'trust-indicators' ),
					),
					'newsroom_contact_info_phone' => array(
						'label' => esc_html__( 'Phone', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'phone',
						'schema' => array(
							'parent' => 'contactPoint_newsroom',
							'key' => 'telephone',
						),
						'classes' => 'regular-text',
						'placeholder' => esc_html__( 'Enter a phone number', 'trust-indicators' ),
					),
					'newsroom_contact_info_url' => array(
						'label' => esc_html__( 'URL', 'trust-indicators' ),
						'description' => esc_html__( '', 'trust-indicators' ),
						'type' => 'url',
						'schema' => array(
							'parent' => 'contactPoint_newsroom',
							'key' => 'url',
						),
						'classes' => 'regular-text',
						'placeholder' => esc_html__( 'Enter a URL', 'trust-indicators' ),
					),
					'newsroom_contact_info_social_media_profiles' => array(
						'label' => esc_html__( 'Social Media Profiles', 'trust-indicators' ),
						'description' => esc_html__( 'Enter one URL per line.', 'trust-indicators' ),
						'type' => 'textarea',
						'schema' => array(
							'parent' => 'contactPoint_newsroom',
							'key' => 'sameAs'
						),
						'classes' => 'regular-text',
						'placeholder' => esc_html__( 'https://example.com/user/foo', 'trust-indicators' ),
					),
				),
			),
		);
	}

	public function section_callback( $arg ) {}

	/**
	 * Given a field from $this->get_fields, render its HTML
	 *
	 * @param Array $arg an associative array of input arguments
	 * @since 1.0
	 * @return String HTML
	 */
	public function field_callback( $arg ) {
		// prevent undefined index errors, sometimes
		$arg = array_merge(
			array(
				'type' => '',
				'description' => '',
				'classes' => '',
				'placeholder' => '',
			),
			$arg
		);
		if ( 'checkbox' === $arg['type'] ) {
			echo sprintf(
				'<input type="%1$s" name="%2$s" id="%3$s" type="checkbox" value="1" class="%6$s" %4$s /><br/><span class="description">%5$s</span>',
				esc_attr( $arg['type'] ),
				esc_attr( $arg['key'] ),
				esc_attr( $arg['key'] ),
				checked( 1, get_option( $arg['key'] ), false ),
				esc_html( $arg['description'] ),
				esc_attr( $arg['classes'] )
			);
		} elseif ( 'textarea' === $arg['type'] ) {
			echo sprintf(
				'<textarea name="%2$s" id="%3$s" placeholder="%7$s" class="%6$s" />%4$s</textarea><br/><span class="description">%5$s</span>',
				esc_attr( $arg['type'] ),
				esc_attr( $arg['key'] ),
				esc_attr( $arg['key'] ),
				esc_attr( get_option( $arg['key'] ) ),
				esc_html( $arg['description'] ),
				esc_attr( $arg['classes'] ),
				esc_attr( $arg['placeholder'] )
			);
		} else {
			// @TODO needs validation/sanitization.
			echo sprintf(
				'<input type="%1$s" name="%2$s" id="%3$s" value="%4$s" placeholder="%7$s" class="%6$s" /><br/><span class="description">%5$s</span>',
				esc_attr( $arg['type'] ),
				esc_attr( $arg['key'] ),
				esc_attr( $arg['key'] ),
				esc_attr( get_option( $arg['key'] ) ),
				esc_html( $arg['description'] ),
				esc_attr( $arg['classes'] ),
				esc_attr( $arg['placeholder'] )
			);
		}
	}
}
