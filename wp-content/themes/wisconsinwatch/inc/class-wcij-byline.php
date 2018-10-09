<?php
/**
 * Extends Largo_Byline to implement Presspoint avatar support
 *
 * @link https://github.com/INN/umbrella-invwest/blob/master/wp-content/themes/invw/inc/post-tags.php A different extension
 */
include_once( get_template_directory() . '/inc/byline_class.php' );
class WCIJ_Byline extends Largo_Byline {
	public $author;
	/**
	 * On single posts, output the avatar for the author object
	 * This supports both Largo_Byline and Largo_CoAuthors_Byline
	 */
	function avatar() {
		// only do avatars if it's a single post
		if ( ! is_single() ) {
			$output = '';
		} else {
			$author_email = get_the_author_meta( 'email', $this->author_id );
			$this->author = get_user_by( 'email', $author_email );

			if ( $this->author->type == 'guest-author' && get_the_post_thumbnail( $this->author->ID ) ) {
				$output = get_the_post_thumbnail( $this->author->ID, array( 60,60 ) );
				$output = str_replace( 'attachment-32x32', 'avatar avatar-32 photo', $output );
				$output = str_replace( 'wp-post-image', '', $output );
			} else if ( largo_has_avatar( $author_email ) || $this->author->paupress_pp_avatar ) {
				$output = get_avatar(
					$author_email,
					32,
					'',
					$this->author->display_name,
					array(
						'class' => 'avatar avatar-32 photo',
					)
				);
			}
			$output .= ' '; // to reduce run-together bylines
		}
		echo $output;
	}
}
