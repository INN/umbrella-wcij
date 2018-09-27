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
class Trust_Mark_Author_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'trust_indicators_author',
			'description' => 'Display trust indicators on your pages.',
		);
		parent::__construct( 'trust_indicators_author', 'Trust Indicators: Author', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		if ( ! ( is_single() || is_author() ) ) {
			return;
		}

		global $post;
		$user_id = $post->post_author;
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		$author = get_userdata( $user_id );

		if ( function_exists( 'get_user_attribute' ) ) {
			$author_bio = get_user_attribute( $user_id, 'trust_indicators_description' );
		} else {
			//@codingStandardsIgnoreLine
			$author_bio = get_user_meta( $user_id, 'description', true );
		}

		if ( ! empty( $author_bio ) && ! is_author() ) {
			echo '<h3>' . esc_html( $author->display_name ) . '</h3>';
			echo wp_kses_post( wpautop( $author_bio ) );
		}

		// Author Metadata
		$settings_fields = Trust_Indicators_User_Settings::get_fields();
		foreach ( $settings_fields as $field_key => $field ) {

			// Skip some fields if we're not on an author page
			if ( ! is_author() && in_array( $field_key, array( 'location', 'languages_spoken', 'twitter', 'linkedin' ), true ) ) {
				continue;
			}

			if ( function_exists( 'get_user_attribute' ) ) {
				$field_data = get_user_attribute( $user_id, 'trust_indicators_' . $field_key );
			} else {
				//@codingStandardsIgnoreLine
				$field_data = get_user_meta( $user_id, $field_key, true );
			}

			switch ( $field_key ) {
				case 'public_contact_info_tel':
					$field_data = sprintf( '<a href="tel:%1$s">%1$s</a>', $field_data );
					break;
				case 'public_contact_info_email':
					$field_data = sprintf( '<a href="mailto:%1$s">%1$s</a>', $field_data );
					break;
				case 'twitter':
				case 'linkedin':
					$field_data = sprintf( '<a href="%1$s">%1$s</a>', $field_data );
					break;
				case 'location_expertise':
					$value_array = array_map( 'trim', explode( PHP_EOL, $field_data ) );
					$places = "";
					foreach ( $value_array as $value_item ) {
						$places .= explode(",", $value_item)[0] . '<br />';
					}
					$field_data = sprintf( '%1$s', $places );
					break;
				case 'location':
					$value_array = array_map( 'trim', explode( PHP_EOL, $field_data ) );
					$places = "";
					foreach ( $value_array as $value_item ) {
						$places .= explode(",", $value_item)[0] . '<br />';
					}
					$field_data = sprintf( '%1$s', $places );
					break;
			}

			if ( ! empty ( $field_data ) ) {
				echo '<h4>' . esc_html( $field['label'] ) . ':</h4>';
				echo wp_kses_post( wpautop( $field_data ) );
			}
		}

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'About The Author', 'tust-indicators' );
		}
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
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
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';

        return $instance;
	}
}
