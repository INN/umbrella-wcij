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

class Trust_Indicators_TrustProject_Widget extends WP_Widget {
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'trust_indicators_trustproject',
			'description' => 'Display information about the Trust Project on your pages.',
		);
		parent::__construct( 'trust_indicators_trustproject', 'Trust Project Info', $widget_ops );
	}

	/**
	* Outputs trust project logo svg
	*/
	public function trust_project_mark() {
		 echo '<svg class="icon icon-trust-project"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#trust-project"></use></svg>';
	}

	/**
	* Trust project logo svg file
	*/
	public function trust_mark_svg() {
		echo '<img class="icon icon-trust-project" src="' . esc_url( plugins_url( '/Trust-Logo-Stacked.svg', __FILE__ ) ) . '" > ';
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		wp_enqueue_style( 'trust-project-partner-css', plugins_url( 'assets/widget.css', dirname( __FILE__ ) ), array(), '1.2.0' );

		// Article Metadata
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		echo '<div class="trust-indicators-mark">';
			echo '<a href="http://thetrustproject.org/" target="_blank">';
				echo '<img src="' . esc_url( plugins_url( '/Trust-Logo-Stacked.svg', __FILE__ ) ) . '" style="display:block;"> ';
				echo '<div class="trust-indicators-title-wrap">';
				echo '</div>';
			echo '</a>';
		echo '</div>';

		echo sprintf(
			'<p>%s</p>',
			esc_html__( 'The Trust Project is a collaboration among news organizations around the world. Its goal is to create strategies that fulfill journalism’s basic pledge: to serve society with a truthful, intelligent and comprehensive account of ideas and events.' , 'trust-indicators' )
		);
		echo sprintf(
			'<a href="http://thetrustproject.org/" target="_blank">%s</a>',
			esc_html__( 'Learn more.', 'trust-indicators' )
		);

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
			$title = __( 'About The Trust Project', 'text_domain' );
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

?>
