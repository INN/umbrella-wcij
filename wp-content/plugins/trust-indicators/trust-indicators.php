<?php
/**
 * Plugin Name:     Trust Indicators
 * Plugin URI:      https://labs.inn.org
 * Description:     An easy way to incorporate The Trust Project's Trust Indicators throughout your site.
 * Author:          INN Labs
 * Author URI:      https://labs.inn.org
 * Text Domain:     trust_indicators
 * Domain Path:     /languages
 * Version:         1.2.0
 *
 * @package         Trust_Indicators
 */

$includes = array(
	// Settings
	'/includes/class-settings.php',
	'/includes/class-article-settings.php',
	'/includes/class-user-settings.php',
	// Schema Output
	'/includes/class-schema-output.php',
	// Shortcodes
	'/includes/class-shortcodes.php',
	// Widgets
	'/includes/class-sitewide-widget.php',
	'/includes/class-author-widget.php',
	'/includes/class-article-widget.php',
	'/includes/class-trustproject-widget.php',
);
foreach ( $includes as $include ) {
	if ( 0 === validate_file( dirname( __FILE__ ) . $include ) ) {
		require_once( dirname( __FILE__ ) . $include );
	}
}

/**
 * Main initiation class.
 *
 * @since  1.0
 */
final class Trust_Indicators {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  1.0
	 */
	const VERSION = '1.2.0';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $basename = '';

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    Trust_Indicators
	 * @since  1.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of Trust_Indicators_Settings
	 *
	 * @since 1.0
	 * @var Trust_Indicators_Settings
	 */
	protected $settings;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   1.0
	 * @return  Trust_Indicators A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since  1.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Add hooks and filters.
	 * Priority needs to be
	 * < 10 for CPT_Core,
	 * < 5 for Taxonomy_Core,
	 * and 0 for Widgets because widgets_init runs at init priority 1.
	 *
	 * @since  1.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Init hooks
	 *
	 * @since  1.0
	 */
	public function init() {

		// Load translated strings for plugin.
		load_plugin_textdomain( 'trust-indicators', false, dirname( $this->basename ) . '/languages/' );

		$this->settings = new Trust_Indicators_Settings( $this );
		$this->article_settings = new Trust_Indicators_Article_Settings( $this );
		$this->user_settings = new Trust_Indicators_User_Settings( $this );
		$this->schema_output = new Trust_Indicators_Schema_Output( $this );
		$this->shortcodes = new Trust_Mark_Shortcodes( $this );

		add_action( 'admin_init', array( $this, 'update' ) );
		add_action( 'trust_indicators_init', array( $this, 'update' ) );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'trust-indicators-logo', 'trust_project_mark' );
	}

	/**
	 * Plugin updates function
	 */
	public function update() {
		if ( $this->need_updates() ) {
			$this->default_single_page_editorial_policies();
			$this->create_type_of_work_terms();
			update_option( 'trust_indicators_version', self::VERSION );
		}
	}

	/**
	 * Whether updates are needed for this plugin
	 *
	 * @return Boolean Whether or not updates are needed.
	 */
	public function need_updates() {
		// Try to figure out which plugin version's settings are saved
		$version = get_option( 'trust_indicators_version' );
		if ( false !== $version ) {
			$compare = version_compare( self::VERSION, $version );
			if ( 1 === $compare ) {
				return true;
			} else {
				return false;
			}
		}

		return true;
	}

	/**
	 * For newly-activated plugins, default to using the single-page form for editorial policies
	 *
	 * @since 1.2
	 * @link https://github.com/INN/trust-indicators/pull/77
	 * @return bool Whether the value was set.
	 */
	public function default_single_page_editorial_policies() {
		$version = get_option( 'trust_indicators_version' );
		if ( false !== $version ) {
			// this is not a fresh plugin install; do not change stuff.
			return false;
		}

		$setting = get_option( 'sitewide_policies_single_page' );
		if ( false !== $setting ) {
			// the setting has been set somehow; do not change stuff.
			return false;
		}

		return update_option( 'sitewide_policies_single_page', 1 );
	}

	/**
	 * Create default terms for type-of-work taxonomy
	 *
	 * This function wraps everything in term_exists calls so we don't
	 * accidentally create duplicate terms.
	 *
	 * This runs on the initialization hook 'trust_indicators_init',
	 * but also on 'admin_init' because activation hooks may not be
	 * reliable on the WordPress VIP platform, as described in
	 * https://github.com/INN/trust-indicators/issues/51
	 *
	 * @since  1.0
	 * @uses   $this->term_exists
	 */
	public function create_type_of_work_terms() {
		/**
		 * The initial list of terms, provided in plugin version 1.0
		 */
		// Begin "News" term and its children.
		if ( $this->term_exists( 'News', 'type-of-work' ) === null ) {
			$news = wp_insert_term(
				'News',
				'type-of-work',
				array(
					'description' => esc_html__( 'Based on facts, either observed and verified directly by the reporter, or reported and verified from knowledgeable sources.', 'trust-indicators' ),
				)
			);
			if ( ! is_wp_error( $news ) ) {
				add_term_meta( $news['term_id'], 'schema', 'ReportageNewsArticle' );
			}

			// Child terms of "News".
			if ( is_array( $news ) ) {
				if ( $this->term_exists( 'Backgrounder', 'type-of-work' ) === null ) {
					$backgrounder = wp_insert_term(
						'Backgrounder',
						'type-of-work',
						array(
							'parent' => $news['term_id'],
							'description' => esc_html__( 'Provides context, definition and detail on a specific topic.', 'trust-indicators' ),
						)
					);
					if ( ! is_wp_error( $backgrounder ) ) {
						add_term_meta( $backgrounder['term_id'], 'schema', 'BackgroundNewsArticle' );
					}
				} // end "Backgrounder".

				if ( $this->term_exists( 'Opinion', 'type-of-work' ) === null ) {
					$opinion = wp_insert_term(
						'Opinion',
						'type-of-work',
						array(
							'parent' => $news['term_id'],
							'description' => esc_html__( 'Advocates for ideas and draws conclusions based on the author/producer’s interpretation of facts and data.', 'trust-indicators' ),
						)
					);
					if ( ! is_wp_error( $opinion ) ) {
						add_term_meta( $opinion['term_id'], 'schema', 'OpinionNewsArticle' );
					}
				} // end "Opinion".

				if ( $this->term_exists( 'Analysis', 'type-of-work' ) === null ) {
					$analysis = wp_insert_term(
						'Analysis',
						'type-of-work',
						array(
							'parent' => $news['term_id'],
							'description' => esc_html__( 'Based on factual reporting, although it Incorporates the expertise of the author/producer and may offer interpretations and conclusions.', 'trust-indicators' ),
						)
					);
					if ( ! is_wp_error( $analysis ) ) {
						add_term_meta( $analysis['term_id'], 'schema', 'AnalysisNewsArticle' );
					}
				} // end "Analysis".

				if ( $this->term_exists( 'Fact Check', 'type-of-work' ) === null ) {
					$factcheck = wp_insert_term(
						'Fact Check',
						'type-of-work',
						array(
							'parent' => $news['term_id'],
							'description' => esc_html__( 'Checks a specific statement or set of statements asserted as fact.', 'trust-indicators' ),
						)
					);
					if ( ! is_wp_error( $factcheck ) ) {
						add_term_meta( $factcheck['term_id'], 'schema', 'ClaimReview' );
					}
				} // end "Fact Check".

				if ( $this->term_exists( 'Review', 'type-of-work' ) === null ) {
					$review = wp_insert_term(
						'Review',
						'type-of-work',
						array(
							'parent' => $news['term_id'],
							'description' => esc_html__( 'An assessment or critique of a service, product, or creative endeavor such as art, literature or a performance.', 'trust-indicators' ),
						)
					);
					if ( ! is_wp_error( $review ) ) {
						add_term_meta( $review['term_id'], 'schema', 'ReviewNewsArticle' );
					}
				} // end "Review".
			} // end child terms of "News".
		} // end "News".

		if ( $this->term_exists( 'Advertiser Content', 'type-of-work' ) === null ) {
			$advertiser_content = wp_insert_term(
				'Advertiser Content',
				'type-of-work'
			);
			if ( ! is_wp_error( $advertiser_content ) ) {
				add_term_meta( $advertiser_content['term_id'], 'schema', 'AdvertiserContentArticle' );
			}
		} // end "Advertiser Content".

		if ( $this->term_exists( 'Satire', 'type-of-work' ) === null ) {
			$satire = wp_insert_term(
				'Satire',
				'type-of-work',
				array(
					'description' => esc_html__( 'Humorous / satirical article that is not intended to be understood as factual.', 'trust-indicators' ),
				)
			);
			if ( ! is_wp_error( $satire ) ) {
				add_term_meta( $satire['term_id'], 'schema', 'SatiricalArticle' );
			}
		} // end "Satire".

		$version = get_option( 'trust_indicators_version' );

		/**
		 * New term added in 1.2
		 */
		if ( version_compare( $version, '1.2' ) < 0 ) {
			// don't mess with previously-defined $news variable.
			$news1 = get_term_by( 'name', 'News', 'type-of-work' );
			error_log(var_export( $news1, true));
			$help = wp_insert_term(
				'Help Us Report',
				'type-of-work',
				array(
					'parent' => $news1->term_id,
					'description' => esc_html__( 'Asks the public for input, insights, clarifications, anecdotes, documentation, etc., for reporting purposes. Callouts are a type of crowdsourcing in journalism.', 'trust-indicators' ),
				)
			);
			if ( ! is_wp_error( $help ) ) {
				add_term_meta( $help['term_id'], 'schema', 'AskPublicNewsArticle' );
			}
		}
	}

	/**
	 * To simplify logic, wrap the term_exists/wpcom_vip_term_exists check as its own function
	 *
	 * We need to do the term_exists/wpcom_vip_term_exists function_exists dance
	 * because of WordPress.com VIP's plugin standards.
	 *
	 * @see https://developer.wordpress.org/reference/functions/term_exists/
	 * @param int|string $term     The term to check. Accepts term ID, slug, or name.
	 * @param string     $taxonomy The taxonomy name to use.
	 * @param int        $parent   Optional. ID of parent term under which to confine the exists search.
	 * @return mixed Returns null if the term does not exist. Returns the term ID if no taxonomy is specified and the term ID exists. Returns an array of the term ID and the term taxonomy ID the taxonomy is specified and the pairing exists.
	 * @since 1.0
	 */
	private function term_exists( $term, $taxonomy = '', $parent = null ) {
		if ( function_exists( 'wpcom_vip_term_exists' ) ) {
			return wpcom_vip_term_exists( $term, $taxonomy, $parent );
		} else {
			//@codingStandardsIgnoreLine
			return term_exists( $term, $taxonomy, $parent );
		}
	}

	/**
	 * Register our widgets.
	 *
	 * @since 1.0
	 */
	public function register_widgets() {
		register_widget( 'Trust_Mark_Sitewide_Widget' );
		register_widget( 'Trust_Mark_Author_Widget' );
		register_widget( 'Trust_Mark_Article_Widget' );
		register_widget( 'Trust_Indicators_TrustProject_Widget' );
	}

	/**
	 * Activate the plugin.
	 *
	 * @since  1.0
	 */
	public function _activate() {
		wp_schedule_single_event( time() + 2, 'trust_indicators_init' );
	}

	/**
	 * Deactivate the plugin.
	 *
	 * @since  1.0
	 */
	public function _deactivate() {}
}

/**
 * Grab the Trust_Indicators object and return it.
 * Wrapper for Trust_Indicators::get_instance().
 *
 * @since  1.0
 * @return Trust_Indicators  Singleton instance of plugin class.
 */
function trust_indicators() {
	return Trust_Indicators::get_instance();
}

add_action( 'plugins_loaded', array( trust_indicators(), 'hooks' ) );

// Activation and deactivation.
register_activation_hook( __FILE__, array( trust_indicators(), '_activate' ) );
register_deactivation_hook( __FILE__, array( trust_indicators(), '_deactivate' ) );
