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
class Trust_Mark_Sitewide_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'trust_indicators_sitewide',
			'description' => 'Display trust indicators on your pages.',
		);
		parent::__construct( 'trust_indicators_sitewide', 'Trust Indicators: Editorial Policies', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		wp_enqueue_style( 'trust-indicators-css', plugins_url( 'assets/widget.css', dirname( __FILE__ ) ), array( 'dashicons' ), '1.2.0' );
		wp_enqueue_script( 'trust-indicators-js', plugins_url( 'assets/widget.js', dirname( __FILE__ ) ), array( 'jquery' ), Trust_Indicators::VERSION, false );
		wp_enqueue_style( 'trust-indicators-css', plugins_url( 'assets/widget.css', dirname( __FILE__ ) ), array(), Trust_Indicators::VERSION );

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		echo sprintf( '<p>%s</p>', esc_html__( $instance['text'], 'tust-indicators' ) );

		echo sprintf( '<button id="trust-indicators-button">%s</button>', esc_html__( $instance['button'], 'tust-indicators' ) );

		echo '<div id="trust-indicators-modal" class="trust-indicators-overlay" style="display:none;">';

		$this->modal_window( $title );

		echo '</div>'; //#trust-indicators-modal

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Editorial Policies', 'trust-indicators' );
		$text = ! empty( $instance['text'] ) ? $instance['text'] : esc_html__( '', 'tust-indicators' );
		$button = ! empty( $instance['button'] ) ? $instance['button'] : esc_html__( 'Read Our Policies', 'tust-indicators' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" type="text" cols="30" rows="10"><?php echo esc_attr( $text ); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'button' ) ); ?>"><?php esc_html_e( 'Button Text:' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button' ) ); ?>" type="text" value="<?php echo esc_attr( $button ); ?>" />
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['text'] = ( ! empty( $new_instance['text'] ) ) ? esc_html( $new_instance['text'] ) : '';
		$instance['button'] = ( ! empty( $new_instance['button'] ) ) ? esc_html( $new_instance['button'] ) : esc_html__( 'Read Our Policies', 'tust-indicators' );

        return $instance;
	}

	public function modal_window( $title ) {

		$output = sprintf( '<h3>%s</h3><br />', $title );
		$index = array();

		// Sitewide Metadata
		$settings_fields = Trust_Indicators_Settings::get_fields();
		$sitewide_policies_single_page = get_option( 'sitewide_policies_single_page' );

		// Create index from sitewide policies.
		foreach ( $settings_fields['sitewide_policies']['fields'] as $key => $field ) {

			// Skip single page policies question.
			if ( 'sitewide_policies_single_page' === $key ) {
				continue;
			}

			$value = get_option( $key );
			if ( $value ) {
				$index[] = sprintf( '<li class="enabled"><a href="%s" target="_blank">%s</a></li>', $value, $field['label'] );
			} else {
				$index[] = sprintf( '<li class="disabled">%s</li>', $field['label'] );
			}
		}
		$output .= sprintf( '<ul>%s</ul>', implode( '', $index ) );

		// Build content display.
		foreach ( $settings_fields as $section_key => $section ) {

			if ( 'newsroom_contact_info' === $section_key ) {
				continue;
			} elseif ( 'actionable_feedback' === $section_key ) {
				// Only display the policy from the actionable feedback section here.
				$value = get_option( 'actionable_feedback_policy' );
				if ( function_exists( 'wpcom_vip_url_to_postid' ) ) {
					$post_id = wpcom_vip_url_to_postid( $value );
				} else {
					//@codingStandardsIgnoreLine
					$post_id = url_to_postid( $value );
				}

				if ( ! $post_id ) {
					continue;
				}

				$content = get_post_field( 'post_content', $post_id );

				// Remove trust-indicator shortcodes
				$content = preg_replace( '/(\[trust-indicators[^.\]]*])/', '', $content );

				if ( empty( $content ) ) {
					continue;
				}

				$output .= sprintf(
					'<hr /><h4 id="%s">%s</h4>%s',
					esc_attr( 'actionable_feedback_policy' ),
					$section['label'],
					apply_filters( 'the_content', $content )
				);

			} else {

				foreach ( $section['fields'] as $key => $field ) {

					// If single page policies is checked, only output the publishing_principles field.
					if (
						$sitewide_policies_single_page
						&& 'sitewide_policies' === $section_key
						&& 'publishing_principles' !== $key
					) {
						continue;
					}

					if (
						'site_uses_bylines' === $key
					) {
						continue;
					}

					$value = get_option( $key );

					if ( empty ( $value ) ) {
						// There's no sense outputting markup if there isn't a link.
						continue;
					}

					$maybe_url = wp_http_validate_url( $value );

					if ( ! empty( $maybe_url ) ) {
						// it's a URL
						if ( function_exists( 'wpcom_vip_url_to_postid' ) ) {
							$post_id = wpcom_vip_url_to_postid( $value );
						} else {
							//@codingStandardsIgnoreLine
							$post_id = url_to_postid( $value );
						}

						if ( ! empty( $post_id ) ) {
							$content = get_post_field( 'post_content', $post_id );
							$content = preg_replace( '/(\[trust-indicators[^.\]]*])/', '', $content );
						} else {
							$content = sprintf(
								__( 'This policy can be found <a href="%1$s" target="_blank">on this page</a>.' , 'trust-indicators' ),
								esc_attr( $value )
							);
						}

						if ( empty( $content ) ) {
							continue;
						}

						$output .= sprintf(
							'<hr /><h4 id="%1$s">%2$s</h4>%3$s',
							esc_attr( $key ),
							$field['label'],
							apply_filters( 'the_content', $content )
						);
					} else {
						// it's not a URL
						$output .= sprintf(
							'<hr /><h4 id="%1$s">%2$s</h4>%3$s',
							esc_attr( $key ),
							$field['label'],
							apply_filters( 'the_content', wp_kses_post( $value ) )
						);
					}


					// cleanup
					unset( $content );
					unset( $post_id );
					unset( $value );
				}
			}
		}

		// $output has been constructed from escaped strings. https://github.com/INN/trust-indicators/pull/50#issuecomment-395926120
		//@codingStandardsIgnoreLine
		echo sprintf( '<div id="trust-indicators"><div class="trust-indicators-modal-content">%s</div><div class="trust-indicators-close">X</div></div>', $output );
	}
}
