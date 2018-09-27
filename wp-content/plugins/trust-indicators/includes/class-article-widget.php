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
class Trust_Mark_Article_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'trust_indicators_article',
			'description' => 'Display trust indicators on your pages.',
		);
		parent::__construct( 'trust_indicators_article', 'Trust Indicators: Article', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param arr $args Widget arguments.
	 * @param arr $instance Instance of the widget.
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget.
		if ( ! is_single() ) {
			return;
		}

		global $post;

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		// Type of Work.
		$type_of_work_terms = wp_get_post_terms( $post->ID, 'type-of-work' );
		$type = array();
		$type_descriptions = array();

		if ( is_array( $type_of_work_terms ) && ! empty( $type_of_work_terms ) ) {
			foreach ( $type_of_work_terms as $type_of_work ) {
				$type[] = $type_of_work->name;
				$type_descriptions[] = wpautop( $type_of_work->name . ': ' . $type_of_work->description );
			}
			echo '<p><strong>' . esc_html__( 'Type', 'trust_indicators' ) . '</strong>: ' . wp_kses_post( implode( ', ', $type ) ) . '</p>';
			echo wp_kses_post( implode( PHP_EOL, $type_descriptions ) );
		}

		// Article Metadata.
		$settings_fields = Trust_Indicators_Article_Settings::get_fields();

		foreach ( $settings_fields as $meta_box ) {
			foreach ( $meta_box['fields'] as $field_key => $field ) {
				$field_data = get_post_meta( $post->ID, $field_key, true );
				if ( ! empty ( $field_data ) ) {
					echo '<h4>' . esc_html( $field['label'] ) . ':</h4>';
				}
				echo wp_kses_post( wpautop( get_post_meta( $post->ID, $field_key, true ) ) );
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
			$title = __( 'Behind The Story', 'trust_indicators' );
		}
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'trust_indicators' ); ?></label>
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
