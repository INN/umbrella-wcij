<?php
/**
 * Functions replacing or shimming largo's inc/post-tags.php
 */

/**
 * Patch largo_byline to use WCIJ_Byline instead of Largo_Byline
 * @link https://github.com/INN/umbrella-wcij/pull/7
 * @link https://github.com/INN/largo/issues/1471
 * @since Largo 0.5.5.4
 */
	function largo_byline( $echo = true, $exclude_date = false, $post = null ) {

		// Get the post ID
		if (!empty($post)) {
			if (is_object($post))
				$post_id = $post->ID;
			else if (is_numeric($post))
				$post_id = $post;
		} else {
			$post_id = get_the_ID();
		}

		// Set us up the options
		// This is an array of things to allow us to easily add options in the future
		$options = array(
			'post_id' => $post_id,
			'values' => get_post_custom( $post_id ),
			'exclude_date' => $exclude_date,
		);

		if ( isset( $options['values']['largo_byline_text'] ) && !empty( $options['values']['largo_byline_text'] ) ) {
			// Temporary placeholder for largo custom byline option
			$byline = new Largo_Custom_Byline( $options );
		} else if ( function_exists( 'get_coauthors' ) ) {
			// If Co-Authors Plus is enabled and there is not a custom byline
			$byline = new Largo_CoAuthors_Byline( $options );
		} else {
			// no custom byline, no coauthors: let's do the default
			$byline = new WCIJ_Byline( $options );
		}

		/**
		 * Filter the largo_byline output text to allow adding items at the beginning or the end of the text.
		 *
		 * @since 0.5.4
		 * @param string $partial The HTML of the output of largo_byline(), before the edit link is added.
		 * @link https://github.com/INN/Largo/issues/1070
		 */
		$byline = apply_filters( 'largo_byline', $byline );

		if ( $echo ) {
			echo $byline;
		}
		return $byline;
	}
