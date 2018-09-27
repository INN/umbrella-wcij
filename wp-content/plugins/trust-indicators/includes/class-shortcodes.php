<?php
/**
 * Trust Indicators Shortcodes.
 *
 * @since   1.0
 * @package Trust_Indicators
 */

/**
 * Trust Indicators Shortcodes class.
 *
 * @since 1.0
 */
class Trust_Mark_Shortcodes {
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
		add_action( 'init', array( $this, 'init' ), 10, 0 );
	}

	public function init() {
		add_shortcode( 'trust-indicators', array( $this, 'create_shortcode' ) );
	}

	// [trust-indicatiors type="author" fields=""]
	public function create_shortcode( $atts ) {

		$output = '';

		$attributes = shortcode_atts(
			array(
				'type'   => '',
				'fields' => '',
			),
			$atts,
			'trust-indicatiors'
		);

		// Shortcode only works on post or author pages
		if ( ! ( is_single() || is_author() ) ) {
			return;
		}

		// Validate type attribute
		if ( ! in_array( $attributes['type'], array( 'author', 'post', 'sitewide_policies', 'actionable_feedback', 'bylines', 'newsroom_contact_info' ), true ) ) {
			return;
		}

		// Prepare fields attribute
		if ( ! empty( $attributes['fields'] ) ) {
			$attributes['fields'] = explode( ',', $attributes['fields'] );
		} else {
			$attributes['fields'] = array();
		}

		global $post;
		$user_id = $post->post_author;

		if ( 'author' === $attributes['type'] ) {

			$settings_fields = Trust_Indicators_User_Settings::get_fields();
			foreach ( $settings_fields as $field_key => $field ) {

				// Skip some fields if we're not on an author page
				if ( ! is_author() && in_array( $field_key, array( 'location', 'languages_spoken', 'twitter', 'linkedin' ), true ) ) {
					continue;
				}

				// If the fields attribute is set, skip over anything that doesn't match.
				if ( 0 < count( $attributes['fields'] ) ) {
					if ( ! in_array( $field_key, $attributes['fields'], true ) ) {
						continue;
					}
				}

				if ( function_exists( 'get_user_attribute' ) ) {
					$field_data = get_user_attribute( $user_id, 'trust_indicators_' . $field_key );
				} else {
					//@codingStandardsIgnoreLine
					$field_data = get_user_meta( $user_id, $field_key, true );
				}

				if ( ! empty( $field_data ) ) {
					$output .= '<h4>' . $field['label'] . ':</h4>';
					$output .= wpautop( $field_data );
				}
			}

		} elseif ( 'post' === $attributes['type'] ) {

			// Type of Work.
			$type_of_work_terms = wp_get_post_terms( $post->ID, 'type-of-work' );

			if ( in_array( 'type_of_work', $attributes['fields'], true ) && is_array( $type_of_work_terms ) && ! empty( $type_of_work_terms ) ) {
				foreach ( $type_of_work_terms as $type_of_work ) {
					$output .= '<div class="tip-tow-label">' . $type_of_work->name . '<span class="tip-tow-desc">' . $type_of_work->description . '</span></div>';
				}
			}

			// Article Metadata.
			$settings_fields = Trust_Indicators_Article_Settings::get_fields();

			foreach ( $settings_fields as $meta_box ) {
				foreach ( $meta_box['fields'] as $field_key => $field ) {

					// If the fields attribute is set, skip over anything that doesn't match.
					if ( 0 < count( $attributes['fields'] ) ) {
						if ( ! in_array( $field_key, $attributes['fields'], true ) ) {
							continue;
						}
					}

					$field_data = get_post_meta( $post->ID, $field_key, true );
					if ( ! empty( $field_data ) ) {
						$output .= '<h4>' . $field['label'] . ':</h4>';
					}
					$output .= wpautop( get_post_meta( $post->ID, $field_key, true ) );
				}
			}

		} elseif ( in_array( $attributes['type'], array( 'sitewide_policies', 'actionable_feedback', 'bylines', 'newsroom_contact_info' ), true ) ) {

			// Sitewide Metadata
			$settings_fields = Trust_Indicators_Settings::get_fields();
			foreach ( $settings_fields as $section_key => $section ) {
				if ( $attributes['type'] !== $section_key ) {
					continue;
				}

				if ( 'actionable_feedback' === $section_key ) {
					// Only display the policy from the actionable feedback section here.
					$value = get_option( 'actionable_feedback_policy' );
					if ( function_exists( 'wpcom_vip_url_to_postid' ) ) {
						$post_id = wpcom_vip_url_to_postid( $value );
					} else {
						//@codingStandardsIgnoreLine
						$post_id = url_to_postid( $value );
					}

					if ( $post_id && $post_id !== $post->ID ) {
						$content = get_post_field( 'post_content', $post_id );

						// Remove trust-indicator shortcodes
						$content = preg_replace( '/(\[trust-indicators[^.\]]*])/', '', $content );

						if ( ! empty( $content ) ) {
							$output .= sprintf( '<h4 id="%s">%s</h4>', esc_attr( 'actionable_feedback_policy' ), $section['fields']['actionable_feedback_policy']['label'] );
							$output .= apply_filters( 'the_content', $content ) . '<hr />';
						}
					} elseif ( ! empty( $value ) ) {
						$output .= sprintf( '<h4 id="%s">%s</h4>', esc_attr( $section_key ), $section['fields']['actionable_feedback_policy']['label'] );
						$output .= $value;
					}

				} else {
					$output .= sprintf( '<h3 id="%s">%s</h3>', esc_attr( $section_key ), $section['label'] );
					foreach ( $section['fields'] as $key => $field ) {
						$value = get_option( $key );
						$post_id = ( $value );

						if ( $post_id && $post_id !== $post->ID ) {
							$content = get_post_field( 'post_content', $post_id );

							// Remove trust-indicator shortcodes
							$content = preg_replace( '/(\[trust-indicators[^.\]]*])/', '', $content );

							if ( ! empty( $content ) ) {
								$output .= sprintf( '<h4 id="%s">%s</h4>', esc_attr( $key ), $field['label'] );
								$output .= apply_filters( 'the_content', $content ) . '<hr />';
							}
						} elseif ( ! empty( $value ) ) {
							$output .= sprintf( '<h4 id="%s">%s</h4>', esc_attr( $key ), $field['label'] );
							$output .= $value;
						}
					}
				}
			}
		}

		return $output;
	}
}
